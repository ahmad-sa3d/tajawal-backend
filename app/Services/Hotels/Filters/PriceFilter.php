<?php
namespace App\Services\Hotels\Filters;

use App\Services\Hotels\FilterContract;

class PriceFilter implements FilterContract
{
	/**
	 * Hotel Min price to apply filter againest it's value
	 * @var float
	 */
	protected $min;

	/**
	 * Hotel Max price to apply filter againest it's value
	 * @var float
	 */
	protected $max;

	/**
	 * Constructor
	 * @param float $min hotel minimum price
	 */
	public function __construct($min, $max = null)
	{
		$this->min = $min > 0 ? $min : 0;
		$this->max = $max > $this->min ? $max : $min;
	}
	
	/**
	 * Apply Filter
	 *
	 * this method is where filter implementation occures
	 * 
	 * @param  array $hotel hotel to apply filter to
	 * @return boolean        true if filter succeeded, false if not
	 */
	public function apply($hotel)
	{
		if (!$this->min) {
			return true;
		}

		return $hotel['price'] >= $this->min && $hotel['price'] <= $this->max;
	}
}