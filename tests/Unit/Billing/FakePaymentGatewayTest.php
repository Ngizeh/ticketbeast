<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\FakePaymentGateway;

class FakePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    public function getPaymentGateway(): FakePaymentGateway
    {
		return  new FakePaymentGateway;
	}

    /** @test **/
    public function running_a_hook_before_the_first_charge()
    {
        $paymentGateway = $this->getPaymentGateway();
        $callbackRun = 0;

        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$callbackRun){
            $callbackRun++;
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        $this->assertEquals(5000, $paymentGateway->totalCharges());
        $this->assertEquals(1, $callbackRun);
    }

}
