<?php

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'BooksOnline',
	'version'        				=> '0.0.0.1',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);

// $wgExtensionMessagesFiles['BooksOnline'] = $dir . 'BooksOnline.i18n.php';

$dir = dirname( __FILE__ );




class BooksOnline {

	public static function isBonNamespace($ns){
		global $wgOcdlaBooksOnlineNamespaces;
		return in_array($ns,$wgOcdlaBooksOnlineNamespaces);
	}



	public static function onParserSetup( Parser $parser ) {
		// When the parser sees the <sample> tag, it executes renderTagSample (see below)
		// $parser->setHook( 'bonUpdate', 'BooksOnlineOcdla::renderTagBonUpdate' );
		return true;
	}


	// Render <bonUpdate>	
	public static function renderTagBonUpdate( $input, array $args, Parser $parser, PPFrame $frame ) {
		// Nothing exciting here, just escape the user-provided input and throw it back out again (as example)
		// return htmlspecialchars( $input );
		$year = $args['year'];
		$output = $parser->recursiveTagParse( $input, $frame );
		return "<div class='bon-update bon-update-{$year}'><span class='bon-update-title'>{$year} Update</span>".$output."</div>";
	}


	public static function SetupBooksOnline(){
		global $wgHooks, $wgResourceModules, $wgOcdlaShowBooksOnlineDrawer;

		$wgHooks['BeforePageDisplay'][] = 'BooksOnline::onBeforePageDisplay';
		// $wgHooks['ParserFirstCallInit'][] = 'BooksOnline::onParserSetup';


		/*
		$wgResourceModules['ext.booksOnlineOcdla.search'] = array(
			'scripts' => array('js/books-online-view.js','js/books-online-loader.js','js/search.controller.js'),
			'dependencies' => array('clickpdx.framework.js'),
			'position' => 'top',
			'remoteBasePath' => '/extensions/BooksOnline',
			'localBasePath' => 'extensions/BooksOnline'
		);
		
		$wgResourceModules['ext.booksOnline.styles'] = array(
			'styles' => array(
				'css/bon.css'
			),
			'dependencies' => array('ext.uiFixedNav'), 
			'position' => 'top',
			'remoteBasePath' => '/extensions/BooksOnlineOcdla',
			'localBasePath' => 'extensions/BooksOnlineOcdla'
		);
		*/
		
	}
	
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {

		self::getCurrentSubscriptionEndDate();
		
		$out->addHTML("<h2>Hello World!</h2>");

		return true;
	}



	/**
	 * Load an instance of Salesforce\RestApiRequest();
	 */
	public static function getForceApi() {
		$accessToken = $_SESSION["access-token"];
		$instanceUrl = $_SESSION["instance-url"];

		// If the access token has been removed from the session, return false...for now.  (Need a better solution)
		if(empty($accessToken) || empty($instanceUrl)) return false;
		
		return new RestApiRequest($instanceUrl, $accessToken);
	}


	



	public static function getCurrentSubscriptionEndDate($contactId) {


		$contactId = $_SESSION["sf-contact-id"];

		$api = self::getForceApi();



		$minPurchaseDate = new \DateTime();
		$minPurchaseDate->modify("-367 days");
		$minPurchaseDate = $minPurchaseDate->format("Y-m-d");


		// Subscription should last only a year, but we dont have a reliable way of determining expiration.
		//$query = "SELECT Id FROM OrderItem WHERE Contact__c = '$contactId' AND RealExpirationDate__c > $today AND Product2id IN($soqlProdIds)";
		$query = "SELECT Id, OrderId, Order.ActivatedDate, Order.EffectiveDate FROM OrderItem WHERE Contact__c = '$contactId' AND Product2Id IN(SELECT Id FROM Product2 WHERE Name LIKE '%Books Online%' AND IsActive = True) AND Order.StatusCode != 'Draft' ORDER BY Order.ActivatedDate DESC LIMIT 1";


		$resp = $api->query($query);

		$record = $resp->getFirst();

		var_dump($record);

		if(!$resp->success()) throw new \Exception($resp->getErrorMessage());

		count($resp->getRecords()) > 0;
	}

}