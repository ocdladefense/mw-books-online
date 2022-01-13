<?php

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * General extension information.
 */
$wgExtensionCredits['specialpage'][] = array(
	'path'           				=> __FILE__,
	'name'           				=> 'BooksOnlineOcdla',
	'version'        				=> '0.0.0.1',
	'author'         				=> 'José Bernal',
	// 'descriptionmsg' 		=> 'wikilogocdla-desc',
	// 'url'            		=> 'http://www.mediawiki.org/wiki/Extension:WikilogOcdla',
);

// $wgExtensionMessagesFiles['WikilogOcdla'] = $dir . 'WikilogOcdla.i18n.php';

$dir = dirname( __FILE__ );

class BooksOnlineOcdla {

	public static function isBonNamespace($ns){
		global $wgOcdlaBooksOnlineNamespaces;
		return in_array($ns,$wgOcdlaBooksOnlineNamespaces);
	}

	public static function onParserSetup( Parser $parser ) {
		// When the parser sees the <sample> tag, it executes renderTagSample (see below)
		$parser->setHook( 'bonUpdate', 'BooksOnlineOcdla::renderTagBonUpdate' );
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


	public static function SetupBooksOnlineOcdla(){
		global $wgHooks, $wgResourceModules, $wgOcdlaShowBooksOnlineDrawer;

		// $wgHooks['SpecialSearchCreateLink'][] = 'SetupBooksOnlineOcdla::onSpecialSearchCreateLink';
		$wgHooks['BeforePageDisplay'][] = 'BooksOnlineOcdla::onBeforePageDisplay';
		$wgHooks['ParserFirstCallInit'][] = 'BooksOnlineOcdla::onParserSetup';


		$wgResourceModules['ext.booksOnlineOcdla.search'] = array(
			'scripts' => array('js/books-online-view.js','js/books-online-loader.js','js/search.controller.js'),
			'dependencies' => array('clickpdx.framework.js'),
			'position' => 'top',
			'remoteBasePath' => '/extensions/BooksOnlineOcdla',
			'localBasePath' => 'extensions/BooksOnlineOcdla'
		);
		
		$wgResourceModules['ext.booksOnlineOcdla.styles'] = array(
			'styles' => array(
				'css/bon.css'
			),
			'dependencies' => array('ext.uiFixedNav'), 
			'position' => 'top',
			'remoteBasePath' => '/extensions/BooksOnlineOcdla',
			'localBasePath' => 'extensions/BooksOnlineOcdla'
		);
		
	}
	
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {
		global $wgOcdlaShowBooksOnlineDrawer, $wgOcdlaShowBooksOnlineNs;
		
		$checkNamespace = isset($wgOcdlaShowBooksOnlineNs) && $wgOcdlaShowBooksOnlineNs != NS_ALL;
		
		$skin->customElements = array();
		
		$title = $out->getTitle();
		$ns = $title->getNamespace();
		
		if(self::isBonNamespace($ns)||$ns == NS_SSM) {
			/*
			$out->addModuleStyles( [
				'ext.booksOnlineOcdla.styles'
				//'skins.ocdla.styles',
			] );
			*/
			// $out->addStyle('/extensions/BooksOnlineOcdla/css/bon.css', 'all');
			// $out->addModules('ext.booksOnlineOcdla.styles');
		} else {
			// $out->addModules('ext.booksOnlineOcdla.banner');
		}
		
		$out->addModules('ext.booksOnlineOcdla.search');

		return true;
	}

}