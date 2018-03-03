<?php

/*
1. TASK
        */

// Human Class
class Human
{

    // db (human`s brain) credentials
    const DB_IP_ADDRESS = "10.193.9.134";
    const DB_USERNAME = "root";
    const DB_NAME = "test";
    const DB_PASSWORD = "test123";

    // personal identify number
    public $pin;
    // ages
    public $ages;
    // height
    public $height;
    // weight
    public $weight;
    // first name
    public $firstName;
    // last name
    public $lastName;
    //date of birth
    public $dateOfBirth;


    /**
     * Human constructor.
     * @param int $pin
     * @param int $ages
     * @param int $height
     * @param int $weight
     * @param string $firstName
     * @param string $lastName
     * @param string $dateOfBirth
     */
    public function __construct($pin = 0, $ages = 0, $height = 0, $weight = 0, $firstName = "", $lastName = "", $dateOfBirth = "")
    {
        $this->pin = $pin;
        $this->ages = $ages;
        $this->height = $height;
        $this->weight = $weight;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dateOfBirth = $dateOfBirth;
    }


    /**
     * Get Human profile
     * @return mixed
     */
    public function getHumanProfile()
    {
        $result["pin"] = $this->pin;
        $result["ages"] = $this->ages;
        $result["height"] = $this->height;
        $result["weight"] = $this->weight;
        $result["firstName"] = $this->firstName;
        $result["lastName"] = $this->lastName;
        $result["dateOfBirth"] = $this->dateOfBirth;

        return $result;
    }

    /**
     * Saves Human`s profile
     * @return mixed
     */
    public function saveHumanProfile()
    {
        $profile = $this->getHumanProfile();
        if (isset($profile)) {
            foreach ($profile as $key => $item) {
                if ($key !== "pin") {
                    $this->setInfo($key, $item, $this->pin);
                }
            }
        }
    }

    /**
     * Human`s Blood Steam.
     * @return mixed
     */
    public function getBloodSteamData()
    {
        // get blood count
        $report = $this->getBloodCount($this->pin);
        // creates result array
        $result["bloodGroup"] = $report->group;
        $result["numberOfErythrocytes"] = $report->erythrocytes;
        $result["numberOfLeukocytes"] = $report->leukocytes;
        $result["cholesterol"] = $report->cholesterol;
        $result["enzymes"] = $report->enzymes;

        return $result;
    }


    /**
     * Get blood count report.
     * @param $pin
     * @return mixed
     */
    private function getBloodCount($pin)
    {
        $result = false;
        // get info about access token from db (human`s brain)
        $accessToken = $this->getInfo('access_token', $pin);
        // get API response
        $response = $this->executeApi('/report', 'testApi.com', $accessToken);
        // check if response status is 200
        if ($response["status"] == 200) {
            $result = $response["value"];
        }

        return $result;
    }


    /**
     * Creates API request and returns response.
     * @param string $endpoint
     * @param $apiUrl
     * @param $accessToken
     * @return mixed
     */
    public function executeApi($endpoint = '', $apiUrl, $accessToken)
    {
        $url = $apiUrl . $endpoint;

        $header = array(
            'Accept: application/vnd.test.v1+json',
            "Authorization: Bearer $accessToken",
        );

        // curl options
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => false,
        );

