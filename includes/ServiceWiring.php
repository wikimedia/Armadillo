<?php
use MediaWiki\MediaWikiServices;

return [
	'Armadillo.Config' => static function ( MediaWikiServices $services ) {
		return $services->getService( 'ConfigFactory' )
				->makeConfig( armadillo );
	},
];
