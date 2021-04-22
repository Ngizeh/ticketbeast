<?php

namespace App;

use App\Exceptions\NotEnoughTickectsRemaining;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    protected $casts = ['price' => 'integer'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
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

    public function findTickets($quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();

        if ($tickets->count() < $quantity) {
            throw new NotEnoughTickectsRemaining;
        }

        return $tickets;
    }


    public function orderTickets($email, $ticketQuantity)
    {
        $tickets = $this->findTickets($ticketQuantity);

        return $this->createOrders($email, $tickets);
    }

    public function createOrders($email, $tickets)
    {
        return Order::ticketsFor($tickets, $email, $tickets->sum('price'));
    }

    public function reserveTickets($ticketQuantity, $email)
    {
        $tickets = $this->findTickets($ticketQuantity)->each(fn($ticket) => $ticket->reserve());

        return new Reservation($tickets, $email);
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
