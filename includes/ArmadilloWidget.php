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

	/**
	 * @param string $name
	 * @param array $props
	 * @param string $location
	 */
	public function __construct( string $name, array $props, string $location ) {
		$this->name = $name;
		$this->props = $props;
		$this->location = $location;
	}

	public function toHTML() {
		$html = Html::openElement( 'div', [
			'class' => 'armadillo-widget',
		] );
		$fallback = Html::element( 'noscript', [], 'JavaScript required to see this content' );
		$html .= Html::rawElement(
			'armadillo:' . $this->name,
			[
				'data-props' => json_encode( $this->props ),
			],
			$fallback
		);
		$html .= Html::closeElement( 'div' );
		return $html;
	}
}
