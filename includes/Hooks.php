<?php
namespace Armadillo;

use OutputPage;
use Skin;

class Hooks {
	/**
	 * BeforePageDisplay hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SkinAfterPortlet
	 *
	 * @param Skin &$skin Skin object
	 * @param string $portletName
	 * @param string $html
	 */
	public static function onSkinAfterPortlet( $skin, $portletName, &$html ) {
		// @todo: can add widgets to the right rail.
	}

	/**
	 * BeforePageDisplay hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	 *
	 * @param OutputPage &$out
	 * @param Skin &$skin Skin object that will be used to generate the page, added in 1.13.
	 */
	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		$out->addModules( 'armadillo' );
	}
}