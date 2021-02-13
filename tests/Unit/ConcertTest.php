<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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


}
