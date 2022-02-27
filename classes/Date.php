<?php

namespace Ocdla;


class Date {





	public static function daysFromToday($date) {

		$today = new \DateTime();


		$intv = $date->diff($today);
		$years = $intv->y;
		$months = $intv->m;
		$days = $intv->d;
		// var_dump($intv); exit;

		return ($years * 365) + ($months * 30) + $days;
	}



	public static function getFriendlyDateDifference($message, $days, $params = array()) {

		// "past" => "expired",
		// "near" => "expires", "%s yesterday", "%s today", "%s tomorrow"
		// "future" => "expires in" but "will expire"

		$abs = abs($days);



		if($days < -1) {

			$text = sprintf("expired %s days ago.", $abs);
		} else if($days == -1) {
			$text = "expired yesterday.";
		} else if($days == 0) {
			$text = "expires today.";
		} else if($days == 1) {
			$text = "expires tomorrow.";
		} else if($days > 1) {
			$text = sprintf("expires in %s days.", $abs);
		}


		
		return sprintf($message, $text);
	}
}