<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function create_order_from_the_tickets_email_and_amount()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::ticketsFor($concert->findTickets(3,), 'jane@example.com', 3600);

        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals('jane@example.com', $order->email);
    }

    /** @test **/
    public function retrieving_orders_by_confirmation_orders()
    {
        $order = factory(Order::class)->create([
            'confirmation_order' => 'ORDERCONFIRMATION12345'
        ]);

        $foundOrder = Order::findByConfirmationOrder('ORDERCONFIRMATION12345');

        $this->assertEquals($foundOrder->id, $order->id);
    }

    /** @test **/
    public function retrieving_orders_by_confirmation_orders_that_does_not_exist_throws_an_error()
    {
        $order = factory(Order::class)->create([
            'confirmation_order' => 'ORDERCONFIRMATION12345'
        ]);

        $this->expectException(ModelNotFoundException::class);

        $foundOrder = Order::findByConfirmationOrder('ORDERTHATDOESNOTEXIST12345');

        $this->assertFalse($foundOrder->id, $order->id);
    }


    /** @test **/
    public function converting_the_order_into_array()
    {
        $order = factory(Order::class)->create([
            'confirmation_order' => 'ORDERCONFIRMATION12345',
            'email' => 'jane@example.com',
            'amount' => 6000
        ]);
        
        $order->tickets()->saveMany(factory(Ticket::class, 5)->create());

        $results = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'amount' => 6000,
            'ticket_quantity' =>  5,
            'confirmation_order' => 'ORDERCONFIRMATION12345',
        ], $results);
    }
}
