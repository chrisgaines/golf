<?php

Namespace Golf;

class Pin extends \Illuminate\Database\Eloquent\Model 
{
	public $timestamps = false;

	public $fillable = [
		'hole_id',
        'round_number',
        'year',
        'par',
        'distance',
        'tee_x',
        'tee_y',
        'tee_z',
        'pin_x',
        'pin_y',
        'pin_z',
	];
}