<?php

Namespace Services;

require 'vendor/autoload.php'; 

use Illuminate\Database\Capsule\Manager as Capsule; 
use Dotenv\Dotenv;
use Illuminate\Database\Schema\Blueprint;

class Schema
{
	public $schema;

	public function __construct()
	{
		$capsule = new Capsule;

		$dotenv = new Dotenv(dirname(__FILE__)."/..");
		$dotenv->load();

		$capsule->addConnection(array(
		    'driver'    => 'mysql',
		    'host'      => getenv('DB_HOST'),
		    'database'  => getenv('DB_NAME'),
		    'username'  => getenv('DB_USER'),
		    'password'  => getenv('DB_PASS'),
		    'charset'   => 'utf8',
		    'collation' => 'utf8_unicode_ci',
		    'prefix'    => ''
		));

		$capsule->bootEloquent();
        $capsule->setAsGlobal();
        $this->schema = $capsule->schema();
	}

	/**
	 * Add tables to the database.
	 */
	public function install()
	{	
		$this->schema->dropIfExists('courses');
		$this->schema->create('courses', function(Blueprint $table){

            $table->increments('id');
            $table->integer('tour_id');
            $table->integer('tournament_id');
            $table->string('number');
            $table->string('name');
            $table->integer('year');
            $table->string('state');
            $table->string('city');
            $table->string('country');

        });

        $this->schema->dropIfExists('tours');
		$this->schema->create('tours', function(Blueprint $table){

            $table->increments('id');
            $table->string('code');
            $table->string('name');

        });

        $this->schema->dropIfExists('tournaments');
		$this->schema->create('tournaments', function(Blueprint $table){

            $table->increments('id');
            $table->integer('tour_id');
            $table->string('name');
            $table->string('number');
            $table->integer('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('purse');
            $table->string('winner_share');

        });

        $this->schema->dropIfExists('holes');
		$this->schema->create('holes', function(Blueprint $table){

            $table->increments('id');
            $table->integer('course_id');
            $table->integer('number');
            $table->integer('year');
            $table->string('roll');
            $table->string('target_x');
            $table->string('target_y');
            $table->string('target_z');
            $table->string('green_roll');
            $table->string('green_x');
            $table->string('green_y');
            $table->string('green_z');

        });

        $this->schema->dropIfExists('pins');
		$this->schema->create('pins', function(Blueprint $table){

            $table->increments('id');
            $table->integer('hole_id');
            $table->integer('round_number');
            $table->integer('year');
            $table->integer('par');
            $table->integer('distance');
            $table->string('tee_x');
            $table->string('tee_y');
            $table->string('tee_z');
            $table->string('pin_x');
            $table->string('pin_y');
            $table->string('pin_z');

        });

        $this->schema->dropIfExists('golfers');
		$this->schema->create('golfers', function(Blueprint $table){

            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('country');
            $table->boolean('amateur');

        });

        $this->schema->dropIfExists('shots');
		$this->schema->create('shots', function(Blueprint $table){

            $table->increments('id');
            $table->integer('player_id');
            $table->integer('tour_id');
            $table->integer('tournament_id');
            $table->integer('course_id');
            $table->integer('hole_id');
            $table->integer('pin_id');
            $table->integer('round');
            $table->boolean('tee');
            $table->boolean('cup');
            $table->integer('putt');
            $table->integer('distance');
            $table->integer('left');
            $table->integer('x');
            $table->integer('y');
            $table->integer('z');
            $table->string('to');
            $table->string('from');
            $table->string('text');
            $table->string('club');

        });
	}
}