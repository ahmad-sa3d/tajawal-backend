<?php
namespace App\Services\Hotels\Filters;

use App\Services\Hotels\Exceptions\InvalidDateException;
use App\Services\Hotels\FilterContract;
use Illuminate\Support\Facades\Validator;

class DateFilter implements FilterContract
{
	/**
	 * Start Date Range timestamp
	 * @var integer
	 */
	protected $from;

	/**
	 * End Date range timestamp
	 * @var integer
	 */
	protected $to;

	/**
	 * Constructor
	 * @param string $from date range start
	 * @param string $to date range end
	 */
	public function __construct($from, $to = null)
	{
		if (!$from) return;

		$from_to = explode(':', $from);
		$from = $from_to[0];
		$to = count($from_to) > 1 ? $from_to[1] : $to;

		$this->validateInputs($from, $to);

		$this->from = strtotime($from);
		$this->to = $to ? strtotime($to) : $this->from;

		if ($this->to < $this->from) {
			$to = $this->from;
			$this->from = $this->to;
			$this->to = $to;
		}
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
		if (!$this->from) {
			return true;
		}

		if (!$this->hasAvailabilityPeriods($hotel)) {
			return false;
		}

		return $this->isAvailable($hotel);
	}

	/**
	 * Validate Date Range
	 * @param  string $from start range
	 * @param  string $to   end range
	 * @return void
	 */
	private function validateInputs($from, $to)
	{
		$validator = Validator::make([
			'from' => $from,
			'to' => $to
		], [
			'from' => 'required|date_format:d-m-Y',
			'to' => 'nullable|date_format:d-m-Y',
		]);

		if ($validator->fails()) {
			throw new InvalidDateException(__METHOD__ . ' ' . $validator->errors()->first());
		}
	}

	/**
	 * Check if hotel has available periods or not
	 * @param  array  $hotel hotel to check
	 * @return boolean
	 */
	private function hasAvailabilityPeriods($hotel)
	{
		return (!isset($hotel['availability']) || !is_array($hotel['availability'])) ? false : true;
	}

	/**
	 * Check if Hotel is available or not
	 * @param  array  $hotel Hotel
	 * @return boolean
	 */
	private function isAvailable($hotel)
	{
		$is_available = false;

		foreach ($hotel['availability'] as $period) {
			if ($this->from >= strtotime($period['from']) && $this->to <= strtotime($period['to'])) {
				$is_available = true;
				break;
			}
		}

		return $is_available;
	}
}