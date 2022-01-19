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


$dir = dirname( __FILE__ );


// $wgExtensionMessagesFiles['BooksOnline'] = $dir . 'BooksOnline.i18n.php';
$wgAutoloadClasses['Ocdla\View'] = $dir .'/classes/View.php';
$wgAutoloadClasses['Ocdla\BooksOnline'] = $dir .'/classes/BooksOnline.php';

$wgHooks['BeforeInitialize'][] = 'Ocdla\BooksOnline::onBeforeInitialize';
	

$wgResourceModules['ext.booksOnline.styles'] = array(
	'styles' => array(
		'css/bon.css'
	), 
	'position' => 'top',
	'remoteBasePath' => '/extensions/BooksOnline',
	'localBasePath' => 'extensions/BooksOnline'
);
	


