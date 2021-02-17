<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public static function ticketsFor($tickets, $email, $amount)
    {
        $orders = self::create([
            'email' => $email,
            'amount' => $amount
        ]);

        foreach ($tickets as $ticket) {
            $orders->tickets()->save($ticket);
        }
        return $orders;
    }
    

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

    public function cancel()
    {
        foreach($this->tickets as $ticket){
            $ticket->release();
        }

        $this->delete();
    }

    public function toArray()
    {
        return [
            'email' => $this->email,
            'amount' => $this->amount,
            'ticket_quantity' => $this->ticketQuantity()
        ];
    }
    
    
    

}
