<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Concert;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Concert::class, function (Faker $faker) {
	return [
		'title' => 'The Mugithi Gikuyu',
		'subtitle' => ' with Samidoe',
		'date' => Carbon::parse('+2 weeks'),
		'ticket_price' => 2000,
		'avenue' => 'Karasani Stadium',
		'address' => 'Karasani',
		'city' => 'Gigurai',
		'state' => 'Kiambu County',
		'zip' => '008700',
		'additional_information' => 'Some additional information',
	];
});

$factory->state(Concert::class, 'published', function(Faker $faker){
	return [
		'published_at' => Carbon::parse('-2 weeks')
	];
});

$factory->state(Concert::class, 'unpublished', function(Faker $faker){
	return [
		'published_at' => null
	];
});
