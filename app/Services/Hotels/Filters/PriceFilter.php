<?php
namespace App\Services\Hotels\Filters;

use App\Services\Hotels\FilterContract;

class PriceFilter implements FilterContract{

	protected $min;
	protected $max;

	public function __construct($min, $max = null)
	{
		$this->min = $min > 0 ? $min : 0;
		$this->max = $max > $this->min ? $max : $min;
	}
	
	public function apply($hotel)
	{
		if(!$this->min)
			return true;

		return $hotel['price'] >= $this->min && $hotel['price'] <= $this->max;
	}
}