<?php
namespace App\Services\Hotels;

use App\Services\Hotels\Exceptions\FilterDuplicatedException;
use App\Services\Hotels\FilterContract;
use Illuminate\Support\Collection;

class HotelsStore {

	protected $hotels;
	protected $filters;
	protected $appliedFilterTypes = [];

	public function __construct($hotels)
	{
		if ($hotels instanceof Collection){
			$this->hotels = $hotels;
		} else if (is_array($hotels)){
			$this->hotels = collect($hotels);
		} else {
			throw new \Exception(__METHOD__ . ' should accept collection or array of hotels');
		}
	}
	
	public function total()
	{
		return $this->hotels->count();
	}

	public function addFilter(FilterContract $filter)
	{
		if(!$this->filters)
			$this->filters = collect();

		$filter_class_name = get_class($filter);
		$this->checkAgainestFilterDuplication($filter_class_name);

		$this->filters->push($filter);
		$this->appliedFilterTypes[] = $filter_class_name;

		return $this;
	}

	public function getFiltersCount()
	{
		return $this->hasFilters() ? $this->filters->count() : 0;
	}

	public function removeFilters()
	{
		$this->filters = collect();

		return $this;
	}

	public function applyFilters()
	{
		if(!$this->hasFilters())
			return $this;

		$filtered = $this->hotels->filter(function($hotel, $key){
			// Apply Filters
			$matching = true;

			$this->filters->each(function($filter) use($hotel, &$matching){
				if (!$filter->apply($hotel)) {
					$matching = false;
					return false;
				}
			});

			return $matching;
		});

		return new self($filtered);
	}

	private function hasFilters()
	{
		return !!($this->filters && $this->filters->count());
	}

	private function checkAgainestFilterDuplication($filter_class_name)
	{
		if($this->hasFilters()){
			if(in_array($filter_class_name, $this->appliedFilterTypes))
				throw new FilterDuplicatedException;
		}
	}

	public function first($value='')
	{
		# code...
	}

	public function __get($property)
	{
		if($property == 'hotels')
			return $this->hotels;
		else
			throw new \RuntimeException('undefined property ' . __CLASS__ . '::$' .$property);
	}
}

