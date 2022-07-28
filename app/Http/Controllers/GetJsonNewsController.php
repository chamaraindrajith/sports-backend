<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class GetJsonNewsController extends Controller
{
    public function getJson($sport, $date) {
        $path = public_path() . '/news/' . $sport . '/' . $date;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file = $path."/news.json";
        
        if ($jsonFile = file_get_contents($file)){
            return $jsonFile;
        } else {
            echo "Oops! Error ...";
        }
    }
    
    public function getNewsByDate($sport, $date)
    {
        $json = $this->getJson($sport, $date);

        // $json = json_encode($json,true);   
        $json = json_decode($json, true);

        return View::make('json', compact('json'));
    }

    public function today($sport)
    {
        $date = date_format(now(), 'Ymd');
        return $this->getDataByDate($sport, $date);
    }
}
