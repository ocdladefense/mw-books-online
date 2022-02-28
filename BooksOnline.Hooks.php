<?php


use Ocdla\OrderHistory;
use Ocdla\Subscription;
use Ocdla\Date;


class BooksOnlineHooks {


	// The message that will be displayed to user's
	// if their subscription is about to expire.
	private static $expiryMessage = "Your Books Online subscription %s";


	// Number of days afte which we should 
	// be notifying the user their BON subscription will expire.
	private static $threshold = 30;


	// Set to force an expiring subscription.
	// For testing purposes.
	private static $test = true;


	
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
		if(!self::$test && self::isAdministrator($user)) return true;

		// Don't show alerts for guest users.
		if($user->isAnon()) return true;


		$contactId = $_SESSION["sf-contact-id"];
		$accessToken = $_SESSION["access-token"];
		$instanceUrl = $_SESSION["instance-url"];


		$orders = new OrderHistory($instanceUrl, $accessToken, $contactId);

		$subscription = self::$test ? $orders->getSampleSubscription("expiring") : $orders->getCurrentSubscription($contactId, "Books Online");

		// var_dump($subscription);exit;
		// Request failed or some other unexpected condition,
		// so bail out.		// var_dump($subscription);exit;
		// Request failed or some other unexpected condition,
		// so bail out.
		if(null == $subscription) return true;
		

		
		$daysRemaining = $subscription->daysRemaining();
		
		
		// Subscription isn't going to expire soon,
		//  so don't alert the customer.
		if($daysRemaining > self::$threshold) return true;


		$message = $subscription->getAlertHtml(
			self::$expiryMessage,
			$expiryDate,
			$daysRemaining
		);

	

		if(null != $message) {
			$out->addModuleStyles( [
				'ext.booksOnline.styles'
			] );
			$out->addHTML($message);
		}
		
		

		return true;
	}




	public static function isAdministrator($user) {

		return in_array("sysop", $user->getGroups());
	}




	public static function isBooksOnlineNamespace() {

		global $wgOcdlaBooksOnlineNamespaces, $wgTitle;

		return in_array($wgTitle->mNamespace, $wgOcdlaBooksOnlineNamespaces);
	}





}