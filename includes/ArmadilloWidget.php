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
	 * @param string $moduleName
	 */
	public function __construct( string $name, array $props, string $location, string $moduleName ) {
		$this->name = $name;
		$this->props = $props;
		$this->location = $location;
		$this->moduleName = $moduleName;
	}

	public function toHTML() {
		$html = Html::openElement( 'div', [
			'class' => 'armadillo-widget',
		] );
		$fallback = Html::element( 'noscript', [], 'JavaScript required to see this content' );
		$html .= Html::rawElement(
			'armadillo',
			[
				'data-module' => $this->moduleName,
				'data-props' => json_encode( $this->props ),
			],
			$fallback
		);
		$html .= Html::closeElement( 'div' );
		return $html;
	}
}
