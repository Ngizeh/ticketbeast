<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function scopePublished($query)
    {
    	return $query->whereNotNull('published_at');
    }


    public function getformattedDateAttribute()
    {
    	return $this->date->format('F j, Y');
    }

    public function getformattedTimeAttribute()
    {
    	return $this->date->format('g:ia') ;
    }

	public function getformattedPriceAttribute()
    {
    	return number_format($this->ticket_price/100, 2);
    }

}
