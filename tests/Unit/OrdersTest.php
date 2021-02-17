<?php

namespace Tests\Unit;

use App\Order;
use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    public function converting_the_order_into_array()
    {        
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(5);
        $order = $concert->orderTickets('jane@example.com', 5);

        $results = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'amount' => 6000,
            'ticket_quantity' =>  5
        ], $results);
    }


    /** @test **/
    public function order_can_be_cancelled()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);
        $order = $concert->orderTickets('jane@example.com', 4);
        $this->assertEquals(1, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(5, $concert->ticketsRemaining());
        $this->assertNull($order->fresh());
    }
}
