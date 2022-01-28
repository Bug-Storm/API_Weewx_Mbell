<?php

// Version : 0.2 Bêta
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







        if ($timestamp <= 300) { // Si le $timestamp est == ou < a 300s ou 5m   la requete est bonne on continnue sinon un erreur 404 est envoyé

            // On récupère les données du user

            $produit->getuser();


            if (!empty($produit->id == $id and $produit->apikey == $apikey and $produit->apisignature == $apisignature)) {
                // On verifie si les données de la query string sont correctes


                // On récupère les données

                $produit->rainyear();
                $produit->rainyear;

                $produit->rainmonth();
                $produit->rainmonth;
                //Round pour avoir 1 chiffre après la virgule :) 
                $rainyear = round($produit->rainyear, 2);
                $rainmonth = round($produit->rainmonth, 2);

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
                            $rainmonth = $rainmonth * 0.393701;
                            $rainyear = $rainyear * 0.393701;
                            $rainRate = $rainRate * 0.393701;
                            $rain = $rain * 0.393701;
                            //       //

                            //Temp//

                            $appTemp = (float)(($appTemp * 9 / 5) + 32);
                            $dewpoint = (float)(($dewpoint * 9 / 5) + 32);
                            $outTemp = (float)(($outTemp * 9 / 5) + 32);
                            $inTemp = (float)(($inTemp * 9 / 5) + 32);
                            $inDewpoint = (float)(($inDewpoint * 9 / 5) + 32);



                            //Wind//
                            $windGust = $windGust / 1.609344;
                            $windSpeed = $windSpeed / 1.609344;

                            //Pressure//
                            $altimeter = $altimeter / 33.8639;
                            $barometer = $barometer / 33.8639;
                            $pressure = $pressure / 33.8639;
                        } elseif ($usUnits == 17) {
                            //Rain//
                            $rainmonth = $rainmonth * 0.393701;
                            $rainyear = $rainyear * 0.393701;
                            $rainRate = $rainRate * 0.393701;
                            $rain = $rain * 0.393701;
                            //       //

                            //Temp//
                            $appTemp = ($appTemp - 32) * (5 / 9);
                            $windchill = ($windchill - 32) * (5 / 9);
                            $heatindex = ($heatindex - 32) * (5 / 9);
                            $dewpoint = ($dewpoint - 32) * (5 / 9);
                            $outTemp = ($outTemp - 32) * (5 / 9);
                            $inTemp = ($inTemp - 32) * (5 / 9);
                            $inDewpoint = ($inDewpoint - 32) * (5 / 9);
                            // ------// 


                            //Wind//
                            $windGust = $windGust * 2.2369362921;
                            $windSpeed = $windSpeed * 2.2369362921;

                            //Pressure//
                            $altimeter = $altimeter / 33.8639;
                            $barometer = $barometer * 0.029530;
                            $pressure = $pressure * 0.029530;
                        } else {

                            //Nothing 
                        }

                        $user = [

                            "station" => $station,
                            "latitude" => $latitude,
                            "longitude" => $longitude,
                            "timezone" => $timezone,
                        ];

                        $prod = [



                            "datetime" => $dateTime,
                            "interval" => $interval,
                            "usUnits"  => $usUnits,
                            "altimeter_inHg" => $altimeter,
                            "appTemp_F" => $appTemp,
                            "bar_sea_level_inHg" => $barometer,
                            "dew_point_F" => $dewpoint,
                            "heat_index_F" => $heatindex,
                            "humidex" => $humidex,
                            "temp_F" => $outTemp,
                            "hum" => $outHumidity,
                            "bar_absolute_inHg" => $pressure,
                            "rainfall_last_24_hr_in" => $rain,
                            "rain_rate_last_in" => $rainRate,
                            "rain_month_in"   => $rainmonth,
                            "rain_year_in"   => $rainyear,
                            "wind_chill_F" => $windchill,
                            "wind_dir_last" => $windDir,
                            "wind_speed_hi_last_10_min_mile" => $windGust,
                            "wind_gust_dir_last_" => $windGustDir,
                            "wind_speed_avg_last_10_min_mile" => $windSpeed,
                            "uv_index"        => $UV,
                            "solar_rad" => $radiation,
                            "temp_in_in"      => $inTemp,
                            "dew_point_in_in"  => $inDewpoint,
                            "hum_in_"        => $inHumidity,
                            "Last_record" => $echo =  str_replace("/", "/", date('d/m/Y H:i', $dateTime)),
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
} else {

    // ($_SERVER['REQUEST_METHOD'] == 'GET')
    // 405 Method Not Allowed
    http_response_code(405);

    echo json_encode(array("message" => "La méthode n'est pas autorisée."), JSON_UNESCAPED_UNICODE);
}
