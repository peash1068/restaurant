<?php

namespace App\Http\Controllers;

use App\RestaurentDetails;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * returns all restaurant
     * if app version is the problem version, then it masks name as RestaurantName
     * For testing problem app version is v5.12.300
     * error message for associated code is read from config file for easy update
     *
     * Each app version is assigned a specific user user
     * Access credential is assigned to that user
     * App uses this user credential to acquire access token
     */
    public function restaurantList()
    {
        try {
            $restaurants = RestaurentDetails::query();

            $appVersion = User::find(auth('api')->user()->id)->app_version;

            if ($appVersion == 'v5.12.300')
                $restaurants->selectRaw('restaurant_details.*, restaurant_details.name as RestaurantName');
            else
                $restaurants->selectRaw('restaurant_details.*');

            return [
                'code' => 0,
                'details' => errorMessage(0),
                'data' => $restaurants->get()->toArray()
            ];
        } catch (\Exception $exception) {
            Log::error('Exception Happened @ApiController. Exception ' . json_encode($exception->getMessage()));
            return [
                'code' => 1,
                'details' => errorMessage(1)
            ];
        }

    }

    /**
     * considering open column value as below
     * 0 --- OPEN
     * 1 --- Order ahead
     * 2 --- Currently Closed
     *
     * return data is order as below
     * OPEN Restaurants are at the top
     * Order ahead restaurants are at the middle
     * Currently Closed restaurants are at the bottom
     *
     * Each app version is assigned a specific user user
     * Access credential is assigned to that user
     * App uses this user credential to acquire access token
     * if app version is the problem version, then it masks name as RestaurantName
     * For testing problem app version is v5.12.300
     */
    public function sortRestaurants()
    {
        try {
            $inputs = request()->input();
            $validationRules = [
                'sort_option' => 'required|string|max:100|regex:/(^[A-Za-z0-9 ]+$)+/',
                'sort_order' => 'nullable|string|max:100|regex:/(^[A-Za-z0-9 ]+$)+/'
            ];

            $validation = $this->validateRequest($inputs, $validationRules);
            if ($validation->fails())
                return ['code' => 3, 'details' => implode(', ', $validation->getMessageBag()->all())];

            $sort_column_name = $this->getSortingColumnName(strtolower($inputs['sort_option']));

            if (array_key_exists('sort_order', $inputs) && strtolower($inputs['sort_order']) == 'ascending')
                $order = 'asc';
            else
                $order = 'desc';

            $appVersion = User::find(auth('api')->user()->id)->app_version;
            if ($appVersion == 'v5.12.300')
                $masking = ',t1.name as RestaurantName';
            else
                $masking = '';

            $restaurants = DB::select(DB::raw("SELECT t1.* {$masking} FROM (SELECT * FROM restaurant_details ORDER BY {$sort_column_name} {$order}) t1 ORDER BY t1.open ASC"));

            return [
                'code' => 0,
                'details' => errorMessage(0),
                'data' => $restaurants
            ];

        } catch (\Exception $exception) {
            Log::error('Exception Happened @ApiController. Exception ' . json_encode($exception->getMessage()));
            return [
                'code' => 1,
                'details' => errorMessage(1)
            ];

        }
    }

    /**
     * @param string $sort_option
     * returns column name associated with sorting option
     *
     * @return string
     *
     */

    private function getSortingColumnName(string $sort_option): string
    {
        switch ($sort_option) {
            case 'best match':
                return 'bestMatch';
                break;
            case 'newest':
                return 'newestScore';
                break;
            case 'rating average':
                return 'ratingAverage';
                break;
            case 'popularity':
                return 'popularity';
                break;
            case 'average product price':
                return 'averageProductPrice';
                break;
            case 'delivery costs':
                return 'deliveryCosts';
                break;
            case 'minimum order amount costs':
                return 'minimumOrderAmount';
                break;
            default:
                return 'id';
                break;
        }
    }

    /**
     * validates Request Parameters
     *
     * Searches restaurant using name parameter
     *
     * Each app version is assigned a specific user user
     * Access credential is assigned to that user
     * App uses this user credential to acquire access token
     * if app version is the problem version, then it masks name as RestaurantName
     * For testing problem app version is v5.12.300
     *
     */
    public function searchRestaurant()
    {
        try {
            $inputs = request()->input();

            $validationRules = [
                'name' => 'required|string|max:100|regex:/(^[A-Za-z0-9 ]+$)+/',
            ];

            $validation = $this->validateRequest($inputs, $validationRules);
            if ($validation->fails())
                return ['code' => 3, 'details' => implode(', ', $validation->getMessageBag()->all())];

            $appVersion = User::find(auth('api')->user()->id)->app_version;
            $restaurant = RestaurentDetails::query();
            $restaurant->where('name', 'like', '%' . $inputs['name'] . '%');
            if ($appVersion == 'v5.12.300')
                $restaurant->selectRaw('restaurant_details.*, restaurant_details.name as RestaurantName');
            else
                $restaurant->selectRaw('restaurant_details.*');


            return [
                'code' => 0,
                'details' => errorMessage(0),
                'data' => $restaurant->get()
            ];

        } catch (\Exception $exception) {
            Log::error('Exception Happened @ApiController. Exception ' . json_encode($exception->getMessage()));
            return [
                'code' => 1,
                'details' => errorMessage(1)
            ];
        }

    }

    /**
     * @param $inputs
     * @param $validationRules
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validateRequest($inputs, $validationRules)
    {
        return Validator::make($inputs, $validationRules, [
            'required' => 'The :attribute field is required.',
        ]);
    }
}
