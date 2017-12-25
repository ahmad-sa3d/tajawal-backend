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

		if(count($min_max) > 1){
			$this->min = $min_max[0];
			$this->max = $min_max[1];
		} else {
			$this->min = $min_max[0];
		}

		if($this->min < 0) {
			$this->min = 0;
		}

		if(!$this->max) {
			$this->max = $max ? $max : $this->min;
		}

		if($this->max < $this->min) {
			$max = $this->max;
			$this->max = $this->min;
			$this->min = $max;
		}
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