<?php

namespace App\Http\Controllers;

use App\Repo\OpenWeatherApiRepo;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Mockery\Exception;

class WeatherController extends BaseController
{

    private $openWeatherApiRepo;

    function __construct(OpenWeatherApiRepo $openWeatherApiRepo)
    {
        $this->openWeatherApiRepo = $openWeatherApiRepo;
    }

    /**
     * Home page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Get current weather and next 5days of forecast for a city
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        try{
            $data = $currentWeather = $this->openWeatherApiRepo->getForecast([
                'lat' => $request->get('lat'),
                'lon' => $request->get('lon'),
            ]);
            return response()->json($data);
        }catch(Exception $e){
            return response()->json(['Invalid format'],500);
        }

    }
}
