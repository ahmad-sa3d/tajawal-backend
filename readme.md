<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/ahmad-sa3d/tajawal-backend"><img src="https://api.travis-ci.org/ahmad-sa3d/tajawal-backend.svg?branch=master" alt="Build Status"></a>
<a href="https://codeclimate.com/github/ahmad-sa3d/tajawal-backend/maintainability"><img src="https://api.codeclimate.com/v1/badges/7a42bac3e3c2ac7f7060/maintainability" /></a>
<a href="https://codeclimate.com/github/ahmad-sa3d/tajawal-backend/test_coverage"><img src="https://api.codeclimate.com/v1/badges/7a42bac3e3c2ac7f7060/test_coverage" /></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Tajawal Hotels Filter Task

### Setup
* inside project directory run `composer install`
* After generate Application Key `php artisan key:generate`
* Run testing `vendor/bin/phpunit --coverage-text`
	> to generate coverage text you should have xDebug php extension


* Set .env `APP_URL`, `APP_API_URL` for example:
	> `APP_URL` => tajawal.dev
	>
	> `APP_API_URL` => api.tajawal.dev
	
### Usage
* Web Service URL `{APP_API_URL}/v1/hotels`
* Web Service Method `Get`
* Web Service Query Inputs
	- `name` __String__ Hotel Name `grand hayat`
	- `city` __String__ City Name `dubai`
	- `price` __String | Float__ Hotel Price `12.40` OR Range `10:40.5`
	- `date`__String__ Hotel available at specific date `12-10-2020` or Date Range `12-10-2020:20-11-2020`
	- `order_by` __String__ Order Result By `name` OR `price`
	- `order_direction` __String__ Order Result `asc` for Ascending (default) or `desc` for Descending order
	- `per_page` __Integer__ How many result to get per page
	- `page` __Integer__ Get Paginated Result for specific page


* Response Example:

	``` javascript
	
		{
		    "success": true,
		    "data": {
		        "hotels": {
		            "current_page": 1,
		            "data": [
		                {
		                    "name": "Novotel Hotel",
		                    "price": 111,
		                    "city": "Vienna",
		                    "availability": [
		                        {
		                            "from": "20-10-2020",
		                            "to": "28-10-2020"
		                        },
		                        {
		                            "from": "04-11-2020",
		                            "to": "20-11-2020"
		                        },
		                        {
		                            "from": "08-12-2020",
		                            "to": "24-12-2020"
		                        }
		                    ]
		                },
		                {
		                    "name": "Golden Tulip",
		                    "price": 109.6,
		                    "city": "paris",
		                    "availability": [
		                        {
		                            "from": "04-10-2020",
		                            "to": "17-10-2020"
		                        },
		                        {
		                            "from": "16-10-2020",
		                            "to": "11-11-2020"
		                        },
		                        {
		                            "from": "01-12-2020",
		                            "to": "09-12-2020"
		                        }
		                    ]
		                }
		            ],
		            "first_page_url": "https://api.tajawal.dev/v1/hotels?order_by=price&order_direction=desc&per_page=2&page=1",
		            "from": 1,
		            "last_page": 3,
		            "last_page_url": "https://api.tajawal.dev/v1/hotels?order_by=price&order_direction=desc&per_page=2&page=3",
		            "next_page_url": "https://api.tajawal.dev/v1/hotels?order_by=price&order_direction=desc&per_page=2&page=2",
		            "path": "https://api.tajawal.dev/v1/hotels",
		            "per_page": "2",
		            "prev_page_url": null,
		            "to": 2,
		            "total": 6
		        }
		    },
		    "message": "Successfully Retrieved"
		}

	```

	
### Postman Collection Link
[Postman Collection](https://www.getpostman.com/collections/eb86bedd54c39a2065f3)


## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
