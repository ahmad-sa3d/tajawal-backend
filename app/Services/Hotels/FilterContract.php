<?php
namespace App\Services\Hotels;

/**
 * @codeCoverageIgnore
 */
interface FilterContract {

	public function apply($hotel);
	
}