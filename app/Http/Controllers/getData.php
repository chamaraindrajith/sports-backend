<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class getData extends Controller
{
    // public function getJson($sport, $date) {
    //     $path = public_path() . "/data/".$sport."/".$date;
    //     if (!file_exists($path)) {
    //         mkdir($path, 0777, true);
    //     }
    //     $file = $path."/data.json";

    //     if ($jsonFile = file_get_contents($file)){
    //         return $jsonFile;
    //     } else {
    //         echo "Oops! Error ...";
    //     }
    // }

    public function app($sport, $date, $utc_offset)
    {
        $countryCode = $_GET['countryCode'];
        $locale = $_GET['locale'];
        $MD = $_GET['MD'];
        $url =
            'https://prod-public-api.livescore.com/v1/api/app/date/' . $sport . '/' . $date . '/' . $utc_offset . '?countryCode=' . $countryCode . '&locale=' . $locale . '&MD=' . $MD;
        // $response = json_decode($this->curlRequest($url, array()), true);


        // Read the JSON file
        $jsonFile = 'replacements.json';
        $jsonData = file_get_contents("https://api.coderog.com/sports/public/json/replacement.json");
        $replacements = json_decode($jsonData, true);

        if ($replacements === null) {
            die("Error parsing JSON data from $jsonFile.");
        }

        // Input string
        $inputString = $this->curlRequest($url, array());

        // Apply replacements
        foreach ($replacements['replace_strings'] as $replacement) {
            $search = $replacement['search'];
            $replace = $replacement['replace'];
            $inputString = str_replace($search, $replace, $inputString);
        }

        echo $inputString;
    }

    public function curlRequest($url, array $new_headers)
    {
        // header("Content-Type: image/jpeg");
        $curl = curl_init();

        $headers = array(
            'cache-control: no-cache',
        );

        if (isset($new_headers)) {
            $headers = array_merge($headers, $new_headers);
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:' . $err;
        } else {
            return $response;
        }
    }

    // public function getDataByDate($sport, $date)
    // {
    //     header('Content-Type: application/json');
    //     $arr = $this->getJson($sport, $date);
    //     $arr = json_decode($arr);
    //     echo json_encode($arr);
    // }

    // public function today($sport)
    // {
    //     $date = date_format(now(), 'Ymd');
    //     return $this->getDataByDate($sport, $date);
    // }
}
