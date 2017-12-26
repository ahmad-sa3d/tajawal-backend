<?php
namespace App\Services\Hotels\Exceptions;

use Exception;

class InvalidArgumentException extends Exception
{
	public function __construct($message = null)
	{
		parent::__construct($message);
	}
}