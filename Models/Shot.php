<?php

Namespace Golf;

class Shot extends \Illuminate\Database\Eloquent\Model 
{
	public $timestamps = false;

	public $fillable = [
		'player_id',
		'tour_id',
        'tournament_id',
        'course_id',
        'hole_id',
        'pin_id',
        'round',
        'tee',
        'cup',
        'putt',
        'distance',
        'left',
        'x',
        'y',
        'z',
        'to',
        'from',
        'text',
        'club'
	];
}