<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StudentRequest;
use App\Services\Hotels\Filters\CityFilter;
use App\Services\Hotels\Filters\DateFilter;
use App\Services\Hotels\Filters\NameFilter;
use App\Services\Hotels\Filters\PriceFilter;
use App\Services\Hotels\HotelsStore;
use App\Services\Hotels\Orders\NameOrder;
use App\Services\Hotels\Orders\PriceOrder;
use Illuminate\Http\Request;

class HotelsController extends Controller
{

	/**
	 * Get Hotels
	 * @param  Request $request [description]
	 * @return [type]                  [description]
	 */
    public function getHotels(Request $request)
    {
    	// $url = 'https://api.myjson.com/bins/tl0bp';

     //    $client = new \GuzzleHttp\Client();
     //    $response = $client->get($url);

     //    $data = json_decode($response->getBody(), true);
        
        try {
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

            $hotelsStore = new HotelsStore($data);

            $hotelsStore->addFilter(new NameFilter($request->get('name')))
                        ->addFilter(new CityFilter($request->get('city')))
                        ->addFilter(new PriceFilter($request->get('price')))
                        ->addFilter(new DateFilter($request->get('date')));

            $hotelsStore = $hotelsStore->applyFilters();

            switch ($request->get('order_by')) {
                case 'price':
                    $order = new PriceOrder($request->get('order_direction'));
                    break;
                case 'name':
                    $order = new NameOrder($request->get('order_direction'));
                default:
                    $order = new NameOrder($request->get('order_direction'));
            }

            $output = $hotelsStore->orderBy($order)
                            ->paginate($request->get('per_page'), $request->get('page'), ['path' => \Request::url()])
                            ->appends($request->except('page'));

        	// Send response
        	return $this->sendResponse( [
        		'hotels' => $output
        	] );

        } catch(\Exception $e) {
            // Send Error
            return $this->sendError($e->getMessage());
        }
    }
}
