<?php

namespace Tests\Feature;

use App\Order;
use App\Ticket;
use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewOrderTest extends TestCase
{
	use RefreshDatabase;

	/** @test **/
	public function user_can_view_their_confirmation()
	{
		$this->withoutExceptionHandling();

		$concert = factory(Concert::class)->create();

		$order = factory(Order::class)->create([
			'confirmation_order' => 'ORDERCONFIRMATION12345',
			'amount' => 3400,
			'card_last_four' => '4242'
		]);

		$ticketA = factory(Ticket::class)->create([
			'order_id' => $order->id,
			'concert_id' => $concert->id,
			'ticket_code' => 'TICKETCODE123'
		]);

		$ticketB = factory(Ticket::class)->create([
			'order_id' => $order->id,
			'concert_id' => $concert->id,
			'ticket_code' => 'TICKETCODE456'
		]);

		$response = $this->get('/orders/ORDERCONFIRMATION12345')->assertStatus(200);

		$response->assertViewHas('order', $order);

		$response->assertSee('ORDERCONFIRMATION12345');
		$response->assertSee('$34.00');
		$response->assertSee('**** **** **** 4242');
		$response->assertSee('TICKETCODE123');
		$response->assertSee('TICKETCODE456');
		$response->assertSee('The Mugithi Gikuyu');
		$response->assertSee('with Samidoe');
		$response->assertSee('Karasani Stadium');
		$response->assertSee('Karasani');
		$response->assertSee('Gigurai');
		$response->assertSee('Kiambu County');
		$response->assertSee('008700');

	   $response->assertSee(Carbon::parse('+2 weeks')->format('Y-m-d'));
	}

}
