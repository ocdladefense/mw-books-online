<?php



namespace Ocdla;




class BooksOnline {


	private static $expiryMessage = "Your Books Online subscription %s";


	private static $threshold = 400;





	
	/**
     * 
     */
    public static function onBeforeInitialize( \Title &$title, $unused, \OutputPage $out, \User $user, \WebRequest $request, \MediaWiki $mediaWiki ) {


		// Uncomment this line to make this hook do nothing.
		// Useful when running into trouble with 
		// any of the Salesforce API requests.
		// return true;


		// Don't show alerts for non-BON pages.
		if(!self::isBooksOnlineNamespace()) return true;

		// Don't show alerts for system administrators.
		if(self::isAdministrator($user)) return true;

		// Don't show alerts for guest users.
		if($user->isAnon()) return true;


		
		$subscription = self::getCurrentSubscription("Books Online");
		$orderStartDate = $subscription["Order"]["EffectiveDate"];
		$startDate = new \DateTime($startDate);
		// Uncomment to mock an Order EffectiveDate
		// $startDate = new \DateTime("2022-01-17");
		$expiryDate = clone $startDate;
		$expiryDate->modify("+365 day");


		
		$daysRemaining = self::daysFromToday($expiryDate);
		
		
		// Subscription isn't going to expire soon,
		//  so don't alert the customer.
		if($daysRemaining > self::$threshold) return true;


		$message = self::getAlertHtml($expiryDate, $daysRemaining);

	

		if(null != $message) {
			$out->addModuleStyles( [
				'ext.booksOnline.styles'
			] );
			$out->addHTML($message);
		}
		

		return true;
	}






	public static function getAlertHtml($expiryDate, $daysRemaining) {

		$template = "/var/www/html/extensions/BooksOnline/templates/bon-alert.tpl.php";
		

		$renewThroughDate = clone $expiryDate;
		$renewThroughDate->modify("+365 day");

		$message = self::getFriendlyDateDifference($daysRemaining);

		if(null == $message) return $message;


		$params = array(
			"expiryDate" => $expiryDate,
			"renewThroughDate" => $renewThroughDate,
			"message" => $message
		);

		return View::renderTemplate($template, $params);
	}
	






	public static function daysFromToday($date) {

		$today = new \DateTime();


		$intv = $date->diff($today);
		$years = $intv->y;
		$months = $intv->m;
		$days = $intv->d;
		// var_dump($intv); exit;

		return ($years * 365) + ($months * 30) + $days;
	}



	public static function getFriendlyDateDifference($days) {



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


		
		return sprintf(self::$expiryMessage, $text);
	}



	public static function isAdministrator($user) {

		return in_array("sysop", $user->getGroups());
	}




	public static function isBooksOnlineNamespace() {

		global $wgOcdlaBooksOnlineNamespaces, $wgTitle;

		return in_array($wgTitle->mNamespace, $wgOcdlaBooksOnlineNamespaces);
	}


	/**
	 * Load an instance of Salesforce\RestApiRequest();
	 */
	public static function getForceApi() {
		$accessToken = $_SESSION["access-token"];
		$instanceUrl = $_SESSION["instance-url"];

		// If the access token has been removed from the session, return false...for now.  (Need a better solution)
		if(empty($accessToken) || empty($instanceUrl)) return false;
		
		return new \Salesforce\RestApiRequest($instanceUrl, $accessToken);
	}


	



	public static function getCurrentSubscription($productName) {


		$contactId = $_SESSION["sf-contact-id"];

		$api = self::getForceApi();

		if(false === $api) return null;

		$query = "SELECT Id, OrderId, Order.ActivatedDate, Order.EffectiveDate FROM OrderItem WHERE Contact__c = '$contactId' AND Product2Id IN(SELECT Id FROM Product2 WHERE Name LIKE '%{$productName}%' AND IsActive = True) AND Order.StatusCode != 'Draft' ORDER BY Order.ActivatedDate DESC LIMIT 1";


		$resp = $api->query($query);

	

		return ($resp->success() && count($resp->getRecords()) > 0) ? $resp->getFirst() : null;
	}

}