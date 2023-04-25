<?php

namespace Armadillo;

use Html;

class Armadillo {
	private const DEFAULT_ARGS = [
		'name' => null,
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
	 * Render hieroglyph text
	 *
	 * @param string $input
	 * @return string converted code
	 */
	public function render( $input, $args, $parser ) {
		$args = array_merge( self::DEFAULT_ARGS, $args );
		$name = $args[ 'name' ];
		$html = Html::openElement( 'div', [
			'class' => 'armadillo-widget',
		] );
		$fallback = Html::element( 'noscript', [], 'JavaScript required to see this content' );
		switch ( $name ) {
			case 'see-also':
				$html .= Html::rawElement(
					'armadillo:see-also',
					[
						'data-props' => json_encode( [
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
						] ),
					],
					$fallback
				);
				break;
			default:
				$html .= Html::warningBox( 'Unknown armadillo component' );
				break;
		}
		$html .= Html::closeElement( 'div' );
		return $html;
	}
}
