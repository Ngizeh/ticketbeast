<?php


namespace App;


class RandomOrderConfirmationNumberGenerator implements OrderConfirmationNumber
{
    public function generate(): string
    {
        $pool = "ABCDEFGHJLKMNPQRSTUVWXYZ23456789";

        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }
}
