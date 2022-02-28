<?php


use Salesforce\RestApiRequest;

namespace Ocdla;

class Subscription {


	private $contactId;



	private $OrderItem;



	public static function daysRemaining($subscription) {

		// var_dump($subscription);exit;
		// Request failed or some other unexpected condition,
		// so bail out.
		if(null == $subscription) return true;
		
		$orderStartDate = $subscription["Order"]["EffectiveDate"];
		$startDate = new \DateTime($orderStartDate);
		
		// Uncomment to mock an Order EffectiveDate
		$expiryDate = clone $startDate;
		$expiryDate->modify("+365 day");


		
		return Date::daysFromToday($expiryDate);
	}



	public static function getAlertHtml($message, $expiryDate, $daysRemaining) {

		$template = "/var/www/html/extensions/BooksOnline/templates/bon-alert.tpl.php";
		

		$renewThroughDate = clone $expiryDate;
		$renewThroughDate->modify("+365 day");

		$notice = Date::getFriendlyDateDifference($message,$daysRemaining);

		if(null == $notice) return $notice;


		$params = array(
			"expiryDate" => $expiryDate,
			"renewThroughDate" => $renewThroughDate,
			"message" => $notice
		);

		return Template::renderTemplate($template, $params);
	}


}