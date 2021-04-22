<?php

namespace Tests\Unit;

use App\Ticket;
use App\Concert;
use Tests\TestCase;
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
    public function ticket_can_reserved()
    {
        $ticket = factory(Ticket::class)->create();

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }


    /** @test **/
    public function can_be_released()
    {
        $ticket = factory(Ticket::class)->states('reserved')->create();

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_id);
    }
}
