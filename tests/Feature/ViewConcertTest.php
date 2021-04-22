<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewConcertTest extends TestCase
{
	use RefreshDatabase;

	/** @test **/
	public function a_user_can_view_a_published_concert()
	{

		$concert = factory(Concert::class)->states('published')->create([
			'title' => 'The Red Cord',
			'subtitle' => 'Mosh pit',
			'date' => Carbon::parse('February 12, 2021 8:00pm'),
			'ticket_price' => 3250,
			'avenue' => 'Nyayo Stadium',
			'address' => 'Westlands',
			'city' => 'Nairobi',
			'state' => 'Nairobi County',
			'zip' => '00800',
			'additional_information' => 'For tickets, call (254) 688-030',
		]);

		$response = $this->get('concerts/' . $concert->id);

		$response->assertSee('The Red Cord')
				->assertSee('Mosh pit')
				->assertSee('February 12, 2021')
				->assertSee('8:00pm')
				->assertSee(32.50)
				->assertSee('Nyayo Stadium')
				->assertSee('Westlands')
				->assertSee('Nairobi')
				->assertSee('Nairobi County')
				->assertSee('00800')
				->assertSee('For tickets, call (254) 688-030');
	}

	/** @test **/
	public function user_can_not_view_unpublished_concert()
	{
		$concert = factory(Concert::class)->states('unpublished')->create();

		$this->get('concerts/'.$concert->id)->assertStatus(404);

	}

}
