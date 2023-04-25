<?php

namespace Armadillo;

use Html;

class Armadillo {
	private const DEFAULT_ARGS = [
		'name' => null,
		'location' => 'article',
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
		$props = [];
		$error = false;
		switch ( $name ) {
			case 'see-also':
				$props = [
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
				];
				break;
			default:
				return Html::warningBox( 'Unknown armadillo component' );
				break;
		}
		$pOut = $parser->getOutput();
		$widget = new ArmadilloWidget( $name, $props, $args['location'] );
		$html = $widget->toHTML();
		$widgets = $pOut->getExtensionData( 'armadillo' ) ?? [];
		$widgets[] = $widget;
		$widgets = $pOut->setExtensionData( 'armadillo', $widgets );
		return $html;
	}
}
