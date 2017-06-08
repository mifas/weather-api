<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Weather API</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
</head>
<body>
<style>
    .is-loading {
        visibility: hidden;
        display: block;
        width: 100%;
    }

    .is-loading-content:before {
        content: 'Loading....';
        visibility: visible;
    }

    .is-loading:before {
        visibility: visible;
    }

    span.locality {
        font-size: 22px;
        font-weight: 500;
    }

    span.country-name {
        color: #a6a6a6;
    }
</style>
<div class="container" id="app">
    <div class="row" style="margin-top: 30px;">
        <div class="col-md-6" style="margin:0 auto">
            <div class="form-group">
                <input type="text" class="form-control" name="" value="" id="city" aria-describedby="helpId"
                       placeholder="Enter your city and hit Enter">
                <small id="helpId" class="form-text text-muted">Sri Lanka city only!</small>
            </div>
        </div>
    </div>

    <div class="weather-result is-loading">
        <div v-if="error.message">
            <div class="alert alert-danger">@{{error.message}}</div>
        </div>
        <div v-show="!error.message">
            <div class="row">
                <div class="col-md-6" style="margin:0 auto">
                    <div class="row">
                        <div class="col-md-6" v-html="location_html"></div>
                        <div class="col-md-6">
                            <img style="float:left" alt="" :src="weather.icon">
                            <div class="clearfix">
                                <h2 class="float-left">@{{ weather.temperature }}</h2>
                                <span class="float-left">°C</span>
                            </div>
                            <span style="font-size: 12px;margin-top: 0;display: inline-block;position: relative;top: -21px;left: 10px;color: #a2a2a2;">@{{ weather.desc }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:15px;">
                <div class="col-md-12">
                    <h5 style="text-align: center;">Next 5 days, Hourly weather and forecasts in @{{location}}</h5>
                    <div v-for="(weather,index) in forecasts">
                        <p>@{{index}}</p>
                        <table class="table table-bordered table-hover table-sm">
                            <thead>
                            <tr>
                                <th>Time</th>
                                <th>Forecast</th>
                                <th>Temp</th>
                                <th>Wind</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="temperature in weather">
                                <td>@{{ temperature.from +' to ' + temperature.to}}</td>
                                <td><img :src="temperature.icon" alt=""></td>
                                <td>@{{temperature.temperature.value}} °C</td>
                                <td>@{{ temperature.wind }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyDo1-dqX-cjMqP0o-TzCb14-g4nZcZTwWA"></script>
<script src="https://unpkg.com/vue"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script>
    new Vue({
        el     : '#app',
        data   : {
            forecasts    : {},
            weather      : {},
            location     : 'Colombo',
            location_html: 'Colombo',
            error        : {},
        },
        methods: {
            getWeather               : function (lat, lon) {
                var that = this;
                that.showLoading(true);
                axios.get('/weather/current?lat=' + lat + '&lon=' + lon)
                     .then(function (response) {
                         if (response.status == 200) {
                             that.forecasts = response.data.forecast
                             that.weather   = response.data.weather
                             that.error     = {};

                             that.showLoading(false);
                         }
                     })
                     .catch(function (error) {
                         that.error = {
                             'message': error.response.data[0]
                         }
                         that.showLoading(false);
                     })
            },
            initializeMapAutocomplete: function () {
                var that = this;

                var options = {
                    types                : ['(cities)'],
                    componentRestrictions: {country: "lk"}
                };

                var input        = document.getElementById('city');
                var autocomplete = new google.maps.places.Autocomplete(input, options);

                autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (place.name == "") {
                        that.error = {
                            'message': 'Please enter your city'
                        };
                    } else {
                        that.location_html = place.adr_address
                        that.location      = place.vicinity
                        that.getWeather(place.geometry.location.lat(), place.geometry.location.lng());
                    }
                });
            },
            showLoading              : function (visibility) {
                var el = document.querySelectorAll('.weather-result')[0].classList;
                if (visibility === true) {
                    el.add('is-loading-content');
                } else {
                    el.remove('is-loading-content');
                    el.remove('is-loading');
                }
            }
        },
        mounted: function () {
            google.maps.event.addDomListener(window, 'load', this.initializeMapAutocomplete());
        }
    });

</script>
</body>
</html>