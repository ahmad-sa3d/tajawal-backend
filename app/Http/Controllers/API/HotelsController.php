<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StudentRequest;
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
    	$url = 'https://api.myjson.com/bins/tl0bp';

        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);

        $data = json_decode($response->getBody(), true);


    	// Send response
    	return $this->sendResponse( [
    		'data' => $data,
    	] );
    }
}
