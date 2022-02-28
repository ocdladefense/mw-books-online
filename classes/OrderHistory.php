<?php


use Salesforce\RestApiRequest;

namespace Ocdla;




class OrderHistory extends RestApiRequest {


	static $query = "SELECT Id, OrderId, Order.ActivatedDate, Order.EffectiveDate FROM OrderItem WHERE Contact__c = '$contactId' AND Product2Id IN(SELECT Id FROM Product2 WHERE Name LIKE '%{$productName}%' AND IsActive = True) AND Order.StatusCode != 'Draft' ORDER BY Order.ActivatedDate DESC LIMIT 1";


	static $queryAll = "SELECT Id, OrderId, Order.ActivatedDate, Order.EffectiveDate FROM OrderItem WHERE Product2Id IN(SELECT Id FROM Product2 WHERE Name LIKE '%{$productName}%' AND IsActive = True) AND Order.StatusCode != 'Draft' ORDER BY Order.ActivatedDate";


	private $api = null;


	private $contactId = null;



	/**
	 * Load an instance of Salesforce\RestApiRequest();
	 */
	public function __construct($contactId) {

		$this->contactId = $contactId;
	}



	public static function loadApi($instanceUrl, $accessToken) {

		// If the access token has been removed from the session, return false...for now.  (Need a better solution)
		if(empty($accessToken) || empty($instanceUrl)) {
			throw new \Exception("foobar");
		}
		self::$api = new \Salesforce\RestApiRequest($instanceUrl, $accessToken);
	}

	


	public static function getSampleSubscription($status = "expiring") {
		$sub = array(
			"Order" => array("EffectiveDate" => "2021-03-01")
		);
		
		return $sub;
	}
	

	public static function getCurrentSubscription($contactId, $productName) {

		$api = self::loadApi("com.salesforce.restapi");

		if(false === $api) return null;


		$resp = $api->query(self::$query);

		$record =  ($resp->success() && count($resp->getRecords()) > 0) ? $resp->getFirst() : null;

		return new Subscription($record);
	}


	public function getCustomersWhoPurchased($product = "Books Online") {


		if(null === $api) {
			throw new \Exception("INITIALIZATION_ERROR: Salesforce API has not been initialized.");
		}


		$soql = sprintf(self::$queryAll, "");

		$resp = $this->api->query(self::$queryAll);

		return $resp->getRecords();
	}

}