<?php

Namespace Services;

require './Config/config.php';

use Carbon\Carbon;
use Golf\Course;
use Golf\Golfer;
use Golf\Hole;
use Golf\Pin;
use Golf\Shot;
use Golf\Tour;
use Golf\Tournament;

class Importer
{
	/**
	 * Retrieve and save all data that has happened before today.
	 */
	public static function install()
	{
		// The first thing we want to do is import all tournaments for this year.
		self::current(2017);
	}

	/**
	 *
	 */
	public static function current($year)
	{
		$json = json_decode(file_get_contents('http://www.pgatour.com/data/r/current/schedule-v2.json'),true);

		foreach ($json['years'][0]['tours'] as $tour)
		{
			// Create the tour.
			$Tour = Tour::firstOrCreate(['code' => $tour['tourCodeLc'], 'name' => $tour['desc']]);

			// Loop through the tournaments and add those.
			foreach ($tour['trns'] as $tournament)
			{
				// Create the tournament.
				$Tournament = Tournament::firstOrCreate([
					'tour_id' => $Tour->id,
			        'name' => $tournament['trnName']['official'],
			        'number' => $tournament['permNum'],
			        'year' => $year,
			        'start_date' => $tournament['date']['start'],
			        'end_date' => $tournament['date']['end'],
			        'purse' => $tournament['Purse'],
                	'winner_share' => $tournament['winnersShare'],
				]);

				// Loop through the courses for this tournament.
				foreach ($tournament['courses'] as $course)
				{
					Course::firstOrCreate([
						'tour_id' => $Tour->id,
						'tournament_id' => $Tournament->id,
		                'number' => $course['number'],
		                'name' => $course['courseName'],
		                'year' => $year,
		                'state' => $course['location']['state'],
		                'city' => $course['location']['city'],
		                'country' => $course['location']['country']
					]);
				} // End loop through courses

				// Now it is time to find the leaderboard if the tournament is over.
				if (Carbon::now()->gt(Carbon::parse($tournament['date']['end'])))
				{
					self::leaderboard($year,$Tour,$Tournament);
				}

			} // End loop through tournaments

		} // End loop through tours

	}

	public static function leaderboard($year,$tour,$tournament)
	{
		$leaderboard = json_decode(file_get_contents('http://www.pgatour.com/data/' . $tour->code . '/' . $tournament->number . '/' . $year . '/leaderboard-v2.json'),true);

		// Find the courses, create the holes and pin/tee locations.
		foreach ($leaderboard['leaderboard']['courses'] as $course)
		{
			$Course = Course::where('number',$course['course_id'])->where('tour_id',$tour->id)->where('year',$year)->first();

			foreach ($course['holes'] as $hole)
			{
				$Hole = Hole::firstOrCreate([
					'course_id' => $Course->id,
					'number' => $hole['hole_id'],
					'year' => $year,
			        'roll' => $hole['hole_roll'],
			        'target_x' => $hole['hole_target_x'],
			        'target_y' => $hole['hole_target_y'],
			        'target_z' => $hole['hole_target_z'],
			        'green_roll' => $hole['green_roll'],
			        'green_x' => $hole['green_target_x'],
			        'green_y' => $hole['green_target_x'],
			        'green_z' => $hole['green_target_x'],
				]);

				// Create the pin locations.
				foreach ($hole['round'] as $round)
				{
					Pin::firstOrCreate([
						'hole_id' => $Hole->id,
				        'round_number' => $round['round_num'],
				        'year' => $year,
				        'par' => $round['par'],
				        'distance' => $round['distance'],
				        'tee_x' => $round['tee_x'],
				        'tee_y' => $round['tee_y'],
				        'tee_z' => $round['tee_z'],
				        'pin_x' => $round['pin_x'],
				        'pin_y' => $round['pin_y'],
				        'pin_z' => $round['pin_z'],
					]);
				} // End pins
			
			} // End holes
			
		} // End course

		// Now import all the players shots.
		foreach ($leaderboard['leaderboard']['players'] as $player)
		{
			$Player = Golfer::firstOrCreate([
				'first_name' => $player['player_bio']['first_name'],
				'last_name' => $player['player_bio']['last_name'],
				'country' => $player['player_bio']['country'],
				'amateur' => $player['player_bio']['is_amateur']
			]);

			// Now that we have the player, lets get his score card.
			$scorecard = json_decode(file_get_contents('http://www.pgatour.com/data/' . $tour->code . '/' . $tournament->number . '/scorecards/' . $player['player_id'] . '.json'),true);

			$Course = Course::where('number',$player['course_id'])->where('tour_id',$tour->id)->where('year',$year)->first();

			if (empty($Course))
			{
				dd($player['course_id']);
			}

			foreach ($scorecard['p']['rnds'] as $round)
			{
				foreach ($round['holes'] as $hole)
				{
					foreach ($hole['shots'] as $shot)
					{
						// Find the pin and hole.
						$Hole = Hole::where('course_id',$Course->id)->where('number',$hole['cNum'])->where('year',$year)->first();

						$Pin = Pin::where('hole_id',$Hole->id)->where('year',$year)->where('round_number',$round['n'])->first();

						Shot::firstOrCreate([
							'player_id' => $Player->id,
							'tour_id' => $tour->id,
					        'tournament_id' => $tournament->id,
					        'course_id' => $Course->id,
					        'hole_id' => $Hole->id,
					        'pin_id' => $Pin->id,
					        'round' => $round['n'],
					        'tee' => $shot['tee'] == 'y' ? true : false,
					        'cup' => $shot['cup'] == 'y' ? true : false,
					        'putt' => $shot['putt'],
					        'distance' => $shot['dist'],
					        'left' => $shot['left'],
					        'x' => $shot['x'],
					        'y' => $shot['y'],
					        'z' => $shot['z'],
					        'to' => $shot['to'],
					        'from' => $shot['from'],
					        'text' => $shot['shotText'],
					        'club' => $shot['club']
						]);
					} // Finish shots
				} // Finish holes
			} // Finish rounds

		} // Finish players
	}
}