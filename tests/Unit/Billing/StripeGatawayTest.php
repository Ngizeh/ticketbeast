<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\StripeGateway;


/**
 * @group integration
 */
class StripeGatawayTest extends TestCase
{
    use PaymentGatewayContractTests;

	public function getPaymentGateway()
	{
		return  new StripeGateway(config('services.stripe.secret'));
	}
}
