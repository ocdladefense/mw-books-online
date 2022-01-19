<?php

namespace Ocdla;


if ( !defined( 'MEDIAWIKI' ) )
	die();

	
class View {


	public static function renderTemplate($path, $vars = array()) {

		extract($vars);

		ob_start();

		require $path;
		$content = ob_get_contents(); // get contents of buffer
		ob_end_clean();

		return $content;
	}



}