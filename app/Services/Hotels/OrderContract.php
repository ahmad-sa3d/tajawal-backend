<?php
namespace App\Services\Hotels;

/**
 * @codeCoverageIgnore
 */
interface OrderContract {
	/**
	 * Method that responsible to implementing how to apply order
	 * @param  array $hotel hotel data
	 * @return Mix        value to sort by
	 */
	public function orderBy($hotel);

	/**
	 * Set order direction to be Ascending
	 * @return OrderContract instance
	 */
	public function asc();

	/**
	 * Set order direction to be Descending
	 * @return OrderContract instance
	 */
	public function desc();

	/**
	 * Check if order direction is Ascending
	 * @return Boolean
	 */
	public function isAscOrder();
	
	/**
	 * Check if order direction is Descending
	 * @return Boolean
	 */
	public function isDescOrder();
}