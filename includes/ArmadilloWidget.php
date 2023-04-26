<?php

namespace Armadillo;

use Html;
use Parser;
use DateTime;

/**
 * @internal
 */
class ArmadilloWidget {
	public $name;
	public $props;
	public $location;
	public $moduleProps;
	public $parser;

	/**
	 * @param string $name
	 * @param array $props
	 * @param string $location
	 * @param string $moduleProps
	 * @param Parser $parser
	 */
	public function __construct( string $name, array $props, string $location, array $moduleProps, Parser $parser ) {
		$this->name = $name;
		$this->props = $props;
		$this->location = $location;
		$this->moduleProps = $moduleProps;
		$this->parser = $parser;
	}

	public function toHTML() {
		$moduleProps = $this->moduleProps + [
			'aspect-ratio' => '1/1'
		];
		$aRatio = $moduleProps[ 'aspect-ratio' ];
		$parts = explode( '/', $aRatio );
		$props = $this->props;
		$numTitles = count( $props['titles'] );
		if ( $numTitles > 0 ) {
			$height = (int)trim( $parts[1] ) * $numTitles;
			$maxHeight = 'max-height:' . $height . 'px;';
		} else {
			$maxHeight = '';
		}
		$callback = $this->moduleProps[ 'callback' ] ?? null;
		$class = 'armadillo-widget';
		if ( $callback ) {
			$fallback = call_user_func( $callback, $props );
			$class .= ' armadillo-widget-loaded';
		} else {
			$fallback = Html::element( 'noscript', [], 'JavaScript required to see this content' );
		}
		$html = Html::openElement( 'div', [
			'class' => $class,
			'style' => 'aspect-ratio:' . $aRatio . ';' . $maxHeight,
		] );
		if ( $this->name === 'current-page-view-graph' ) {
			$html .= $this->getCurrentPageViewGraphHTML();
		} else {
			$html .= Html::rawElement(
				'armadillo',
				[
					'data-name' => $this->name,
					'data-module' => $moduleProps['module'],
					'data-props' => json_encode( $props ),
				],
				$fallback
			);
		}
		$html .= Html::closeElement( 'div' );
		return $html;
	}

	private function getCurrentPageViewGraphHTML() {
		$start = date( 'Ymd', strtotime( '-7 days' ) );
		$end = date( 'Ymd', time() );
		$project = $this->parser->getTitle()->getNamespace() === NS_MAIN ? 'en.wikipedia.org' : 'en.wikipedia.org';
		$pageViews = json_decode( file_get_contents(
			'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/'
			. $project . '/all-access/all-agents/'
			. $this->parser->getTitle()->getPrefixedDBkey()
			. '/daily/' . $start . '/' . $end
		) );
		$dataValues = [];
		if ( $pageViews ) {
			foreach ( $pageViews->items as $item ) {
				$dataValues[] = [
					'category' => date( 'Y-m-d',
						DateTime::createFromFormat( 'Ymd00', $item->timestamp )->getTimestamp() ),
					'amount' => $item->views,
				];
			}
		}

		$input = '<graph>';
		$input .= '{
  "$schema": "https://vega.github.io/schema/vega/v5.json",
  "description": "Current Page Views",
  "width": 500,
  "height": 300,
  "padding": 5,

  "data": [
    {
      "name": "table",
      "values": ' . json_encode( $dataValues ) . '
    }
  ],

  "signals": [
    {
      "name": "tooltip",
      "value": {},
      "on": [
        {"events": "rect:mouseover", "update": "datum"},
        {"events": "rect:mouseout",  "update": "{}"}
      ]
    }
  ],

  "scales": [
    {
      "name": "xscale",
      "type": "band",
      "domain": {"data": "table", "field": "category"},
      "range": "width",
      "padding": 0.05,
      "round": true
    },
    {
      "name": "yscale",
      "domain": {"data": "table", "field": "amount"},
      "nice": true,
      "range": "height"
    }
  ],

  "axes": [
    { "orient": "bottom", "scale": "xscale" },
    { "orient": "left", "scale": "yscale" }
  ],

  "marks": [
    {
      "type": "rect",
      "from": {"data":"table"},
      "encode": {
        "enter": {
          "x": {"scale": "xscale", "field": "category"},
          "width": {"scale": "xscale", "band": 1},
          "y": {"scale": "yscale", "field": "amount"},
          "y2": {"scale": "yscale", "value": 0}
        },
        "update": {
          "fill": {"value": "steelblue"}
        },
        "hover": {
          "fill": {"value": "red"}
        }
      }
    },
    {
      "type": "text",
      "encode": {
        "enter": {
          "align": {"value": "center"},
          "baseline": {"value": "bottom"},
          "fill": {"value": "#333"}
        },
        "update": {
          "x": {"scale": "xscale", "signal": "tooltip.category", "band": 0.5},
          "y": {"scale": "yscale", "signal": "tooltip.amount", "offset": -2},
          "text": {"signal": "tooltip.amount"},
          "fillOpacity": [
            {"test": "datum === tooltip", "value": 0},
            {"value": 1}
          ]
        }
      }
    }
  ]
}';
		$input .= '</graph>';

		return $this->parser->recursiveTagParseFully( $input );
	}
}
