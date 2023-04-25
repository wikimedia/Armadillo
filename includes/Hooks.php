<?php
namespace Armadillo;

use OutputPage;
use Parser;
use ParserOutput;
use Skin;

class Hooks {
		/**
	 * @param Parser $parser
	 * @return bool
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( 'armadillo', [ Armadillo::class, 'parserHook' ] );
		return true;
	}

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
		$widgets = array_filter( $skin->getOutput()->getProperty( 'armadillo' ) ?? [], static function ( ArmadilloWidget $widget ) use ( $portletName ) {
			$location = $widget->location;
			return $location === $portletName;
		} );
		if ( count( $widgets ) ) {
			foreach ( $widgets as $widget ) {
				$html .= $widget->toHTML( $widget );
			}
		}

	}

	/**
	 * OutputPageParserOutput hook handler
	 * @param OutputPage $outputPage
	 * @param ParserOutput $parserOutput ParserOutput instance being added in $outputPage
	 */
	public static function onOutputPageParserOutput(
		OutputPage $outputPage, ParserOutput $parserOutput
	): void {
		$armadillo = $parserOutput->getExtensionData( 'armadillo' );
		$outputPage->setProperty( 'armadillo', $armadillo );
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
		$out->addModuleStyles( 'armadillo.styles' );
	}
}