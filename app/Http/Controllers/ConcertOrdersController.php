<?php

namespace App\Http\Controllers;

use App\Order;
use App\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Billing\PaymentFailedException;
use App\Exceptions\NotEnoughTickectsRemaining;
use App\Reservation;

class ConcertOrdersController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }
    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        request()->validate([
            'email' => 'required|email',
            'ticket_quantity' => 'required|integer|min:1',
            'payment_token' => 'required'
        ]);

        try {
            $tickets = $concert->findTickets(request('ticket_quantity'));
            $reservation = new Reservation($tickets);
            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));
            $order = Order::ticketsFor($tickets, request('email'), $reservation->totalCost());
            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            // $order->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTickectsRemaining $e) {
            return response()->json([], 422);
        }
    }
}
