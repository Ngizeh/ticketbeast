<?php

namespace Tests\Feature;

use App\Concert;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentGateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;

        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }


    /** @test **/
    public function users_can_purchase_tickets_of_a_published_concert()
    {
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 3500])->addTickets(4);

        $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 4,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(201);

        $this->assertEquals(14000, $this->paymentGateway->totalCharges());

        $this->assertTrue($concert->hasOrdersFor('john@example.com'));
        $this->assertEquals(4, $concert->ordersFor('john@example.com')->first()->ticketQuantity());
    }

    /** @test **/
    public function users_not_can_purchase_tickets_of_unpublished_concerts()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();
        $concert->addTickets(4);

        $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 4,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(404);

        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertFalse($concert->hasOrdersFor('john@example.com'));
    }

    /** @test **/
    public function can_not_purchase_more_tickets_thaan_the_remaining()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(50);

        $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(422);


        $this->assertFalse($concert->hasOrdersFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }


    /** @test **/
    public function email_is_required_to_purchase_a_tickect()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(4);

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => null,
            'ticket_quantity' => 4,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertJsonValidationErrors(['email']);
    }
    /** @test **/
    public function email_must_be_a_valid_email_to_purchase_a_tickect()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $concert->addTickets(4);

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'emailthatdoesnotexit',
            'ticket_quantity' => 4,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
    }
    /** @test **/
    public function ticket_is_required_to_purchase_a_tickect()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(4);

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(422);

        $response->assertJsonValidationErrors(['ticket_quantity']);
    }
    /** @test **/
    public function ticket_quantity_should_be_atleast_one_to_purchase_a_tickect()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(422);

        $response->assertJsonValidationErrors(['ticket_quantity']);
    }

    /** @test **/
    public function valid_payment_token_is_required_to_make_a_successful_purchase()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(4);

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 4,
            'payment_token' => 'invalid-payement-token'
        ]);

        $response->assertStatus(422);
        $order = $concert->orders()->whereEmail('john@example.com')->first();
        $this->assertNull($order);
    }

    /** @test **/
    public function payment_token_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(4);

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
        ])->assertStatus(422);

        $response->assertJsonValidationErrors(['payment_token']);
    }
}
