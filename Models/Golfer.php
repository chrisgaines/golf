<?php

Namespace Golf;

class Golfer extends \Illuminate\Database\Eloquent\Model 
{
	public $timestamps = false;

	public $fillable = [
		'first_name',
        'last_name',
        'country',
        'amateur'
	];
}