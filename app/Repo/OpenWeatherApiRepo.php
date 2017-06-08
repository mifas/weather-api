<?php namespace App\Repo;

use Cmfcmf\OpenWeatherMap;
use Illuminate\Support\Facades\Cache;
use Mockery\Exception;

class OpenWeatherApiRepo
{
    private $openWeatherMap;

    function __construct()
    {
        $this->openWeatherMap = new OpenWeatherMap(env('OPEN_WEATHER_API_KEY'));
    }

    /**
     * Get city's current weather and forecast for next 5days
     *
     * @param $query
     * @param int $days
     * @return mixed
     */
    public function getForecast($query, $days = 5)
    {
        $data = [];

        try {
            $forecast = $this->openWeatherMap->getWeatherForecast($query, 'metric', 'en', '', $days);
            $weather  = $this->openWeatherMap->getWeather($query, 'metric', 'en');
        } catch (OWMException $e) {
            throw new OWMException;
        } catch (\Exception $e) {
            throw new Exception();
        }

        $data['weather']['temperature'] = $weather->temperature->getValue();
        $data['weather']['icon']        = "http://openweathermap.org/img/w/" . $weather->weather->icon . '.png';
        $data['weather']['desc']        = ucfirst($weather->weather->description);

        foreach ($forecast as $weather) {
            $data['forecast'][$weather->time->day->format('d.m.Y')][] = [
                'from'        => $weather->time->from->format('H:i'),
                'to'          => $weather->time->to->format('H:i'),
                'temperature' => [
                    'value' => $weather->temperature->getValue(),
                    'unit'  => $weather->temperature->getUnit(),
                ],
                'icon'        => "http://openweathermap.org/img/w/" . $weather->weather->icon . '.png',
                'wind'        => $weather->wind->speed->getDescription() . ', ' . $weather->wind->speed->getValue() . ' ' . $weather->wind->speed->getUnit() . ' from ' . $weather->wind->direction->getDescription()
            ];
        }

        return $data;

    }

    /**
     * Get current geo location
     *
     * @return mixed
     */
    public function getCurrentGeoData()
    {
        if (!Cache::has('current_location')) {
            $currentGeoData = json_decode(file_get_contents('http://ip-api.com/json'), 1);
            Cache::put('current_location', $currentGeoData, 30);
        } else {
            $currentGeoData = Cache::get('current_location');
        }

        return $currentGeoData;
    }
}