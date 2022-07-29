<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\SetDataController;
use Illuminate\Support\Facades\DB;

class SetNewsController extends Controller
{
    public function setNews($sport, $date)
    {
        switch ($sport) {
            case 'cricket':
                $this->saveJson($this->setCricbuzz(), $sport, $date);
                break;

            case 'football':
                $this->saveJson($this->setFootball(), $sport, $date);
                break;

            default:
                # code...
                break;
        }
       
    }

    function setCricbuzz() {
        $data_array = array();
        
        $url = 'https://cricbuzz-cricket.p.rapidapi.com/news/v1/index';
        $api = 'rapid';
        $api = $api . "_";
        $headers = [
            'X-RapidAPI-Host: cricbuzz-cricket.p.rapidapi.com',
            'X-RapidAPI-Key: 7795701e29msh045cee6ff0afd4ep1560b9jsn3ebc3aa4fb6a',
        ];

        $setDataController = new SetDataController;

        $response = json_decode($setDataController->curlRequest($url, $headers), true);

        $index = 0;
        foreach ($response['storyList'] as $stories_key => $stories) {
            foreach ($stories as $key => $story) {
                if (
                    array_key_exists('id', $story) &&
                    array_key_exists('context', $story) &&
                    array_key_exists('coverImage', $story)
                ) {
                    $isExists = DB::table('news')
                        ->where('id_api', $api . $story['id'])
                        ->exists();
                    if (!$isExists) {
                        DB::table('news')->insert([
                            'id_api' => $api . $story['id'],
                            'title' => $story['hline'],
                            'description' => $story['intro'],
                            'image_id' => $story['coverImage']['id'],
                            'created_at' => now(),
                        ]);
                    }
                    $this->saveImage([
                        'X-RapidAPI-Host: cricbuzz-cricket.p.rapidapi.com',
                        'X-RapidAPI-Key: 7795701e29msh045cee6ff0afd4ep1560b9jsn3ebc3aa4fb6a',
                        'Content-type: image/jpeg',
                    ], $api, $story['coverImage']['id']);

                    $data_array = $this->setJson($data_array, $api, $index, $story);
                    $index++;

                }
            }
        }
        return $data_array;
    }

    function saveImage(array $headers, $api, $image_id) {
        $image_path = public_path() . '/data/news/images/';
        if (!file_exists($image_path)) {
            mkdir($image_path, 0777, true);
        }
        $image_name = $api . $image_id . '.jpeg';
        $image_path = $image_path . $image_name;
        $setDataController = new SetDataController;
        
        if (!file_exists($image_path)) {
            
            /** cricbuzz rapid */
            if ($api == "rapid_") {
                $url = "https://cricbuzz-cricket.p.rapidapi.com/img/v1/i1/c" . $image_id . "/i.jpg?p=dete&d=low";
                $image_response = $setDataController->curlRequest($url, $headers);
                file_put_contents($image_path, $image_response);
            }
        }
    }

    function setFootball() {
        $data_array = array();
        
        $url = 'https://onefeed.fan.api.espn.com/apis/v3/cached/contentEngine/oneFeed/leagues/soccer?source=ESPN.com+-+FAM&showfc=true&region=in&limit=20&lang=en&editionKey=espnin-en&isPremium=true';
        $api = 'espn';
        $api = $api . "_";
        $headers = [];

        $setDataController = new SetDataController;

        $response = json_decode($setDataController->curlRequest($url, $headers), true);

        $index = 0;
        foreach ($response['feed'][0]['data']['now'][0]['video'] as $videos) {
            foreach ($videos as $key => $story) {
                if (
                    array_key_exists('id', $videos) &&
                    array_key_exists('headline', $videos) &&
                    array_key_exists('thumbnail', $videos)
                ) {
                    $isExists = DB::table('news')
                        ->where('id_api', $api . $videos['id'])
                        ->exists();
                    if (!$isExists) {
                        DB::table('news')->insert([
                            'id_api' => $api . $videos['id'],
                            'title' => $videos['headline'],
                            'description' => $videos['description'],
                            'image_id' => $videos['id'],
                            'created_at' => now(),
                        ]);
                    }
                    $this->saveFootballImage($api, 'image_'.$videos['id'], $videos['thumbnail']);

                    $data_array = $this->setFootballJson($data_array, $api, $index, $videos);
                    $index++;

                }
            }
        }
        return $data_array;
    }

    function saveFootballImage($api, $image_id, $image_url) {
        $image_path = public_path() . '/data/news/images/';
        if (!file_exists($image_path)) {
            mkdir($image_path, 0777, true);
        }
        
        $image_name = $api . $image_id . '.jpg';
        $image_path = $image_path . $image_name;
        $headers = [];
        $setDataController = new SetDataController;
        
        if (!file_exists($image_path)) {
            
            /** espn football api **/
            if ($api == "espn_") {
                $image_response = $setDataController->curlRequest($image_url, $headers);
                file_put_contents($image_path, $image_response);
            }
        }
    }

    function setJson($data_array, $api, $index, $story) {
        $data_array[$index]['id_api'] = $api . $story['id'];
        $data_array[$index]['title'] = $story['hline'];
        $data_array[$index]['description'] = $story['intro'];
        $data_array[$index]['image_id'] = $story['coverImage']['id'];
        $data_array[$index]['created_at'] = now();
        return $data_array;
    }

    function setFootballJson($data_array, $api, $index, $videos) {
        $data_array[$index]['id_api'] = $api . $videos['id'];
        $data_array[$index]['title'] = $videos['headline'];
        $data_array[$index]['description'] = $videos['description'];
        $data_array[$index]['image_id'] = $videos['id'];
        $data_array[$index]['created_at'] = now();
        return $data_array;
    }

    public function saveJson($array, $sport, $date)
    {
        $path = public_path() . '/news/' . $sport . '/' . $date;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file = $path . '/news.json';

        if (file_put_contents($file, json_encode($array))) {
            // echo json_encode($array);
            // echo "JSON file created successfully...";
        } else {
            // echo "Oops! Error creating json file...";
        }
    }
}