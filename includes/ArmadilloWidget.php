<?php

namespace Armadillo;

use Html;

/**
 * @internal
 */
class ArmadilloWidget {
	public $name;
	public $props;
	public $location;
	public $moduleProps;

	/**
	 * @param string $name
	 * @param array $props
	 * @param string $location
	 * @param string $moduleProps
	 */
	public function __construct( string $name, array $props, string $location, array $moduleProps ) {
		$this->name = $name;
		$this->props = $props;
		$this->location = $location;
		$this->moduleProps = $moduleProps;
	}

	public function toHTML() {
		$moduleProps = $this->moduleProps + [
			'aspect-ratio' => '1/1'
		];
		$html = Html::openElement( 'div', [
			'class' => 'armadillo-widget',
			'style' => 'aspect-ratio:' . $moduleProps[ 'aspect-ratio' ],
		] );
		$fallback = Html::element( 'noscript', [], 'JavaScript required to see this content' );
		$html .= Html::rawElement(
			'armadillo',
			[
				'data-module' => $moduleProps[ 'module' ],
				'data-props' => json_encode( $this->props ),
			],
			$fallback
		);
		$html .= Html::closeElement( 'div' );
		return $html;
	}
}
