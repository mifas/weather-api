<?php


class WeatherResultTest extends TestCase
{

    public $openWeatherApiRepo;

    public function setUp()
    {
        $this->openWeatherApiRepo = app('App\Repo\OpenWeatherApiRepo');

        parent::setUp();
    }

    /**
     * Load the page without any errors,
     *
     * @test
     */
    public function a_user_can_see_weather_report()
    {
        $response = $this->call('GET', '/');
        $this->assertResponseStatus(200);
        $this->assertContains('Wind', $response->content());
    }

    /**
     * A user can able to fetch city's current weather
     * and next day 5 days of forecast
     *
     * @test
     */
    public function a_user_can_see_current_weather_of_city_and_next_5_days_of_forecast()
    {
        $this->get('weather/current?lat=6.9319&lon=79.8478')->assertResponseStatus(200);

        $this->seeJsonStructure(['weather','forecast']);
    }

    /**
     * When unable to fetch geo coordinates
     * It should throw an Exception
     *
     * @test
     */
    public function should_throw_an_error_when_invalid_geo_coordinates_pass()
    {
        $this->get('weather/current?lat=6.9319')->assertResponseStatus(500);
    }


}