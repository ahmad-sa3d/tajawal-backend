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
	 * Get Hotels API
	 * @param  Request $request Current Request
	 * @return response         Hotels
	 */
    public function getHotels(Request $request)
    {   
        try {
            $data = $this->getHotelsFromApi();
        } catch(\Exception $e) {
            return $this->sendError('Conection Error while calling API');
        }

        try {
            $hotelsStore = new HotelsStore($data);

            $hotelsStore = $this->applyFilters($hotelsStore, $request);
            $order = $this->getOrderByInstance($request);

            $output = $hotelsStore->orderBy($order)
                            ->paginate($request->get('per_page'), $request->get('page'), ['path' => \Request::url()])
                            ->appends($request->except('page'));

        	// Send response
        	return $this->sendResponse([ 'hotels' => $output ]);

        } catch(\Exception $e) {
            // Send Error
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Call Hotels Api and get hotels
     * @return array hotels
     */
    private function getHotelsFromApi()
    {
        $url = 'https://api.myjson.com/bins/tl0bp';
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);
        return json_decode($response->getBody(), true)['hotels'];
    }

    /**
     * Get Order By Instance according to given request
     * @param  Request $request Current request instance
     * @return OrderContract           Order Instance
     */
    private function getOrderByInstance(Request $request)
    {
        switch ($request->get('order_by')) {
            case 'price':
                $order = new PriceOrder($request->get('order_direction'));
                break;
            case 'name':
            default:
                $order = new NameOrder($request->get('order_direction'));
        }

        return $order;
    }

    /**
     * Apply Filters to given Hotels Store
     * @param  HotelsStore &$hotelsStore Store
     * @param  Request     $request      CurrentRequest instance
     * @return HotelStore                Filtered HotelStore
     */
    private function applyFilters(HotelsStore &$hotelsStore, Request $request)
    {
        $hotelsStore->addFilter(new NameFilter($request->get('name')))
                        ->addFilter(new CityFilter($request->get('city')))
                        ->addFilter(new PriceFilter($request->get('price')))
                        ->addFilter(new DateFilter($request->get('date')));

        return $hotelsStore = $hotelsStore->applyFilters();
    }
}
