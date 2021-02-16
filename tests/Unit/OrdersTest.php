<?php

namespace Tests\Unit;

use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

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
