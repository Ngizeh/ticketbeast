<?php

namespace Tests\Feature;

use Mockery;
use App\Concert;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\OrderConfirmationNumber;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var FakePaymentGateway
     */
    private $paymentGateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;

        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderRequest($concertId, $params, $status = 201)
    {
        $savedRequest = $this->app['request'];
        $this->postJson("concerts/{$concertId}/orders", $params)->assertStatus($status);
        $this->app['request'] = $savedRequest;
    }


    /** @test **/
    public function users_can_purchase_tickets_of_a_published_concert()
    {
        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 3500])->addTickets(4);

        $orderConfirmationNumber = Mockery::mock(OrderConfirmationNumber::class, [
            'generate' => 'ORDERCONFIRMATION12345'
        ]);

        $this->app->instance(OrderConfirmationNumber::class, $orderConfirmationNumber);

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 4,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(201);

        $this->assertEquals(14000, $this->paymentGateway->totalCharges());

        $this->assertTrue($concert->hasOrdersFor('john@example.com'));

        $response->assertJson([
            'confirmation_order' => 'ORDERCONFIRMATION12345',
            'email' => 'john@example.com',
            'amount' => 14000,
            'ticket_quantity' => 4
        ]);

        $this->assertEquals(4, $concert->ordersFor('john@example.com')->first()->ticketQuantity());

        $this->assertTrue($concert->hasOrdersFor('john@example.com'));
    }

    /** @test **/
    public function users_not_can_purchase_tickets_of_unpublished_concerts()
    {
        $concert = factory(Concert::class)->states('unpublished')->create()->addTickets(4);
        $this->orderRequest($concert->id, [
            'email' => 'john@example.com',
            'ticket_quantity' => 4,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ], 404);
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertFalse($concert->hasOrdersFor('john@example.com'));
    }

    /** @test **/
    public function can_not_purchase_more_tickets_than_the_remaining()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(50);
        $this->orderRequest($concert->id, [
            'email' => 'personA@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ], 422);
        $this->assertFalse($concert->hasOrdersFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test **/
    public function can_purchase_tickets_another_customer_is_already_trying_to_buy()
    {
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 1200])->addTickets(3);

        $this->paymentGateway->beforeFirstCharge(function ($paymentGateway) use ($concert) {
            $this->orderRequest($concert->id, [
                'email' => 'personA@example.com',
                'ticket_quantity' => 1,
                'payment_token' => $paymentGateway->getValidTestToken()
            ], 422);
            $this->assertFalse($concert->hasOrdersFor('personA@example.com'));
            $this->assertEquals(0, $paymentGateway->totalCharges());
        });

        $this->orderRequest($concert->id, [
            'email' => 'personB@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ], 201);

        $this->assertTrue($concert->hasOrdersFor('personB@example.com'));
        $this->assertEquals(3600, $this->paymentGateway->totalCharges());
        $this->assertEquals(3, $concert->ordersFor('personB@example.com')->first()->ticketQuantity());
    }

    /** @test **/
    public function valid_payment_token_is_required_to_make_a_successful_purchase()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(4);
        $this->assertEquals(4, $concert->ticketsRemaining());

        $this->orderRequest($concert->id, [
            'email' => 'john@example.com',
            'ticket_quantity' => 4,
            'payment_token' => 'invalid-payement-token'
        ], 422);

        $this->assertFalse($concert->hasOrdersFor('john@example.com'));
        $this->assertEquals(4, $concert->fresh()->ticketsRemaining());
    }

    /** @test **/
    public function ticket_is_required_to_purchase_a_ticket()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(4);

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(422);

        $response->assertJsonValidationErrors(['ticket_quantity']);
    }
    /** @test **/
    public function ticket_quantity_should_be_at_least_one_to_purchase_a_ticket()
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
    public function payment_token_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(4);

        $response = $this->postJson("concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
        ])->assertStatus(422);

        $response->assertJsonValidationErrors(['payment_token']);
    }


    /** @test **/
    public function email_is_required_to_purchase_a_ticket()
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
    public function email_must_be_a_valid_email_to_purchase_a_ticket()
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
}
