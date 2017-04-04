<?php

Namespace Golf;

class Hole extends \Illuminate\Database\Eloquent\Model 
{
	public $timestamps = false;

	public $fillable = [
		'course_id',
		'year',
		'number',
        'roll',
        'target_x',
        'target_y',
        'target_z',
        'green_roll',
        'green_x',
        'green_y',
        'green_z',
	];
}