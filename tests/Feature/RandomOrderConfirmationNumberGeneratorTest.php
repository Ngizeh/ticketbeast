<?php

namespace Tests\Feature;

use App\RandomOrderConfirmationNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RandomOrderConfirmationNumberGeneratorTest extends TestCase
{
    // Must 24 characters long
    // Contain uppercase letter and numbers
    // No ambiguous characters
    // Must be unique

    /** @test **/
    public function have_24_characters_long()
    {
        $this->withoutExceptionHandling();

        $confirmationNumber = new RandomOrderConfirmationNumberGenerator;

        $generator = $confirmationNumber->generate();

        $this->assertEquals(24, strlen($generator));
    }

    /** @test **/
    public function must_have_uppercase_letter_and_numbers_only()
    {
        $confirmationNumber = new RandomOrderConfirmationNumberGenerator;

        $generator = $confirmationNumber->generate();

        $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $generator);
    }

    /** @test **/
    public function no_ambiguous_characters_in_the_confirmation_order()
    {
        $confirmationNumber = new RandomOrderConfirmationNumberGenerator;

        $generator = $confirmationNumber->generate();

        $this->assertFalse(strpos($generator, 1));
        $this->assertFalse(strpos($generator, 'I'));
        $this->assertFalse(strpos($generator, 0));
        $this->assertFalse(strpos($generator, 'O'));
    }

    /** @test **/
    public function must_be_a_confirmation_number()
    {
        $confirmationNumber = new RandomOrderConfirmationNumberGenerator;

        $confirmation = array_map(function ($i) use ($confirmationNumber) {
            return $confirmationNumber->generate();
        }, range(1, 100));

        $this->assertCount(100, array_unique($confirmation));
    }

}
