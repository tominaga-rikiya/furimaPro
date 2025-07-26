<?php

namespace App\Mail;

use App\Models\SoldItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $soldItem;
    public $buyer;

    public function __construct(SoldItem $soldItem, User $buyer)
    {
        $this->soldItem = $soldItem;
        $this->buyer = $buyer;
    }

    public function build()
    {
        $itemName = $this->soldItem->item->name ?? '商品';

        return $this->subject('取引完了のお知らせ - ' . $itemName)
            ->view('emails.completed')
            ->with([
                'soldItem' => $this->soldItem,
                'buyer' => $this->buyer,
                'item' => $this->soldItem->item,
                'seller' => $this->soldItem->item->user,
                'completedAt' => $this->soldItem->completed_at ?? now(),
            ]);
    }
}
