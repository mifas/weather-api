<?php

use Illuminate\Cache\Repository;

class OpenWeatherApiTest extends TestCase
{

    /**
     * @var Open Weather Api Key
     */
    public $apiKey;

    /**
     * @var Repository
     */
    public $openWeatherApiRepo;


    public function setUp()
    {
        $this->apiKey = env('OPEN_WEATHER_API_KEY');

        $this->openWeatherApiRepo = app(\App\Repo\OpenWeatherApiRepo::class);

        parent::setUp();

    }

    /**
     * Weather API key must be entered
     *
     * @test
     */
    public function not_an_empty_api_key()
    {
        $this->assertNotEmpty($this->apiKey);
    }

}