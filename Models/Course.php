<?php

Namespace Golf;

class Course extends \Illuminate\Database\Eloquent\Model 
{
        public $timestamps = false;

	public $fillable = [
                'tour_id',
		'tournament_id',
                'number',
                'name',
                'year',
                'state',
                'city',
                'country',
	];
}