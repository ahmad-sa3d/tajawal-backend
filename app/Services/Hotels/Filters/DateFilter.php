<?php
namespace App\Services\Hotels\Filters;

use App\Services\Hotels\Exceptions\InvalidDateException;
use App\Services\Hotels\FilterContract;
use Illuminate\Support\Facades\Validator;

class DateFilter implements FilterContract{

	protected $from;
	protected $to;

	public function __construct($from, $to = null)
	{
		// $validator = Validator::make([
		// 	'from' => 'required|date_format:d-m-Y',
		// 	'to' => 'nullable|date_format:d-m-Y',
		// ], [
		// 	'from' => $from,
		// 	'to' => $to
		// ]);

		// if($validator->fails()){
		// 	throw new InvalidDateException(__METHOD__ . $validator->errors()->first());
		// }

		$this->from = strtotime($from);
		$this->to = $to ? strtotime($to) : $this->from;
	}
	
	public function apply($hotel)
	{
		$available = false;

		if(!isset($hotel['availability']) || !is_array($hotel['availability']))
			return false;

		foreach($hotel['availability'] as $period){
			if( $this->from >= strtotime($period['from']) && $this->to <= strtotime($period['to']))
			{
				$available = true;
				break;
			}
		}

		return $available;
	}
}