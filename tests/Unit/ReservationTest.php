<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Concert;
use Tests\TestCase;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function reserving_tickets_for_an_order()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test **/
    public function it_can_get_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reserved = new Reservation($tickets, 'john@example.com');

        $this->assertEquals($tickets, $reserved->tickets());
    }

    /** @test **/
    public function can_complete_an_order()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1300]);
        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);
        $reservation = new Reservation($tickets, 'jane@example.com');
        $paymentGateway = new FakePaymentGateway();

        $order = $reservation->complete($paymentGateway, $paymentGateway->getvalidTestToken());

        $this->assertEquals(3900, $reservation->totalCost());
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3900, $paymentGateway->totalCharges());
    }

    /** @test **/
    public function it_can_get_email()
    {
        $tickets = collect();

        $reserved = new Reservation($tickets, 'john@example.com');

        $this->assertEquals($tickets, $reserved->tickets());
        $this->assertEquals('john@example.com', $reserved->email());
    }


    /** @test **/
    public function tickets_are_released_when_the_reservation_is_cancelled()
    {
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class)
        ]);
        $reservation = new Reservation($tickets, 'john@example.com');

        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }
}
