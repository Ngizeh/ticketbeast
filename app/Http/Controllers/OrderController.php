<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show($confirmation_order)
    {
    	$order = Order::findByConfirmationOrder($confirmation_order);

    	return view('order.show', compact('order'));
    }

}
