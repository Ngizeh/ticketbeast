<?php

namespace App;

use App\Exceptions\NotEnoughTickectsRemaining;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function hasOrdersFor($customerEmail)
    {
        return $this->orders()->whereEmail($customerEmail)->count() > 0;
    }

    public function ordersFor($customerEmail)
    {
        return $this->orders()->whereEmail($customerEmail)->get();
    }


    public function orderTickets($email, $ticketQuantity)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();

        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTickectsRemaining;
        }

        $orders = $this->orders()->create(compact('email'));

        foreach ($tickets as $ticket) {
            $orders->tickets()->save($ticket);
        }
        return $orders;
    }

    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
        return $this;
    }

    public function ticketsRemaining()
    {
        return  $this->tickets()->available()->count();
    }

    public function getformattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getformattedTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getformattedPriceAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }
}
