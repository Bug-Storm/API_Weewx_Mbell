<?php

// Version : 0.3
// Name: Api_Weewx_MBELL
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; default_charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 300");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



// On vérifie que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
// On inclut les fichiers de configuration et d'accès aux données
include_once './Database.php';
include_once './weewx.php';

// On instancie la base de données
$database = new Database();
$db = $database->getConnection();

// On instancie les weewx
$produit = new weewx($db);


//Make sure that our query string parameters exist.
if (isset($_GET['t']) && isset($_GET['id']) && isset($_GET['apikey']) && isset($_GET['apisignature'])) {
    $id = trim($_GET['id']); //Get l'id via la query string

    $apikey = trim($_GET['apikey']); //Get l'api Key via la query string

    $apisignature = trim($_GET['apisignature']); //Get l'api Signature via la query string

    $t = trim($_GET['t']); //Get le Time  via la query string

    $timestampnow = time(); //Timestamp Now

    $timestamp = $timestampnow - $t; // On calc la difference entre le timestamp now et celui de la query string


    if ($timestamp <= 300) { // Si le $timestamp est == ou < a 300s ou 5m la requete est bonne on continnue sinon un erreur 404 est envoyé

        // On récupère les données de l'utilisateur

        $produit->getuser();


        if (!empty($produit->id == $id and $produit->apikey == $apikey and $produit->apisignature == $apisignature)) {
            // On verifie si les données de la query string sont correctes


            // On récupère les données : Rain_Year / Rain_Month/ Bar_Trend

            $produit->rainyear();
            $produit->rainyear;

            $produit->rainmonth();
            $produit->rainmonth;
 

            //Bar_Trend
            $stmt = $produit->bar_trend();

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
                    extract($row);


                    $pression_last = $row[0]['bar_trend'];
                    $pression_first = $row[1]['bar_trend'];

                    //Calc Diff hPa/3H
                    $calc = abs($pression_last - $pression_first); // 


                    if ($pression_last > $pression_first) {

                        $trend_bar = "+" . round($calc, 2) . "";
                    } else {
                        $trend_bar = "-" . round($calc, 2) . "";
                    }
                }
            }


            //Round pour avoir 3 chiffre après la virgule :) 
            $rainyear = round($produit->rainyear, 3);
            $rainmonth = round($produit->rainmonth, 3);

            // On récupère les données current
            $stmt = $produit->current();


            // On vérifie si on a au moins 1 produit
            if ($stmt->rowCount() > 0) {
                // On initialise un tableau associatif
                $tableauProduits['sensors'] = [];
                $tableauProduits['user'] = [];
                $tableauProduits['sensors'] = [];

                // On parcourt les produits
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);

                    //Convert to US units//
                    if ($usUnits == 16) {
                        //Rain//
                        $rainmonth = round($rainmonth * 0.393701, 3);
                        $rainyear = round($rainyear * 0.393701, 3);
                        $rainRate = round($rainRate * 0.393701, 3);
                        $rain = round($rain * 0.393701, 3);
                        //       //

                        //Temp//

                        $appTemp = round((($appTemp * 9 / 5) + 32), 3);
                        $dewpoint = round((($dewpoint * 9 / 5) + 32), 3);
                        $outTemp = round((($outTemp * 9 / 5) + 32), 3);
                        $inTemp = round((($inTemp * 9 / 5) + 32), 3);
                        $inDewpoint = round((($inDewpoint * 9 / 5) + 32), 3);
                        $heatindex = round((($heatindex * 9 / 5) + 32), 3);
                        $humidex = round((($humidex * 9 / 5) + 32), 3);
                        $windchill = round((($windchill * 9 / 5) + 32), 3);

                        //Wind//
                        $windGust = round($windGust / 1.609344, 3);
                        $windSpeed = round($windSpeed / 1.609344, 3);
                        $windDir = round($windDir, 3);

                        //Pressure//
                        $altimeter = round($altimeter / 33.8639, 3);
                        $barometer = round($barometer / 33.8639, 3);
                        $pressure = round($pressure / 33.8639, 3);
                        $trend_bar = round($trend_bar / 33.8639, 3);
                        //Humidity 
                        $outHumidity = round($outHumidity, 1);
                        $inHumidity = round($inHumidity, 1);

                    } elseif ($usUnits == 17) {

                        //Rain//
                        $rainmonth = round($rainmonth * 0.393701, 3);
                        $rainyear = round($rainyear * 0.393701, 3);
                        $rainRate = round($rainRate * 0.393701, 3);
                        $rain = round($rain * 0.393701, 3);
                        //       //

                        //Temp//
                        $appTemp = round(($appTemp - 32) * (5 / 9), 3);
                        $windchill = round(($windchill - 32) * (5 / 9), 3);
                        $heatindex = round(($heatindex - 32) * (5 / 9), 3);
                        $dewpoint = round(($dewpoint - 32) * (5 / 9), 3);
                        $outTemp = round(($outTemp - 32) * (5 / 9), 3);
                        $inTemp = round(($inTemp - 32) * (5 / 9), 3);
                        $inDewpoint = round(($inDewpoint - 32) * (5 / 9), 3);
                        $humidex = round(($humidex - 32) * (5 / 9), 3);
                        $windchill = round(($windchill - 32) * (5 / 9), 3);
                        // ------// 


                        //Wind//
                        $windGust = round($windGust * 2.2369362921, 3);
                        $windSpeed = round($windSpeed * 2.2369362921, 3);
                        $windDir = round($windDir, 3);
                        //Pressure//
                        $altimeter = round($altimeter * 33.8639, 3);
                        $barometer = round($barometer * 0.029530, 3);
                        $pressure = round($pressure * 0.029530, 3);
                        $trend_bar = round($trend_bar * 0.029530, 3);

                        //Humidity 
                        $outHumidity = round($outHumidity, 1);
                        $inHumidity = round($inHumidity, 1);

                    } else {

                        $rainmonth = round($rainmonth, 3);
                        $rainyear = round($rainyear, 3);
                        $rainRate = round($rainRate, 3);
                        $rain = round($rain, 3);
                        //       //

                        //Temp//
                        $appTemp = round($appTemp, 3);
                        $windchill = round($windchill, 3);
                        $heatindex = round($heatindex, 3);
                        $dewpoint = round($dewpoint, 3);
                        $outTemp = round($outTemp, 3);
                        $inTemp = round($inTemp, 3);
                        $inDewpoint = round($inDewpoint, 3);
                        $humidex = round($humidex, 3);
                        $windchill = round($windchill, 3);
                        // ------// 


                        //Wind//
                        $windGust = round($windGust, 3);
                        $windSpeed = round($windSpeed, 3);
                        $windDir = round($windDir, 3);

                        //Pressure//
                        $altimeter = round($altimeter, 3);
                        $barometer = round($barometer, 3);
                        $pressure = round($pressure, 3);
                        $trend_bar = round($trend_bar, 3);

                        //Humidity 
                        $outHumidity = round($outHumidity, 1);
                        $inHumidity = round($inHumidity, 1);
                    }
                    //Utilisateur//
                    $user = [

                        "station" => is_null($station) ? NULL : (string)$station,
                        "latitude" => is_null($latitude) ? NULL : (float) $latitude,
                        "longitude" => is_null($longitude) ? NULL : (float) $longitude,
                        "time_zone" => is_null(preg_replace('/\s+/', ' ', $time_zone)) ? NULL  : (string) preg_replace('/\s+/', ' ', $time_zone),
                    ];

                    //Archive//
                    $prod = [



                        "datetime" => is_null($dateTime) ? NULL : (float)$dateTime,
                        "interval" => is_null($interval) ? NULL : (float)$interval,
                        "altimeter_inHg" => is_null($altimeter) ? NULL : (float) $altimeter,
                        "appTemp_F" => is_null($appTemp) ? NULL : (float) $appTemp,
                        "bar_sea_level_inHg" => is_null($barometer) ? NULL : (float)$barometer,
                        "dew_point_F" => is_null($dewpoint) ? NULL : (float)$dewpoint,
                        "heat_index_F" => is_null($heatindex) ? NULL : (float)$heatindex,
                        "humidex" => is_null($humidex) ? NULL : (float)$humidex,
                        "temp_F" => is_null($outTemp) ? NULL : (float) $outTemp,
                        "hum" => is_null($outHumidity) ? NULL : (float)$outHumidity,
                        "bar_absolute_inHg" => is_null($pressure) ? NULL : (float)$pressure,
                        "bar_trend" => is_null($trend_bar) ? NULL : (float)$trend_bar,
                        "rainfall_last_24_hr_in" => is_null($rain) ? NULL : (float) $rain,
                        "rain_rate_last_in" => is_null($rainRate) ? NULL : (float) $rainRate,
                        "rain_month_in"   => is_null($rainmonth) ? NULL  : (float)$rainmonth,
                        "rain_year_in"   => is_null($rainyear) ? NULL  : (float) $rainyear,
                        "wind_chill_F" => is_null($windchill) ? NULL : (float) $windchill,
                        "wind_dir_last" => is_null($windDir) ? NULL : (float) $windDir,
                        "wind_speed_hi_last_10_min_mph" => is_null($windGust) ? NULL : (float) $windGust,
                        "wind_gust_dir_last" => is_null($windGust) ? NULL : (float) $windGustDir,
                        "wind_speed_avg_last_10_min_mph" => is_null($windSpeed)  ? NULL : (float) $windSpeed,
                        "uv_index"        => is_null($UV) ? NULL : (float)$UV,
                        "solar_rad" => is_null($radiation) ? NULL : (float)$radiation,
                        //Inside//
                        "temp_in_F"      => is_null($inTemp) ? NULL : (float) $inTemp,
                        "dew_point_in_F"  => is_null($inDewpoint) ? NULL : (float) $inDewpoint,
                        "hum_in"        => is_null($inHumidity) ? NULL : (float)$inHumidity,
                        "Last_record" => is_null($echo =  str_replace("/", "/", date('d-m-Y H:i', $dateTime))) ? NULL : (string) $echo =  str_replace("/", "/", date('d-m-Y H:i', $dateTime)),
                    ];



                    $tableauProduits['user'][] = $user;
                    $tableauProduits['sensors'][]['data'][]['data'] = $prod;
                }



                // On envoie le code réponse 200 OK
                http_response_code(200);

                // On encode en json et on envoie
                echo json_encode(($tableauProduits), JSON_UNESCAPED_UNICODE);
            }
        } else {
            //if (!empty($produit->id == $id and $produit->apikey == $apikey ......

            // 404 Not found
            http_response_code(404);

            echo json_encode(array("message" => "Votre requête ne pas bonne ou les relevés n'existent pas, veuillez ressayer ."), JSON_UNESCAPED_UNICODE);
        }
    } else {
        // if ($timestamp <= 300 )
        // 405 Method Not Allowed
        http_response_code(405);
        echo json_encode(["message" => "Vous avez dépassé le limit de 5m"], JSON_UNESCAPED_UNICODE);
    }
} else {
    // if (isset($_GET['t']) && isset($_GET['id']) && isset($_GET['apikey']).....
    // 405 Method Not Allowed
    http_response_code(405);

    echo json_encode(array("message" => "La méthode n'est pas autorisée."), JSON_UNESCAPED_UNICODE);
}
}