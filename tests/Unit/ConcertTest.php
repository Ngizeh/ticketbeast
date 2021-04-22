<?php

namespace Tests\Unit;

use App\Order;
use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Collection;
use App\Exceptions\NotEnoughTickectsRemaining;
use App\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConcertTest extends TestCase
{
	use RefreshDatabase;

	/** @test **/
	public function can_format_the_date()
	{
		$concert = factory(Concert::class)->make([
			'date' => Carbon::parse('2021-02-13 8:00pm')
		]);

		$this->assertEquals('February 13, 2021', $concert->formatted_date);
	}

	/** @test **/
	public function can_format_the_opening_time()
	{
		$concert = factory(Concert::class)->make([
			'date' => Carbon::parse('2021-02-13 08:00pm')
		]);

		$this->assertEquals('8:00pm', $concert->formatted_time);
	}

	/** @test **/
	public function can_format_the_price()
	{
		$concert = factory(Concert::class)->make([
			'ticket_price' => 6050
		]);

		$this->assertEquals(60.50, $concert->formatted_price);
	}

	/** @test **/
	public function concertes_with_published_at_data_are_published()
	{
		$publishedconcertA = factory(Concert::class)->create(['published_at' => Carbon::parse('-2 weeks')]);
		$publishedconcertB = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 weeks')]);
		$unpublishedconcert = factory(Concert::class)->create(['published_at' => null]);

		$publishedconcerts = Concert::published()->get();

		$this->assertTrue($publishedconcerts->contains($publishedconcertA));

		$this->assertTrue($publishedconcerts->contains($publishedconcertB));

		$this->assertFalse($publishedconcerts->contains($unpublishedconcert));
	}

	/** @test **/
	public function it_has_many_orders()
	{
		$concert = factory(Concert::class)->create();

		$this->assertInstanceOf(Collection::class, $concert->orders);
	}

	/** @test **/
	public function it_has_tickets()
	{
		$concert = factory(Concert::class)->create();

		$this->assertInstanceOf(Collection::class, $concert->tickets);
	}

	/** @test **/
	public function concert_can_add_tickets()
	{
		$concert = factory(Concert::class)->create();

		$concert->addTickets(50);

		$this->assertEquals(50, $concert->ticketsRemaining());
	}

	/** @test **/
	public function concert_counts_tickets_remaing_for_concerts()
	{
		$concert = factory(Concert::class)->create()->addTickets(50);

		$concert->orderTickets('jane@example', 30);

		$this->assertEquals(20, $concert->ticketsRemaining());
	}


	/** @test **/
	public function concert_can_have_ordered_tickets()
	{
		$concert = factory(Concert::class)->create()->addTickets(3);

		$order = $concert->orderTickets('jane@example.com', 3);

		$this->assertEquals(3, $order->ticketQuantity());
		$this->assertEquals('jane@example.com', $order->email);
	}

	/** @test **/
	public function can_not_purchase_tickets_more_than_remaining()
	{
		$concert = factory(Concert::class)->create()->addTickets(3);

		$this->expectException(NotEnoughTickectsRemaining::class);
		$orders = $concert->orderTickets('jane@example.com', 4);

		$this->assertNull($orders);
		$this->assertEquals(3, $concert->ticketsRemaining());
	}


	/** @test **/
	public function can_not_purchase_tickets_that_have_already_been_purchased()
	{
		$concert = factory(Concert::class)->create();
		$concert->addTickets(15);
		$concert->orderTickets('jane@example.com', 10);

		$this->expectException(NotEnoughTickectsRemaining::class);
		$johnOrders = $concert->orderTickets('john@example.com', 6);

		$this->assertNull($johnOrders);
		$this->assertEquals(5, $concert->ticketsRemaining());
	}

	/** @test **/
	public function can_reserve_a_ticket()
	{
		$concert = factory(Concert::class)->create()->addTickets(15);
		$this->assertEquals(15, $concert->ticketsRemaining());
		
		$reservation = $concert->reserveTickets(10, 'jane@example.com');
		
		$this->assertCount(10, $reservation->tickets());
		$this->assertEquals(5, $concert->ticketsRemaining());
	}

	/** @test **/
	public function can_not_reserve_already_purchased_tickets()
	{
		$concert = factory(Concert::class)->create()->addTickets(5);
		$concert->orderTickets('jane@example.com', 4);
		$this->expectException(NotEnoughTickectsRemaining::class);
        $concert->reserveTickets(3, 'jane@example.com');
		$this->assertEquals(1, $concert->ticketsRemaining());
	}

	/** @test **/
	public function can_not_reserve_already_been_reserved()
	{
		$concert = factory(Concert::class)->create()->addTickets(5);
		$concert->reserveTickets(4, 'jane@example.com');
		$this->expectException(NotEnoughTickectsRemaining::class);
        $concert->reserveTickets(3, 'jane@example.com');
		$this->assertEquals(1, $concert->ticketsRemaining());
	}
	
}
