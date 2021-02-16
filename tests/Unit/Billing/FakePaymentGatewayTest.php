<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;

class FakePaymentGatewayTest extends TestCase
{
    /** @test **/
    public function it_charges_a_concert_with_a_valid_token()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(3500, $paymentGateway->getValidTestToken());

        $this->assertEquals(3500, $paymentGateway->totalCharges());
    }

    /** @test **/
    public function purchase_fails_with_a_valid_token()
    {
        $paymentGateway = new FakePaymentGateway;
        $this->expectException(PaymentFailedException::class);
        $paymentGateway->charge(3500, 'invalid-test-token');
    }
}
