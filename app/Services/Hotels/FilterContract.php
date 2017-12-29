<?php
namespace App\Services\Hotels;

/**
 * @codeCoverageIgnore
 */
interface FilterContract {

	/**
	 * Method which will hold how to implement filter
	 * 
	 * @param  array $hotel hotel
	 * @return boolean      true if hotel passes filter, false otherwise
	 */
	public function apply($hotel);
}