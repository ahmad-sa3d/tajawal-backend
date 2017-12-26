<?php
namespace App\Services\Hotels\Orders;

use App\Services\Hotels\OrderContract;

abstract class Order implements OrderContract
{
	/**
	 * Order Direction
	 * @var string
	 */
	protected $orderDirection = 'ASC';

	/**
	 * Instantiate new order with direction
	 * @param string $order_direction direction asc|desc
	 */
	public function __construct($order_direction = 'asc')
	{
		if (strtolower($order_direction) == 'desc') {
			$this->desc();
		} else {
			$this->asc();
		}
	}

	/**
	 * Apply Order By
	 * @return Mix Value to order Againest
	 */
	abstract public function orderBy($hotel);

	/**
	 * Order in Asccednding direction
	 * @return OrderContract instance
	 */
	final public function asc()
	{
		$this->orderDirection = 'ASC';
		return $this;
	}

	/**
	 * Order in Asccednding direction
	 * @return OrderContract instance
	 */
	final public function desc()
	{
		$this->orderDirection = 'DESC';
		return $this;
	}

	/**
	 * Check if order direction is ASC
	 * @return boolean
	 */
	final public function isAscOrder()
	{
		return ! $this->isDescOrder();
	}

	/**
	 * Check if order direction is DESC
	 * @return boolean
	 */
	final public function isDescOrder()
	{
		return !!($this->orderDirection == 'DESC');
	}
}