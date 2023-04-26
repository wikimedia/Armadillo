<?php

namespace Armadillo;

use ExtensionRegistry;
use Html;

class Armadillo {
	private const LOCATION_ARTICLE = 'article';
	private const DEFAULT_ARGS = [
		'name' => null,
		'location' => self::LOCATION_ARTICLE
	];
	private const DEFAULT_TAGS = [
		'armadillo' => [
			'module' => 'armadillo.widgets',
			'aspect-ratio' => '799/482',
		],
	];

	/**
	 * @param Config|null $config
	 * @throws MWException
	 */
	public function __construct( Config $config = null ) {
	}

	/**
	 * Parser callback for <armadillo> tag
	 * @param string $input
	 * @param array $args
	 * @param Parser $parser
	 * @return string
	 */
	public static function parserHook( $input, $args, $parser ) {
		$widget = new Armadillo();
		return $widget->render( $input, $args, $parser );
	}

	/**
	 * Render widget
	 * @param string $input
	 * @return string converted code
	 */
	public function render( $input, $args, $parser ) {
		$args = array_merge( self::DEFAULT_ARGS, $args );
		$name = $args[ 'name' ];
		$location = $args[ 'location' ];
		$props = [];
		$error = false;
		$tags = array_merge( self::DEFAULT_TAGS, ExtensionRegistry::getInstance()->getAttribute(
			'ArmadilloTags'
		) );
		$validComponent = $tags[ $name ] ?? null;
		if ( $validComponent ) {
			// All other args are passed to component.
			$customArgs = $args;
			unset( $customArgs['location'] );
			unset( $customArgs['name'] );

			$props = array_merge( $customArgs, [
				'titles' => array_values(
					array_filter( array_map(
						static function ( $str ) {
							return trim( $str );
						},
						explode( '*', $input )
					), static function ( $str ) {
						return !!$str;
					} )
				),
			] );
		} else {
			return Html::warningBox( 'Unknown armadillo component' );
		}
		$pOut = $parser->getOutput();
		$widget = new ArmadilloWidget( $name, $props, $args['location'], $validComponent );
		$widgets = $pOut->getExtensionData( 'armadillo' ) ?? [];
		$widgets[] = $widget;
		$widgets = $pOut->setExtensionData( 'armadillo', $widgets );
		if ( $location !== self::LOCATION_ARTICLE ) {
			// this content is not displayed in article.
			return '';
		} else {
			$html = $widget->toHTML();
			return $html;
		}
	}
}
