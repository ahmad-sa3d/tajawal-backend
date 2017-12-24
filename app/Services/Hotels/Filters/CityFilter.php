<?php
namespace App\Services\Hotels\Filters;

use App\Services\Hotels\FilterContract;

class CityFilter implements FilterContract
{

	protected $hotel_city = '';

	public function __construct($hotel_city)
	{
		$this->hotel_city = strtolower(trim($hotel_city));
	}
	
	public function apply($hotel)
	{
		if(!strlen($this->hotel_city))
			return true;

		return !! preg_match('/'.$this->hotel_city.'/i', $hotel['city']);
	}

}