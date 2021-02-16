<?php

namespace Tests\Unit;

use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function check_for_available_tickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);

        $concert->orderTickets('john@example.com', 3);

        $this->assertEquals(2, $concert->tickets()->available()->count());
    }


        /** @test **/
        public function can_be_released()
        {
            $concert = factory(Concert::class)->create()->addTickets(1);
            $order = $concert->orderTickets('jane@example.com', 1);
            $ticket = $order->tickets()->first();
            $this->assertEquals($order->id, $ticket->order_id);
    
            $ticket->release();
    
            $this->assertNull($ticket->fresh()->order_id);
        }
    
}
