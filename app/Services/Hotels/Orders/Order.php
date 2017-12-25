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
	 * Apply Order By
	 * @return Mix Value to order Againest
	 */
	abstract public function orderBy($hotel);

	/**
	 * Order in Asccednding direction
	 * @return OrderContract instance
	 */
	public function asc()
	{
		$this->orderDirection = 'ASC';
		return $this;
	}

	/**
	 * Order in Asccednding direction
	 * @return OrderContract instance
	 */
	public function desc()
	{
		$this->orderDirection = 'DESC';
		return $this;
	}

	/**
	 * Check if order direction is ASC
	 * @return boolean
	 */
	public function isAscOrder()
	{
		return ! $this->isDescOrder();
	}

	/**
	 * Check if order direction is DESC
	 * @return boolean
	 */
	public function isDescOrder()
	{
		return !!($this->orderDirection == 'DESC');
	}
}