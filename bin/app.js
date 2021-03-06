(function() {
  var _, app, callback, config, cors, express, getWeather, http, key, lat, long, port, processConditions, processForecast, request, requestType, sendData;

  request = require('request');

  express = require('express');

  cors = require('cors');

  http = require('http');

  _ = require('underscore');

  app = express();

  app.use(cors());

  callback = "";

  config = {
    api: process.env.WU_API
  };

  requestType = "conditions";

  lat = 0;

  long = 0;

  key = "";

  callback = "";

  sendData = function(body, res) {
    res.type('text/json');
    if (callback) {
      return res.send(callback + "(" + JSON.stringify(body) + ")");
    } else {
      return res.send(JSON.stringify(body));
    }
  };

  processForecast = function(data, res) {
    var body, err, forecast;
    body = data.forecast;
    forecast = [];
    try {
      _.each(body.txt_forecast.forecastday, function(element, i) {
        var day, isNight;
        isNight = element.title.indexOf("Night", 0);
        if (isNight === -1) {
          day = {
            day: element.title,
            icon: element.icon,
            text: element.fcttext_metric,
            text_f: element.fcttext
          };
        }
        if (isNight === -1) {
          return forecast.push(day);
        }
      }, this);
      forecast.splice(0, 1);
      return sendData(forecast, res);
    } catch (_error) {
      err = _error;
      return res.end("Oops...");
    }
  };

  processConditions = function(data, res) {
    var body, conditions;
    body = data.current_observation;
    conditions = {
      city: body.display_location.city,
      state: body.display_location.state_name,
      icon: body.icon,
      temp: body.temp_c + 273.15,
      temp_c: body.temp_c,
      temp_f: body.temp_f,
      feelslike: body.feelslike_c,
      feelslike_c: body.feelslike_c,
      feelslike_f: body.feelslike_f,
      humidity: body.relative_humidity,
      UV: body.UV,
      visibility: body.visibility_km,
      visibility_ki: body.visibility_km,
      visibility_mi: body.visibility_mi,
      precip: body.precip_today_metric,
      precip_metric: body.precip_today_metric,
      precip_in: body.precip_today_in,
      windchill: body.windchill_c,
      windchill_c: body.windchill_c,
      windchill_f: body.windchill_f,
      time: body.observation_time
    };
    return sendData(conditions, res);
  };

  getWeather = function(res) {
    var _rt, url;
    url = "http://api.wunderground.com/api/APIKEY/TYPE/pws:0/q/LAT,LONG.json";
    url = url.replace("APIKEY", config.api);
    url = url.replace("LAT", lat);
    url = url.replace("LONG", long);
    url = url.replace("TYPE", this.requestType);
    _rt = this.requestType;
    return http.get(url, function(response) {
      var body;
      body = '';
      response.on('data', function(chunk) {
        return body += chunk;
      });
      return response.on('end', function() {
        switch (_rt) {
          case 'conditions':
            return processConditions(JSON.parse(body), res);
          case "forecast":
            return processForecast(JSON.parse(body), res);
        }
      });
    });
  };

  app.get('/:type/:lat/:long', function(req, res) {
    var type;
    if (req.query.callback) {
      callback = req.query.callback;
    }
    type = req.params.type;
    lat = req.params.lat;
    long = req.params.long;
    this.requestType = type;
    switch (this.requestType) {
      case 'conditions':
      case 'forecast':
        return getWeather(res);
      default:
        return res.send("Go away... Go away now!", 404);
    }
  });

  app.get('*', function(req, res) {
    return res.send("Go away... Go away now!", 404);
  });

  port = process.env.PORT || 5000;

  app.listen(port);

}).call(this);
