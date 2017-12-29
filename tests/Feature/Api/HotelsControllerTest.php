<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelsControllerTest extends TestCase
{
	protected $apiBaseUrl;

	/**
	 * Testing Setup
	 * executed before each test
	 */
	public function setUp()
	{
		parent::setUp();
		$this->apiBaseUrl = env('APP_API_URL') . '/v1/';
	}

    /**
     * @test
     * @vcr all_hotels
     *
     * Since External api are only one and response is unique
     * because we apply filters on our server
     * so we will record all test responses to one VCR casset called all_hotels
     * so donot panic if you see bellow we use the same vcr tab for each test
     */
    public function it_fetches_all_hotels()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels');
        $response->assertStatus(200);
        $response->assertJsonFragment([
        	"total" => 6
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_name()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'name' => 'rotana'
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
        	"name" => "Rotana Hotel",
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_name_for_non_existing_name()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'name' => 'blabla'
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
        	"total" => 0,
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_price()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'price' => 80.6
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
			"price" => 80.6,
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_price_for_non_existing_price()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'price' => 100
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
			"total" => 0,
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_city()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'city' => 'cai'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
			"city" => "cairo",
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_city_for_non_existing_city()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'city' => 'blabla'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
			"total" => 0,
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_date()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'date' => '10-10-2020'
        ]);
        $response->assertStatus(200);
        $response->assertJsonMissing([
			"total" => 0,
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_date_for_non_existing_date()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'date' => '10-01-2010'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
			"total" => 0,
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_can_filter_by_date_range()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'date' => '25-10-2020:10-11-2020'
        ]);

        $response->assertStatus(200);
        $response->assertJsonMissing([
			"total" => 0,
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_validates_wrong_date_format()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'date' => '2020-10-25'
        ]);

        $response->assertStatus(488);
        $response->assertJsonFragment([
        	"success" => false,
        ]);
    }

    /**
     * @test
     * @vcr all_hotels
     */
    public function it_paginates_results()
    {
        $response = $this->json('GET', $this->apiBaseUrl . 'hotels', [
        	'per_page' => 2,
        	'page' => 2,
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
        	"per_page" => 2,
        	"current_page" => 2,
        ]);
    }

}
