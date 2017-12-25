<?php
namespace App\Services\Hotels\Orders;

use App\Services\Hotels\Orders\Order;

class PriceOrder extends Order
{
	/**
	 * Implement how to order hotel by name
	 *
	 * Key which sorting hotels according to
	 * 
	 * @param  array $hotel hotel
	 * @return Mix        value to order hotels againest
	 */
	public function orderBy($hotel)
	{
		return $hotel['price'];
	}
}