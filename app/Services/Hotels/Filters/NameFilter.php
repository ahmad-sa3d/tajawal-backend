<?php
namespace App\Services\Hotels\Filters;

use App\Services\Hotels\FilterContract;

class NameFilter implements FilterContract
{
	/**
	 * Hotel name to apply filter againest it's value
	 * @var string
	 */
	protected $hotel_name = '';

	/**
	 * Constructor
	 * @param string $hotel_name hotel name
	 */
	public function __construct($hotel_name)
	{
		$this->hotel_name = strtolower(trim($hotel_name));
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
		if(!strlen($this->hotel_name))
			return true;

		return !! preg_match('/'.$this->hotel_name.'/i', $hotel['name']);
	}
}