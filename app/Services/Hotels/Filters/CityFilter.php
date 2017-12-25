<?php
namespace App\Services\Hotels\Filters;

use App\Services\Hotels\FilterContract;

class CityFilter implements FilterContract
{
	/**
	 * Hotel city name to apply filter againest it's value
	 * @var string
	 */
	protected $hotel_city = '';

	/**
	 * Constructor
	 * @param string $hotel_city hotel city name
	 */
	public function __construct($hotel_city)
	{
		$this->hotel_city = strtolower(trim($hotel_city));
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
		if (!strlen($this->hotel_city)) {
			return true;
		}

		return !! preg_match('/'.$this->hotel_city.'/i', $hotel['city']);
	}

}