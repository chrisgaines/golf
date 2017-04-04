<?php

Namespace Golf;

class Tournament extends \Illuminate\Database\Eloquent\Model 
{
	public $timestamps = false;

	public $fillable = [
		'tour_id',
                'name',
                'number',
                'year',
                'start_date',
                'end_date',
                'purse',
                'winner_share',
	];
}