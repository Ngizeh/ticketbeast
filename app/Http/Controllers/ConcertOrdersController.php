<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Billing\PaymentFailedException;
use App\Exceptions\NotEnoughTickectsRemaining;

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
            $order = $concert->orderTickets(request('email'), request('ticket_quantity'));
            $this->paymentGateway->charge($concert->ticket_price *  request('ticket_quantity'), request('payment_token'));
            return response()->json([], 201);
        } catch (NotEnoughTickectsRemaining $e) {
            return response()->json([], 422);
        } catch (PaymentFailedException $e) {
            $order->cancel();
            return response()->json([], 422);
        }
    }
}
