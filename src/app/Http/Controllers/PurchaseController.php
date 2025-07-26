<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;
use App\Models\Profile;
use App\Mail\PurchaseNotificationMail;
use Stripe\StripeClient;

class PurchaseController extends Controller
{
    public function index($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = User::findOrFail(Auth::id());

        return view('purchase', compact('item', 'user'));
    }

    public function purchase($item_id, Request $request)
    {
        $item = Item::findOrFail($item_id);
        $stripe = new StripeClient(config('stripe.stripe_secret_key'));

        $purchaseData = [
            'user_id' => Auth::id(),
            'amount' => $item->price,
            'sending_postcode' => $request->destination_postcode,
            'sending_address' => urlencode($request->destination_address),
            'sending_building' => $request->destination_building ? urlencode($request->destination_building) : null
        ];

        $successUrl = url("/purchase/{$item_id}/success?" . http_build_query($purchaseData));

        $checkout_session = $stripe->checkout->sessions->create([
            'payment_method_types' => [$request->payment_method],
            'payment_method_options' => [
                'konbini' => ['expires_after_days' => 7],
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $item->name],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $successUrl,
        ]);

        return redirect($checkout_session->url);
    }

    public function success($item_id, Request $request)
    {
        $requiredParams = ['user_id', 'amount', 'sending_postcode', 'sending_address'];

        foreach ($requiredParams as $param) {
            if (!$request->has($param)) {
                throw new Exception("必須パラメータが不足: {$param}");
            }
        }

        SoldItem::create([
            'user_id' => $request->user_id,
            'item_id' => $item_id,
            'sending_postcode' => $request->sending_postcode,
            'sending_address' => urldecode($request->sending_address),
            'sending_building' => $request->sending_building ? urldecode($request->sending_building) : null,
        ]);

        try {
            $item = Item::findOrFail($item_id);
            $buyer = User::findOrFail($request->user_id);
            Mail::to($item->user->email)->send(new PurchaseNotificationMail($item, $buyer));
        } catch (Exception $e) {
            Log::error('購入通知メール送信失敗: ' . $e->getMessage());
        }

        return redirect('/')->with('flashSuccess', '決済が完了しました！');
    }

    public function address($item_id)
    {
        $user = User::findOrFail(Auth::id());
        return view('address', compact('user', 'item_id'));
    }

    public function updateAddress(AddressRequest $request)
    {
        Profile::where('user_id', Auth::id())->update([
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building
        ]);

        return redirect()->route('purchase.index', ['item_id' => $request->item_id]);
    }
}
