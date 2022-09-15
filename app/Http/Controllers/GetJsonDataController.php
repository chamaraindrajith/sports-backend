<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class GetJsonDataController extends Controller
{
    public function getJson($sport, $date) {
        $path = public_path() . "/data/".$sport."/".$date;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file = $path."/data.json";
        
        if ($jsonFile = file_get_contents($file)){
            return $jsonFile;
        } else {
            echo "Oops! Error ...";
        }
    }
    
    public function getDataByDate($sport, $date)
    {
        header('Content-Type: application/json');
        $arr = $this->getJson($sport, $date);
        $arr = json_decode($arr);
        echo json_encode($arr);
    }

    public function today($sport)
    {
        $date = date_format(now(), 'Ymd');
        return $this->getDataByDate($sport, $date);
    }
}
