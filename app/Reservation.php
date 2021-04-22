<?php

namespace App;

use App\Order;

class Reservation 
{
    protected $tickets;
    protected $email;

    public function __construct($tickets,$email)
    {
       $this->tickets = $tickets;
       $this->email = $email;
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }
    
    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function complete($paymenGateway, $token)
    {
        $paymenGateway->charge($this->totalCost(), $token);

        return Order::ticketsFor($this->tickets(), $this->email(), $this->totalCost());
    }
    

    public function cancel()
    {
        foreach($this->tickets as $ticket){
            $ticket->release();
        }
    }
    
    
}
