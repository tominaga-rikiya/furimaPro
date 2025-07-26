<?php

namespace App\Mail;

use App\Models\Item;
use App\Models\User;
use Illuminate\Mail\Mailable;

class PurchaseNotificationMail extends Mailable
{
    public $item;
    public $buyer;

    public function __construct(Item $item, User $buyer)
    {
        $this->item = $item;
        $this->buyer = $buyer;
    }

    public function build()
    {
        return $this->subject('商品が購入されました - ' . $this->item->name)
            ->view('emails.purchase-notification');
    }
}
