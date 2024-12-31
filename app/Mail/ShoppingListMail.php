<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShoppingListMail extends Mailable
{
    use SerializesModels;

    public $uncheckedItems;
    public $checkedItems;
    public $user;

    public function __construct($uncheckedItems, $checkedItems, $user)
    {
        $this->uncheckedItems = $uncheckedItems;
        $this->checkedItems = $checkedItems;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Your Shopping List')
            ->view('emails.shopping_list');
    }
}
