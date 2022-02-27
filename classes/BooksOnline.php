<?php



namespace Ocdla;




class BooksOnline {


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

		return View::renderTemplate($template, $params);
	}
	


	public static function getSampleSubscription($status = "expiring") {
		$sub = array(
			"Order" => array("EffectiveDate" => "2021-03-01")
		);
		
		return $sub;
	}
	


	/**
	 * Load an instance of Salesforce\RestApiRequest();
	 */
	public static function loadForceApi() {
		$accessToken = $_SESSION["access-token"];
		$instanceUrl = $_SESSION["instance-url"];

		// If the access token has been removed from the session, return false...for now.  (Need a better solution)
		if(empty($accessToken) || empty($instanceUrl)) return false;
		
		return new \Salesforce\RestApiRequest($instanceUrl, $accessToken);
	}


	public static function loadApi($api) {

		return self::loadForceApi();
	}


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

	



	public static function getCurrentSubscription($productName) {


		$contactId = $_SESSION["sf-contact-id"];

		$api = self::loadApi("com.salesforce.restapi");

		if(false === $api) return null;

		$query = "SELECT Id, OrderId, Order.ActivatedDate, Order.EffectiveDate FROM OrderItem WHERE Contact__c = '$contactId' AND Product2Id IN(SELECT Id FROM Product2 WHERE Name LIKE '%{$productName}%' AND IsActive = True) AND Order.StatusCode != 'Draft' ORDER BY Order.ActivatedDate DESC LIMIT 1";


		$resp = $api->query($query);

	

		return ($resp->success() && count($resp->getRecords()) > 0) ? $resp->getFirst() : null;
	}

}