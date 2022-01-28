<?php


class Weewx
{
    // Connexion
    private $connexion;
    private $table = "archive";
    private $tableuser = "users";

    // object properties
    public $rainmonth;
    public $rainyear;
    public $dateTime;
    public $usUnits;
    public $altimeter;
    public $appTemp;
    public $barometer;
    public $dewpoint;
    public $heatindex;
    public $humidex;
    public $outTemp;
    public $outHumidity;
    public $pressure;
    public $rain;
    public $rainRate;
    public $windchill;
    public $windDir;
    public $windGust;
    public $windSpeed;
    public $windGustDir;
    public $id;
    public $apikey;
    public $apisignature;
    public $starttimestamp;
    public $endtimestamp;
    public $latitude;
    public $longitude;
    public $station;
    public $time_zone;





    /**
     * Constructeur avec $db pour la connexion à la base de données
     *
     * @param $db
     */
    public function __construct($db)
    {
        $this->connexion = $db;
    }


    //public function table() Cela nous permet de verifier si la tabler "users" existe ou pas 

    public function table()
    {
        // On écrit la requête

        $sql = "SELECT 1 FROM " . $this->tableuser . " LIMIT 1";



        // On prépare la requête
        $query = $this->connexion->prepare($sql);

        // On attache l'id
        $query->bindParam(1, $this->id);

        // On exécute la requête
        $query->execute();

        // on récupère la ligne
        $row = $query->fetch(PDO::FETCH_ASSOC);

        // On hydrate l'objet
        $this->id = $row['id'];
        if ($row !== FALSE) {
        } else {

            // Si la table "users" n'existe pas, on va la creer.

            $sql = "CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `apikey` varchar(255) NOT NULL,
            `apisignature` varchar(255) NOT NULL,
            `station` varchar(20) NOT NULL,
            `latitude` varchar(255) NOT NULL,
            `longitude` varchar(255) NOT NULL,
            `time_zone` varchar(255) NOT NULL,
            `created_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`)
          ) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;
          COMMIT";


            // Préparation de la requête
            $query = $this->connexion->prepare($sql);

            // Exécution de la requête
            if ($query->execute()) {
                return true;
            }
            return false;
        }
    }


    //public function getuser() nous permet de recuperer l'user avec les parametres 
    //$this->id = $row['id'];
    //$this-> apikey  = $row ['apikey'];
    //$this-> apisignature = $row ['apisignature'];


    public function getuser()
    {
        // On écrit la requête
        $sql = "SELECT * FROM " . $this->tableuser . " LIMIT 1";

        // On prépare la requête
        $query = $this->connexion->prepare($sql);

        // On attache l'id
        $query->bindParam(1, $this->id);

        // On exécute la requête
        $query->execute();

        // on récupère la ligne
        $row = $query->fetch(PDO::FETCH_ASSOC);

        // On hydrate l'objet
        $this->id = $row['id'];
        $this->apikey  = $row['apikey'];
        $this->apisignature = $row['apisignature'];
        $this->latitude = $row['latitude'];
        $this->longitude = $row['longitude'];
        $this->station = $row['station'];
        $this->time_zone = $row['time_zone'];
    }

    /**
     * Créer un user
     *
     * @return void
     */
    public function creer()
    {

        // Ecriture de la requête SQL en y insérant le nom de la table
        $sql = "INSERT INTO " . $this->tableuser . " SET  id=:id, username=:username, apikey=:apikey, apisignature=:apisignature, created_at=:created_at, station=:station, latitude=:latitude, longitude=:longitude, time_zone=:time_zone";

        // Préparation de la requête
        $query = $this->connexion->prepare($sql);



        // Ajout des données protégées
        $query->bindParam(':id', $this->id);
        $query->bindParam(':username', $this->username);
        $query->bindParam(':apikey', $this->apikey);
        $query->bindParam(':apisignature', $this->apisignature);
        $query->bindParam(':station', $this->station);
        $query->bindParam(':latitude', $this->latitude);
        $query->bindParam(':longitude', $this->longitude);
        $query->bindParam(':time_zone', $this->time_zone);
        $query->bindParam(':created_at', $this->created_at);

        // Exécution de la requête
        if ($query->execute()) {
            return true;
        }
        return false;
    }


    /*
     * Lecture avec le derner record sur la BDD -- 1 seul donnée 
     *
     * @return void
     */


    public function rainmonth()
    {
        // On recup le 1er jour du mois et le dernier jour du mois en cours  

        $datestart = strtotime(date('Y-m-01'));
        $dateend = strtotime(date('Y-m-t'));
        // On écrit la requête
        $sql = "SELECT sum(rain) AS rainmonth FROM " . $this->table . " WHERE dateTime BETWEEN " . $datestart . " AND " . $dateend . " ";

        // On prépare la requête
        $query = $this->connexion->prepare($sql);

        // On exécute la requête
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);

        $this->rainmonth = $row['rainmonth'];
        // on récupère la ligne
        return $query;
    }



    public function rainyear()
    {

        // On recup le 1er jour de l'année  et le dernier jour de  l'année en cours  
        $datestart = strtotime(date('Y-01-01'));
        $dateend = strtotime(date('Y-m-d'));

        // On écrit la requête
        $sql = "SELECT sum(rain) AS rainyear FROM " . $this->table . " WHERE dateTime BETWEEN " . $datestart . " AND " . $dateend . "";


        // On prépare la requête
        $query = $this->connexion->prepare($sql);

        // On exécute la requête
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);

        // On hydrate l'objet
        $this->rainyear = $row['rainyear'];

        // on récupère la ligne
        return $query;
    }


    public function current()
    {
        // On écrit la requête
        // On écrit la requête
        $sql = "SELECT * FROM " . $this->table .  "  NATURAL JOIN " . $this->tableuser . "  ORDER BY dateTime DESC LIMIT 1";


        // On prépare la requête
        $query = $this->connexion->prepare($sql);

        // On exécute la requête
        $query->execute();

        return $query;
    }
    /*
     * Lecture des historic 
     *
     * @return void
     */
    public function historic()
    {
        // On écrit la requête
        // On écrit la requête
        $sql = "SELECT * FROM " . $this->table .  "  NATURAL JOIN " . $this->tableuser . " WHERE dateTime BETWEEN :dateTime AND :dateTime1 ";

        // On prépare la requête
        $query = $this->connexion->prepare($sql);

        // On attache le dateTime vers le Startimestamp  & Endtimestamp
        $query->bindParam(':dateTime', $this->starttimestamp);
        $query->bindParam(':dateTime1', $this->endtimestamp);

        // On exécute la requête
        $query->execute();

        return $query;
    }
}
