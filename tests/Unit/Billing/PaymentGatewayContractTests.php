<?php

namespace Tests\Unit\Billing;

use App\Billing\PaymentFailedException;

trait PaymentGatewayContractTests
{
    abstract public function getPaymentGateway();

    	/** @test **/
	public function charges_are_successful_with_valid_token()
	{
		$paymentGateway = $this->getPaymentGateway();

		$newCharges = $paymentGateway->newChargesDuring(function($paymentGateway){
			return $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
		});

		$this->assertCount(1, $newCharges);
		$this->assertEquals(2500, $newCharges->sum());
	}

	public function can_get_last_charge_during()
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(1000, $paymentGateway->getValidTestToken());
        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());
        
       $newCharges =  $paymentGateway->newChargesDuring(function($paymentGateway){
            $paymentGateway->charge(3000, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([3000, 4000], $newCharges->all());
    }

     /** @test **/
     public function purchase_fails_with_a_valid_token()
     {
         $paymentGateway = $this->getPaymentGateway();
 
         $this->expectException(PaymentFailedException::class);
 
         $newCharge = $paymentGateway->newChargesDuring(function($paymentGateway){
             $paymentGateway->charge(3500, 'invalid-test-token');
         });

         $this->assertCount(0, $newCharge);
     }
}