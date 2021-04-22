<?php

namespace App\Billing;

use Stripe\Token;
use Stripe\Charge;
use Stripe\Exception\InvalidRequestException;

class StripeGateway implements PaymentGateway
{
	protected $api_key;

	public function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

	public function getValidTestToken()
	{
		return Token::create([
			'card' => [
				'number' => '4242424242424242',
				'exp_month' => 2,
				'exp_year' => date('Y') + 1,
				'cvc' => '123',
			]
		], ['api_key' => $this->api_key])->id;
	}


	public function charge($amount, $token)
	{
		try {
			Charge::create([
				'amount' => $amount,
				'source' => $token,
				'currency' => 'usd'
			], ['api_key' => $this->api_key]);
		} catch (InvalidRequestException $e) {
			throw new PaymentFailedException();
		}
	}

	public function newChargesDuring($callback)
	{
		$lastCharge = $this->lastCharge();
		$callback($this);
		return $this->newCharges($lastCharge)->pluck('amount');
	}

	public function lastCharge()
	{
		return Charge::all(
			['limit' => 1],
			['api_key' => config('services.stripe.secret')]
		)['data'][0];
	}
	

	public function newCharges($charge = null)
	{
		$newCharges = Charge::all(
			[
				'limit' => 1,
				'ending_before' => $charge ? $charge->id : null
			],
			['api_key' => $this->api_key]
		)['data'];

		return collect($newCharges);
	}
}
