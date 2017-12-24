<?php
namespace App\Services\Hotels\Filters;

use App\Services\Hotels\FilterContract;

class NameFilter implements FilterContract{

	protected $hotel_name = '';

	public function __construct($hotel_name)
	{
		$this->hotel_name = strtolower(trim($hotel_name));
	}
	
	public function apply($hotel)
	{
		if(!strlen($this->hotel_name))
			return true;

		return !! preg_match('/'.$this->hotel_name.'/i', $hotel['name']);
	}
}