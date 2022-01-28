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

            // On récupère les données du user

            $produit->getuser();


            if (!empty($produit->id == $id and $produit->apikey == $apikey and $produit->apisignature == $apisignature)) {
                // On verifie si les données de la query string sont correctes


                // On récupère les données

                $produit->rainyear();
                $produit->rainyear;

                $produit->rainmonth();
                $produit->rainmonth;
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
                            $rainmonth = round($rainmonth * 0.393701,3);
                            $rainyear = round($rainyear * 0.393701,3);
                            $rainRate = round($rainRate * 0.393701,3);
                            $rain =round( $rain * 0.393701,3);
                            //       //

                            //Temp//

                            $appTemp = round((($appTemp * 9 / 5) + 32),3);
                            $dewpoint = round((($dewpoint * 9 / 5) + 32),3);
                            $outTemp = round((($outTemp * 9 / 5) + 32),3);
                            $inTemp = round((($inTemp * 9 / 5) + 32),3);
                            $inDewpoint =round((($inDewpoint * 9 / 5) + 32),3);



                            //Wind//
                            $windGust = round($windGust / 1.609344,3);
                            $windSpeed =round($windSpeed / 1.609344,3);

                            //Pressure//
                            $altimeter = round($altimeter / 33.8639,3);
                            $barometer = round($barometer / 33.8639,3);
                            $pressure = round($pressure / 33.8639,3);

                        } elseif ($usUnits == 17) {
                            //Rain//
                            $rainmonth = round($rainmonth * 0.393701,3);
                            $rainyear = round($rainyear * 0.393701,3);
                            $rainRate = round($rainRate * 0.393701,3);
                            $rain = round($rain * 0.393701,3);
                            //       //

                            //Temp//
                            $appTemp = round(($appTemp - 32) * (5 / 9),3);
                            $windchill = round(($windchill - 32) * (5 / 9),3);
                            $heatindex = round(($heatindex - 32) * (5 / 9),3);
                            $dewpoint = round(($dewpoint - 32) * (5 / 9),3);
                            $outTemp = round(($outTemp - 32) * (5 / 9),3);
                            $inTemp = round(($inTemp - 32) * (5 / 9),3);
                            $inDewpoint = round(($inDewpoint - 32) * (5 / 9),3);
                            // ------// 


                            //Wind//
                            $windGust = round($windGust * 2.2369362921,3);
                            $windSpeed = round($windSpeed * 2.2369362921,3);

                            //Pressure//
                            $altimeter = round($altimeter / 33.8639,3);
                            $barometer = round($barometer * 0.029530,3);
                            $pressure = round($pressure * 0.029530,3);

                        } else {

                            //Nothing 
                        }

                        $user = [

                            "station" =>(string)$station,
                            "latitude" =>(string) $latitude,
                            "longitude" =>(string) $longitude,
                            "time_zone" =>(string) preg_replace('/\s+/', '', $time_zone),
                        ];

                        $prod = [



                            "datetime" => (string)$dateTime,
                            "interval" =>(string) $interval,
                            "usUnits"  => (string)$usUnits,
                            "altimeter_inHg" =>(string) $altimeter,
                            "appTemp_F" =>(string) $appTemp,
                            "bar_sea_level_inHg" => (string)$barometer,
                            "dew_point_F" => (string)$dewpoint,
                            "heat_index_F" => (string)$heatindex,
                            "humidex" => (string)$humidex,
                            "temp_F" =>(string) $outTemp,
                            "hum" => (string)$outHumidity,
                            "bar_absolute_inHg" => (string)$pressure,
                            "rainfall_last_24_hr_in" =>(string) $rain,
                            "rain_rate_last_in" =>(string) $rainRate,
                            "rain_month_in"   => (string)$rainmonth,
                            "rain_year_in"   =>(string) $rainyear,
                            "wind_chill_F" =>(string) $windchill,
                            "wind_dir_last" =>(string) $windDir,
                            "wind_speed_hi_last_10_min_mile" =>(string) $windGust,
                            "wind_gust_dir_last_" =>(string) $windGustDir,
                            "wind_speed_avg_last_10_min_mile" =>(string) $windSpeed,
                            "uv_index"        => (string)$UV,
                            "solar_rad" =>(string) $radiation,
                            "temp_in_in"      =>(string) $inTemp,
                            "dew_point_in_in"  =>(string) $inDewpoint,
                            "hum_in_"        => (string)$inHumidity,
                            "Last_record" =>(string) $echo =  str_replace("/", "/", date('d-m-Y H:i', $dateTime)),
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
