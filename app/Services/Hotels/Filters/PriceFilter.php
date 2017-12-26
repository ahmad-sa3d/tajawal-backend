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
		if (!$min) {
			$this->min = $this->max = 0;
			return;
		}

		$min_max = array_sort(explode(':', $min));
		$this->min = $min_max[0] < 0 ? 0 : $min_max[0];
		$this->max = count($min_max) > 1 ? $min_max[1] : $max;

		if(!$this->max) {
			$this->max = $this->min;
		}

		$this->checkRealMinMax();
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

	/**
	 * Check which really min and max value
	 * and reassign values to make sure min is the least value
	 * and max is the greatest value
	 * @return void
	 */
	private function checkRealMinMax()
	{
		if($this->max < $this->min) {
			$max = $this->max;
			$this->max = $this->min;
			$this->min = $max;
		}
	}
}