<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Facades\App\OrderConfirmationNumber;

class Order extends Model
{
    protected $guarded = [];

    public static function ticketsFor($tickets, $email, $amount)
    {
        $orders = self::create([
            'confirmation_order' => OrderConfirmationNumber::generate(),
            'email' => $email,
            'amount' => $amount
        ]);

        foreach ($tickets as $ticket) {
            $orders->tickets()->save($ticket);
        }
        return $orders;
    }

    public static function findByConfirmationOrder($order)
    {
    	return static::where('confirmation_order', $order)->firstOrFail();
    }


    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

    public function toArray()
    {
        return [
            'confirmation_order' => $this->confirmation_order,
            'email' => $this->email,
            'amount' => $this->amount,
            'ticket_quantity' => $this->ticketQuantity()
        ];
    }




}