        $options[CURLOPT_HTTPHEADER] = $header;

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);

        return $result;
    }


    /**
     * Fetches info from database (human`s brain).
     * @param $name
     * @param $id
     * @return mixed
     */
    public function getInfo($name, $id)
    {

        $con = $this->dbConnect();

        $query = "SELECT value FROM `testTable` WHERE `name` = '$name' AND `id` = '$id' ";

        $result = mysqli_query($con, $query);
        $data = mysqli_fetch_array($result);
        mysqli_close($con);

        return $data['value'];

    }


    /**
     * Saves or updates info in database (human`s brain).
     * @param $name
     * @param $value
     * @param $id
     * @return bool
     */
    public function setInfo($name, $value, $id)
    {

        $con = $this->dbConnect();

        $query = "SELECT value FROM `testTable` WHERE `name` = '$name' AND `id` = '$id'";
        $result = mysqli_query($con, $query);
        $data = mysqli_fetch_array($result);

        if ($data) {
            $query = "UPDATE `testTable` SET `value` = '$value' WHERE `name` = '$name' AND `id` = '$id'";
        } else {
            $query = "INSERT INTO `testTable` (name, value, id) VALUES ('$name','$value','$id')";
        }

        mysqli_query($con, $query);

        mysqli_close($con);

        return true;
    }

    /**
     * Connect to db (human`s brain).
     * @return \mysqli
     */
    private function dbConnect()
    {

        $con = mysqli_connect(self::DB_IP_ADDRESS, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);
        if (!$con) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        if (!$con) {
            die('Could not connect: ' . mysqli_error());
        }

        $db_select = mysqli_select_db($con, self::DB_NAME);
        if (!$db_select) {
            die("Database selection failed: " . mysqli_error());
        }

        return $con;

    }

}


// Human Temperament Class
class Temperament extends Human
{

    // temperament type
    public $temperamentType;

    public function __construct($temperamentType = "")
    {
        // call Human constructor
        parent::__construct();
        $this->temperamentType = $temperamentType;
    }

// get certain human`s behavior based on temperament characteristics
    public function getHumanBehavior()
    {
        $result = "";
        switch ($this->temperamentType) {
            case 'Sanguine':
                $result = $this->getInfo('Sanguine', $this->pin);
                break;
            case 'Phlegmatic':
                $result = $this->getInfo('Phlegmatic', $this->pin);
                break;
            case 'Choleric':
                $result = $this->getInfo('Choleric', $this->pin);
                break;
            case 'Melancholic':
                $result = $this->getInfo('Melancholic', $this->pin);
                break;

        }
        return $result;
    }

}

// Human Gender Class
class Gender extends Human
{
    // pole
    public $pole;
    // human`s interest
    public $interest;
    // human`s clothes
    public $clothes;


    /**
     * Pole constructor.
     * @param string $pole
     * @param string $interest
     * @param string $clothes
     */
    public function __construct($pole = "", $interest = "", $clothes = "")
    {
        // call Human constructor
        parent::__construct();

        $this->pole = $pole;
        $this->interest = $interest;
        $this->clothes = $clothes;
    }

    public function saveAdditionalData()
    {
        $this->setInfo("pole", $this->pole, $this->pin);
        $this->setInfo("interest", $this->interest, $this->pin);
        $this->setInfo("clothes", $this->clothes, $this->pin);
    }

}

// Human Race Class
class Race extends Human
{

    // race
    public $race;
    // language
    public $language;
    // culture
    public $culture;


    /**
     * Race constructor.
     * @param string $race
     * @param string $language
     * @param string $culture
     */
    public function __construct($race = "", $language = "", $culture = "")
    {
        // call Human constructor
        parent::__construct();

        $this->race = $race;
        $this->language = $language;
        $this->culture = $culture;
    }

    /**
     * get cultural labels determined by race category
     * @return mixed|string
     */
    public function getCulturalLabelsDeterminedByRace()
    {
        $result = "";
        // race categories
        $raceCategories = [4886 => "American Indian or Alaska Native", 8743 => "Asian", 9532 => "Black or African American", 9822 => "Native Hawaiian or Other Pacific Islander", 1132 => "White"];
        if (in_array($this->race, $raceCategories)) {
            $raceId = array_search($this->race, $raceCategories);
            $result = $this->getInfo($this->culture, $raceId);
        }
        return $result;
    }

}

// creates human object
$human = new Human(2612987209673, 30, 176, 80, "Lazar", "Lazić", "26.12.1987.");

// returns blood count report
$report = $human->getBloodSteamData();
$humanProfile = $human->getHumanProfile();

echo "Blood count report for human: " . $humanProfile["firstName"] . " " . $humanProfile["lastName"] . "<br/>";
foreach ($report as $data) {
    echo "item: " . $data . "<br/>";
}


/*
2. TASK

        loop1:
         D
         S
         G loop2
         D
         L

        loop2:
         D

                  */
