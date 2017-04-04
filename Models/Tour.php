<?php

Namespace Golf;

class Tour extends \Illuminate\Database\Eloquent\Model 
{
	public $timestamps = false;

	public $fillable = [
		'code',
		'name'
	];
}