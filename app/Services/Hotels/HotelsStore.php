<?php
namespace App\Services\Hotels;

use App\Services\Hotels\Exceptions\FilterDuplicatedException;
use App\Services\Hotels\FilterContract;
use App\Services\Hotels\OrderContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HotelsStore {

	/**
	 * Hotels Collection
	 * @var Illuminate\Support\Collection
	 */
	protected $hotels;

	/**
	 * Filters Collection
	 * @var Illuminate\Support\Collection
	 */
	protected $filters;

	/**
	 * Added Filters Types Array
	 * @var array
	 */
	protected $appliedFilterTypes = [];

	/**
	 * Instantiate New Hotels
	 * 
	 * @param array|Collection $hotels Hotels
	 */
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

	/**
	 * Get Hotels Collection
	 * @return Collection Hotels Collection
	 */
	public function get()
	{
		return $this->hotels;
	}
	
	/**
	 * Get Total Hotels Count
	 * @return Integer Hotels Count
	 */
	public function total()
	{
		return $this->hotels->count();
	}

	/**
	 * Add New Filter to hotels
	 * @param FilterContract $filter Filter To Add
	 */
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

	/**
	 * Get Added Filters Count
	 * @return Integer Total Filters Count
	 */
	public function getFiltersCount()
	{
		return $this->hasFilters() ? $this->filters->count() : 0;
	}

	/**
	 * Remove Added Filters
	 * @return HoteslStore current instance
	 */
	public function removeFilters()
	{
		$this->filters = collect();
		$this->appliedFilterTypes = [];

		return $this;
	}

	/**
	 * Apply Added Filters to hotels if exists
	 * and return new instance with filtered hotels
	 * 
	 * @return HotelsStore instance with filtered hotels
	 */
	public function applyFilters()
	{
		if (!$this->hasFilters()) {
			return $this;
		}

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

	/**
	 * Order Hotels By
	 * @param  OrderContract $order Order
	 * @return [type]               [description]
	 */
	public function orderBy(OrderContract $order)
	{
		$orderMethod = $order->isDescOrder() ? 'sortByDesc' : 'sortBy';

		$this->hotels = $this->hotels->$orderMethod(function($hotel, $key) use($order){
			return $order->orderBy($hotel);
		})->values();

		return $this;
	}

	/**
	 * Paginate Hotels Connection
	 * @param  integer $per_page     Number of hotels per page
	 * @param  integer $current_page Current Page
	 * @param  array   $options      LengthAwarePaginator Options
	 * @return LengthAwarePaginator  LengthAwarePaginator Paginator instance
	 */
	public function paginate($per_page = 10, $current_page = 1, array $options = [])
	{
		$per_page = $per_page <= 0 ? 10 : $per_page;
		$skip = ($current_page - 1) * $per_page;
		$sliced_hotels = $this->hotels->slice($skip, $per_page);
		return new LengthAwarePaginator($sliced_hotels, $this->total(), $per_page, $current_page, $options);
	}

	/**
	 * Check if there are flters added
	 * @return boolean true if there are filters added or false if not
	 */
	private function hasFilters()
	{
		return !!($this->filters && $this->filters->count());
	}

	/**
	 * Check if given filter namespace is already added
	 *
	 * prevent adding the same filter more than once
	 * 
	 * @param  String $filter_class_name Filter full namespace
	 * @return void                    
	 * @throws FilterDuplicatedException if filter is already added
	 */
	private function checkAgainestFilterDuplication($filter_class_name)
	{
		if ($this->hasFilters()) {
			if (in_array($filter_class_name, $this->appliedFilterTypes)) {
				throw new FilterDuplicatedException;
			}
		}
	}

	/**
	 * Magic Method
	 *
	 * used to get protected properties as read only
	 * 
	 * @param  String $property Called Property
	 * @return Mix           Property corresponding value
	 * @throws RuntimeException if called property doesnot have corresponding value to return 
	 */
	public function __get($property)
	{
		if ($property == 'hotels') {
			return $this->hotels;
		} else {
			throw new \RuntimeException('undefined property ' . __CLASS__ . '::$' . $property);
		}
	}
}

