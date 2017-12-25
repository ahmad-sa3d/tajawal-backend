<?php

namespace Tests\Unit;

use App\Services\Hotels\Filters\CityFilter;
use App\Services\Hotels\Filters\DateFilter;
use App\Services\Hotels\Filters\NameFilter;
use App\Services\Hotels\Filters\PriceFilter;
use App\Services\Hotels\HotelsStore;
use App\Services\Hotels\Orders\NameOrder;
use App\Services\Hotels\Orders\PriceOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelsTest extends TestCase
{
	protected $hotelsStore;
	protected $originalDataCount;

	public function setUp()
	{
        parent::setUp();
		$data = [
			[
				"name" => "Media One Hotel",
				"price" => 102.2,
				"city" => "dubai",
				"availability" => [
					[
						"from" => "10-10-2020",
						"to" => "15-10-2020"
					],[
						"from" => "25-10-2020",
						"to" => "15-11-2020"
					], [
						"from" => "10-12-2020",
						"to" => "15-12-2020"
					]
				]
			], [
				"name" => "Rotana Hotel",
				"price" => 80.6,
				"city" => "cairo",
				"availability" => [
					[
						"from" => "10-10-2011",
						"to" => "12-10-2011"
					], [
						"from" => "25-10-2011",
						"to" => "10-11-2011"
					], [
						"from" => "05-12-2020",
						"to" => "18-12-2020"
					]
				]
			], [
				"name" => "Another Hotel",
				"price" => 180.6,
				"city" => "cairo",
				"availability" => [
					[
						"from" => "10-10-2020",
						"to" => "12-10-2020"
					], [
						"from" => "25-10-2020",
						"to" => "10-11-2020"
					], [
						"from" => "05-12-2020",
						"to" => "18-12-2020"
					]
				]
			]
		];

		$this->originalDataCount = count($data);
		$this->hotelsStore = new HotelsStore($data);
	}

    /** @test */
    public function it_has_total()
    {
        $this->assertEquals($this->originalDataCount, $this->hotelsStore->total());
    }

    /** @test */
    public function it_returns_all_hotels()
    {
        $this->assertEquals($this->originalDataCount, $this->hotelsStore->hotels->count());
    }

    /** @test */
    public function it_can_detect_filters_count_if_no_filters_added()
    {
        $this->assertEquals(0, $this->hotelsStore->getFiltersCount());
    }


    /** @test */
    public function it_can_add_filter()
    {
    	$this->hotelsStore->addFilter(new NameFilter('media'));

        $this->assertEquals(1, $this->hotelsStore->getFiltersCount());
    }

    /** @test */
    public function it_can_add_more_than_one_filter()
    {
    	$this->hotelsStore->addFilter(new NameFilter('media'));
        $this->assertEquals(1, $this->hotelsStore->getFiltersCount());

        $this->hotelsStore->addFilter(new CityFilter('dubai'));
        $this->assertEquals(2, $this->hotelsStore->getFiltersCount());
    }

    /** @test */
    public function it_cannot_add_the_same_filter_more_than_once()
    {
    	$this->hotelsStore->addFilter(new NameFilter('media'));

        $this->expectException('App\Services\Hotels\Exceptions\FilterDuplicatedException');

    	$this->hotelsStore->addFilter(new NameFilter('media'));
    }

    /** @test */
    public function it_can_reset_filters()
    {
    	$this->hotelsStore->addFilter(new NameFilter('media'));
        $this->assertEquals(1, $this->hotelsStore->getFiltersCount());

        $this->hotelsStore->removeFilters();
        $this->assertEquals(0, $this->hotelsStore->getFiltersCount());
    }

    /** @test */
    public function it_can_reset_filter_and_re_add_same_filter_again()
    {
        $this->hotelsStore->addFilter(new NameFilter('media'));
        $this->assertEquals(1, $this->hotelsStore->getFiltersCount());

        $this->hotelsStore->removeFilters();
        $this->assertEquals(0, $this->hotelsStore->getFiltersCount());

        $this->hotelsStore->addFilter(new NameFilter('media'));
        $this->assertEquals(1, $this->hotelsStore->getFiltersCount());
    }

    /** @test */
    public function it_can_reset_filters_and_re_add_same_filters_again()
    {
        $this->hotelsStore->addFilter(new NameFilter('media'));
        $this->hotelsStore->addFilter(new CityFilter('media'));
        $this->assertEquals(2, $this->hotelsStore->getFiltersCount());

        $this->hotelsStore->removeFilters();
        $this->assertEquals(0, $this->hotelsStore->getFiltersCount());

        $this->hotelsStore->addFilter(new NameFilter('media'));
        $this->hotelsStore->addFilter(new CityFilter('media'));

        $this->assertEquals(2, $this->hotelsStore->getFiltersCount());
    }

    /** @test */
    public function it_can_call_apply_filters_while_no_filters_applied()
    {
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals($this->originalDataCount, $this->hotelsStore->total());
        $this->assertEquals($this->originalDataCount, $filtered->total());
        $this->assertEquals($this->hotelsStore->total(), $filtered->total());
    }

    /** @test */
    public function it_returns_no_hotels_when_filter_by_none_existing_hotel_name()
    {
    	$this->hotelsStore->addFilter(new NameFilter('keyword'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_when_filter_by_existing_hotel_name()
    {
    	$this->hotelsStore->addFilter(new NameFilter('media'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());
        $this->assertEquals('Media One Hotel', $filtered->hotels->first()['name']);

        $this->hotelsStore->removeFilters();
    	$this->hotelsStore->addFilter(new NameFilter('hotel'));
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals($this->originalDataCount, $filtered->total());
    }

    /** @test */
    public function it_returns_no_hotels_when_filter_by_none_existing_hotel_city()
    {
    	$this->hotelsStore->addFilter(new CityFilter('keyword'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    public function it_returns_hotels_when_filter_by_existing_hotel_city()
    {
    	$this->hotelsStore->addFilter(new CityFilter('dubai'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());
        $this->assertEquals('dubai', $filtered->hotels->first()['dubai']);

        $this->hotelsStore->removeFilters();
    	$this->hotelsStore->addFilter(new CityFilter('cairo'));
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertNotEquals('dubai', $filtered->first()['city']);
    }

    /** @test */
    public function it_returns_no_hotels_when_filter_by_none_existing_price()
    {
    	$this->hotelsStore->addFilter(new PriceFilter(80));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    /** @test */
    public function it_returns_no_hotels_when_filter_by_none_existing_price_range()
    {
    	$this->hotelsStore->addFilter(new PriceFilter(200, 300));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    /** @test */
    public function it_returns_no_hotels_when_filter_by_none_existing_price_range_as_single_value()
    {
        $this->hotelsStore->addFilter(new PriceFilter('200:300'));
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());

        $this->hotelsStore->removeFilters();

        $this->hotelsStore->addFilter(new PriceFilter('80:110'));
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(2, $filtered->total());
        $this->assertNotEquals('Another Hotel', $filtered->hotels->first()['name']);

        $this->hotelsStore->removeFilters();

        $this->hotelsStore->addFilter(new PriceFilter('110:80'));
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(2, $filtered->total());
        $this->assertNotEquals('Another Hotel', $filtered->hotels->first()['name']);
    }

    public function it_returns_hotels_when_filter_by_existing_price()
    {
    	$this->hotelsStore->addFilter(new PriceFilter(80.6));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());
        $this->assertEquals('Rotana Hotel', $filtered->hotels->first()['name']);
    }

    public function it_returns_hotels_when_filter_by_existing_price_range()
    {
    	$this->hotelsStore->addFilter(new PriceFilter(80, 110));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(2, $filtered->total());
        $this->assertNotEquals('Another Hotel', $filtered->hotels->first()['name']);
    }

    /** @test */
    public function it_validates_date_filter_date_value()
    {
        $this->expectException('App\Services\Hotels\Exceptions\InvalidDateException');
        $this->hotelsStore->addFilter(new DateFilter('10-2010-10'));

        $this->expectException('App\Services\Hotels\Exceptions\InvalidDateException');
        $this->hotelsStore->addFilter(new DateFilter('10-01-2020', '12-10-10'));
    }

    /** @test */
    public function it_returns_no_hotels_when_filter_by_none_existing_date()
    {
    	$this->hotelsStore->addFilter(new DateFilter('10-01-2010'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_when_filter_by_existing_date()
    {
    	// "from" => "10-10-2011",
		// "to" => "12-10-2011"
	
		// "from" => "25-10-2011",
		// "to" => "10-11-2011"
		
    	$this->hotelsStore->addFilter(new DateFilter('10-11-2011'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());

        $this->hotelsStore->removeFilters();

        $this->hotelsStore->addFilter(new DateFilter('10-10-2011'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());

        $this->hotelsStore->removeFilters();

        $this->hotelsStore->addFilter(new DateFilter('12-10-2011'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());

        $this->hotelsStore->removeFilters();

        $this->hotelsStore->addFilter(new DateFilter('25-10-2011'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_when_filter_by_existing_date_in_range()
    {
    	// "from" => "10-10-2011",
		// "to" => "12-10-2011"
	
		// "from" => "25-10-2011",
		// "to" => "10-11-2011"
		
    	$this->hotelsStore->addFilter(new DateFilter('11-10-2011'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());

        $this->hotelsStore->removeFilters();

        $this->hotelsStore->addFilter(new DateFilter('05-11-2011'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());

        $this->hotelsStore->removeFilters();

        $this->hotelsStore->addFilter(new DateFilter('10-10-2020', '12-10-2020'));
    	$filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(2, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_when_filter_by_existing_date_in_range_as_single_argument()
    {
        $this->hotelsStore->addFilter(new DateFilter('10-10-2020:12-10-2020'));
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(2, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_when_filter_by_existing_date_in_range_as_single_argument_reversed()
    {
        $this->hotelsStore->addFilter(new DateFilter('12-10-2020:10-10-2020'));
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(2, $filtered->total());
    }

    /** @test */
    public function it_returns_no_hotels_while_filter_by_not_existing_city_and_name_combination_together()
    {
        $this->hotelsStore->addFilter(new NameFilter('media')); // 1 record
        $this->hotelsStore->addFilter(new CityFilter('cairo')); // another record
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_while_filter_by_exisisting_city_and_name_combination_together()
    {
        $this->hotelsStore->addFilter(new NameFilter('media')); // 1 record
        $this->hotelsStore->addFilter(new CityFilter('dubai')); // same record
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());
        $this->assertEquals('dubai', $filtered->hotels->first()['city']);

        $this->hotelsStore->removeFilters();

        $this->hotelsStore->addFilter(new NameFilter('hotel')); // 3 records
        $this->hotelsStore->addFilter(new CityFilter('cairo')); // 2 records
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(2, $filtered->total());
    }

    /** @test */
    public function it_returns_no_hotels_while_filter_by_not_existing_city_and_price_combination_together()
    {
        $this->hotelsStore->addFilter(new PriceFilter(80.6)); // 1 record
        $this->hotelsStore->addFilter(new CityFilter('dubai')); // another record
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_while_filter_by_exisisting_city_and_price_combination_together()
    {
        $this->hotelsStore->addFilter(new PriceFilter(80.6)); // 1 record
        $this->hotelsStore->addFilter(new CityFilter('cairo')); // same record
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());
        $this->assertEquals('cairo', $filtered->hotels->first()['city']);
    }

    /** @test */
    public function it_returns_no_hotels_while_filter_by_not_existing_name_and_city_and_price_combination_together()
    {
        $this->hotelsStore->addFilter(new PriceFilter(80.6)); // 1 record
        $this->hotelsStore->addFilter(new CityFilter('dubai')); // 2 records
        $this->hotelsStore->addFilter(new NameFilter('hotel')); // 3 records
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_while_filter_by_existing_name_and_city_and_price_combination_together()
    {
        $this->hotelsStore->addFilter(new PriceFilter(80.6)); // 1 record
        $this->hotelsStore->addFilter(new CityFilter('cairo')); // 2 records
        $this->hotelsStore->addFilter(new NameFilter('rotana')); // 3 records
        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());
    }

    /** @test */
    public function it_returns_no_hotels_while_filter_by_not_existing_name_and_city_and_price_and_date_combination_together()
    {
        $this->hotelsStore->addFilter(new PriceFilter(80.6)); // 1 record
        $this->hotelsStore->addFilter(new CityFilter('dubai')); // 2 records
        $this->hotelsStore->addFilter(new NameFilter('hotel')); // 3 records
        $this->hotelsStore->addFilter(new DateFilter('11-10-2011')); // 1 record

        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(0, $filtered->total());
    }

    /** @test */
    public function it_returns_hotels_while_filter_by_existing_name_and_city_and_price_and_date_combination_together()
    {
        $this->hotelsStore->addFilter(new PriceFilter(80.6)); // 1 record
        $this->hotelsStore->addFilter(new CityFilter('cairo')); // 2 records
        $this->hotelsStore->addFilter(new NameFilter('rotana')); // 1 records
        $this->hotelsStore->addFilter(new DateFilter('11-10-2011')); // 1 record

        $filtered = $this->hotelsStore->applyFilters();
        $this->assertEquals(1, $filtered->total());
    }

    /** @test */
    public function it_can_be_ordered_by_name_by_default_ascending()
    {
        $this->hotelsStore->orderBy(new NameOrder());
        $this->assertEquals('Another Hotel', $this->hotelsStore->hotels->first()['name']);
    }

    /** @test */
    public function it_can_be_ordered_by_name_ascending()
    {
        $this->hotelsStore->orderBy((new NameOrder())->asc());
        $this->assertEquals('Another Hotel', $this->hotelsStore->hotels->first()['name']);
    }

    /** @test */
    public function it_can_be_ordered_by_name_descending()
    {
        $this->hotelsStore->orderBy((new NameOrder())->desc());
        $this->assertEquals('Rotana Hotel', $this->hotelsStore->hotels->first()['name']);
    }

    /** @test */
    public function it_can_be_ordered_by_price_by_default_ascending()
    {
        $this->hotelsStore->orderBy(new PriceOrder());
        $this->assertEquals(80.6, $this->hotelsStore->hotels->first()['price']);
    }

    /** @test */
    public function it_can_be_ordered_by_price_ascending()
    {
        $this->hotelsStore->orderBy((new PriceOrder())->asc());
        $this->assertEquals(80.6, $this->hotelsStore->hotels->first()['price']);
    }

    /** @test */
    public function it_can_be_ordered_by_price_desc()
    {
        $this->hotelsStore->orderBy((new PriceOrder())->desc());
        $this->assertEquals(180.6, $this->hotelsStore->hotels->first()['price']);
    }

}
