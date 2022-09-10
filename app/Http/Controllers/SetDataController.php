<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetDataController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function setDataByDate($sport, $date)
    {
        $stage_index = 0;
        $event_index = 0;
        $data_array = array();

        $date_for_api = str_replace('-', '', $date);
        $url =
            'https://prod-public-api.livescore.com/v1/api/app/date/' .
            $sport .
            '/' .
            $date_for_api .
            '/5.30?MD=1';
        $sport_id = $this->getSportID($sport);

        $response = json_decode($this->curlRequest($url, array()), true);
        $stage_list = array(
            'fifa',
            'world-cup',
            'premier-league',
            'uefa-champions-league',
            'uefa-europa-league',
            'laliga',
            'bundesliga',
            'super-league',
            'english-football-league',
            'national-league',
            'women',
            'womens-national-league',
            'womens-international-champions-cup',
            'womens-national-league',
            'premier-league-1-women',
            'league-1-women',
            'serie-a',
            'football-league',
            'championship',
            'mls',
            'eredivisie',
            'premier-division',
            'campeonato-brasileiro-play-off-women',
            'argentina-reserve-league',
            'liga',
            'liga-portugal',
            'super-liga',
            'nfl',
            'liga-1',
            'friendlies',
            'fa-cup',
            'champions-cup',
            'copa-america',
            'copa-chile',
            'first-division',
            'laliga-santander'
        );

        foreach ($response['Stages'] as $stage_key => $stage) {

            $data_array['Stages'][$stage_index] = [
                'Sid' => $stage['Sid'],
                'Scd' => $stage['Scd'],
                'Snm' => $stage['Snm'],
                'Cid' => $stage['Cid'],
                'Ccd' => $stage['Ccd'],
                'Cnm' => $stage['Cnm'],
                'sport_id' => $sport_id
            ];

            if (in_array($stage['Scd'], $stage_list) || $sport != 'soccer') {
                foreach ($stage['Events'] as $event_key => $event) {
                    $teams1_ids = [];
                    $teams1_names = [];
                    $teams2_ids = [];
                    $teams2_names = [];
                    $team_index = 0;

                    foreach ($event['T1'] as $team) {
                        array_push($teams1_ids, $team['ID']);
                        array_push($teams1_names, $team['Nm']);

                        $this->setTeams([
                            'id' => $team['ID'],
                            'name' => $team['Nm'],
                        ]);

                        $data_array['Stages'][$stage_index]['Events'][$event_index]['T1'][$team_index] = [
                            'ID' => $team['ID'],
                            'Nm' => $team['Nm']
                        ];
                    }

                    $team_index = 0;

                    foreach ($event['T2'] as $team) {
                        array_push($teams2_ids, $team['ID']);
                        array_push($teams2_names, $team['Nm']);

                        $this->setTeams([
                            'id' => $team['ID'],
                            'name' => $team['Nm'],
                        ]);

                        $data_array['Stages'][$stage_index]['Events'][$event_index]['T2'][$team_index] = [
                            'ID' => $team['ID'],
                            'Nm' => $team['Nm']
                        ];
                    }

                    // https://stackoverflow.com/questions/21658926/storing-array-or-std-object-in-database-of-laravel-app

                    $data_array['Stages'][$stage_index]['Events'][$event_index] = [
                        'Eid' => $event['Eid'],
                        'Slug' => '',
                        'Eid' => $event['Eid'],
                        'Esd' => (isset($event["Esd"]) && $event["Esd"] != '') ? $event['Esd'] : null,
                        'Ese' => (isset($event["Ese"]) && $event["Ese"] != '') ? $event['Ese'] : null,
                        'EpsL' => (isset($event["EpsL"]) && $event["EpsL"] != '') ? $event['EpsL'] : null,
                        'Epr' => $event['Epr'],
                        'EtTx' => (isset($event["EtTx"]) && $event["EtTx"] != '') ? $event['EtTx'] : null,
                        'ErnInf' => (isset($event["ErnInf"]) && $event["ErnInf"] != '') ? $event['ErnInf'] : null,
                        'ECo' => (isset($event["ECo"]) && $event["ECo"] != '') ? $event['ECo'] : null
                    ];

                    $isExists = DB::table('games')
                        ->where('id', $event['Eid'])
                        ->exists();

                    if (!$isExists) {
                        DB::table('games')->insert([
                            'id' => $event['Eid'],
                            'stage_id' => $stage['Sid'],
                            'slug' => '',
                            'sport_id' => $sport_id,
                            'ground' => 'test',
                            'team_id_teams1' => serialize($teams1_ids),
                            'team_id_teams2' => serialize($teams2_ids),
                            'start_date' => (isset($event["Esd"]) && $event["Esd"] != '') ? $event["Esd"] : null,
                            'end_date' => (isset($event["Ese"]) && $event["Ese"] != '') ? $event["Ese"] : null,
                            'category_id' => $stage['Cid'],
                            'status_text' =>
                            isset($event['EpsL']) && $event['EpsL'] != ''
                                ? $event['EpsL']
                                : '',
                            'status' => $event['Epr'],

                            'cricket_phase' => (isset($event["EtTx"]) && $event["EtTx"] != '') ? $event["EtTx"] : null,
                            'cricket_phase_info' => isset($event["ErnInf"]) && $event["ErnInf"] != '' ? $event["ErnInf"] : null,
                            'live_time' => (isset($event["EpsL"]) && $event["EpsL"] != '') ? $event["EpsL"] : null,
                            'live_status_comment' => (isset($event["ECo"]) && $event["ECo"] != '') ? $event["ECo"] : null,

                            'created_at' => now(),
                        ]);
                    } else {
                        DB::table('games')
                            ->where('id', $event['Eid'])
                            ->update([
                                'status_text' =>
                                isset($event['EpsL']) && $event['EpsL'] != ''
                                    ? $event['EpsL']
                                    : '',
                                'status' => $event['Epr'],

                                'cricket_phase' => (isset($event["EtTx"]) && $event["EtTx"] != '') ? $event["EtTx"] : null,
                                'cricket_phase_info' => isset($event["ErnInf"]) && $event["ErnInf"] != '' ? $event["ErnInf"] : null,
                                'live_time' => (isset($event["EpsL"]) && $event["EpsL"] != '') ? $event["EpsL"] : null,
                                'live_status_comment' => (isset($event["ECo"]) && $event["ECo"] != '') ? $event["ECo"] : null,

                                'updated_at' => now(),
                            ]);
                    }

                    $this->setScores([
                        'sport' => $sport,
                        'game_id' => $event['Eid'],
                        'Tr1' =>
                        isset($event['Tr1']) && $event['Tr1'] != ''
                            ? $event['Tr1']
                            : '',
                        'Tr2' =>
                        isset($event['Tr2']) && $event['Tr2'] != ''
                            ? $event['Tr2']
                            : '',
                        'Tr1G' =>
                        isset($event['Tr1G']) && $event['Tr1G'] != ''
                            ? $event['Tr1G']
                            : '',
                        'Tr2G' =>
                        isset($event['Tr1G']) && $event['Tr2G'] != ''
                            ? $event['Tr2G']
                            : '',

                        't1i1r' =>
                        isset($event['Tr1C1']) && $event['Tr1C1'] != ''
                            ? $event['Tr1C1']
                            : 0,
                        't2i1r' =>
                        isset($event['Tr2C1']) && $event['Tr2C1'] != ''
                            ? $event['Tr2C1']
                            : 0,
                        't1i2r' =>
                        isset($event['Tr1C2']) && $event['Tr1C2'] != ''
                            ? $event['Tr1C2']
                            : 0,
                        't2i2r' =>
                        isset($event['Tr2C2']) && $event['Tr2C2'] != ''
                            ? $event['Tr2C2']
                            : 0,
                        't1i1w' =>
                        isset($event['Tr1CW1']) && $event['Tr1CW1'] != ''
                            ? $event['Tr1CW1']
                            : 0,
                        't2i1w' =>
                        isset($event['Tr2CW1']) && $event['Tr2CW1'] != ''
                            ? $event['Tr2CW1']
                            : 0,
                        't1i2w' =>
                        isset($event['Tr1CW2']) && $event['Tr1CW2'] != ''
                            ? $event['Tr1CW2']
                            : 0,
                        't2i2w' =>
                        isset($event['Tr2CW2']) && $event['Tr2CW2'] != ''
                            ? $event['Tr2CW2']
                            : 0,
                        't1i1o' =>
                        isset($event['Tr1CO1']) && $event['Tr1CO1'] != ''
                            ? $event['Tr1CO1']
                            : 0,
                        't2i1o' =>
                        isset($event['Tr2CO1']) && $event['Tr2CO1'] != ''
                            ? $event['Tr2CO1']
                            : 0,
                        't1i2o' =>
                        isset($event['Tr1CO2']) && $event['Tr1CO2'] != ''
                            ? $event['Tr1CO2']
                            : 0,
                        't2i2o' =>
                        isset($event['Tr2CO2']) && $event['Tr2CO2'] != ''
                            ? $event['Tr2CO2']
                            : 0,
                        't1i1d' =>
                        isset($event['Tr1CD1']) && $event['Tr1CD1'] != ''
                            ? $event['Tr1CD1']
                            : null,
                        't2i1d' =>
                        isset($event['Tr2CD1']) && $event['Tr2CD1'] != ''
                            ? $event['Tr2CD1']
                            : null,
                        't1i2d' =>
                        isset($event['Tr1CD2']) && $event['Tr1CD2'] != ''
                            ? $event['Tr1CD2']
                            : null,
                        't2i2d' =>
                        isset($event['Tr2CD2']) && $event['Tr2CD2'] != ''
                            ? $event['Tr2CD2']
                            : null,
                    ]);

                    $category_data['id'] = $stage['Cid'];
                    $category_data['slug'] = $stage['Ccd'];
                    $category_data['name'] = $stage['Cnm'];
                    $category_data['sport_id'] = $sport_id;

                    $this->setCategories($category_data);
                    $this->setStages([
                        'id' => $stage['Sid'],
                        'slug' => $stage['Scd'],
                        'name' => $stage['Snm'],
                        'category_id' => $stage['Cid']
                    ]);

                    $event_index++;
                }
            }

            $stage_index++;
        }

        $this->saveJson($data_array, $sport, $date);
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

    public function setCategories(array $data)
    {
        $isExists = DB::table('categories')
            ->where('id', $data['id'])
            ->exists();

        if (!$isExists) {
            DB::table('categories')->insert([
                'id' => $data['id'],
                'slug' => $data['slug'],
                'name' => $data['name'],
                'sport_id' => $data['sport_id'],
                // 'created_at' => now(),
            ]);
        }
    }

    public function setStages(array $data)
    {
        $isExists = DB::table('stages')
            ->where('id', $data['id'])
            ->exists();

        if (!$isExists) {
            DB::table('stages')->insert([
                'id' => $data['id'],
                'slug' => $data['slug'],
                'name' => $data['name'],
                'category_id' => $data['category_id'],
            ]);
        }
    }

    public function setDataLive($sport)
    {
        /*
        $url =
            'https://prod-public-api.livescore.com/v1/api/app/live/' .
            $sport .
            '/5.30?MD=1';
            echo $url . '<br>';
        $sport_id = $this->getSportID($sport);
        $response = json_decode($this->curlRequest($url), true);

        foreach ($response['Stages'] as $stage_key => $stage) {
            foreach ($stage['Events'] as $event_key => $event) {
                $teams1_ids = [];
                $teams1_names = [];
                $teams2_ids = [];
                $teams2_names = [];
                foreach ($event['T1'] as $team) {
                    array_push($teams1_ids, $team['ID']);
                    array_push($teams1_names, $team['Nm']);
                }
                foreach ($event['T2'] as $team) {
                    array_push($teams2_ids, $team['ID']);
                    array_push($teams2_names, $team['Nm']);
                }
                // https://stackoverflow.com/questions/21658926/storing-array-or-std-object-in-database-of-laravel-app

                $isExists = DB::table('games')
                    ->where('sid', $event['Eid'])
                    ->exists();

                    if (isset($event['Tr1G'])) {
                        $Tr1G = $event['Tr1G'];
                    } else {
                        $Tr1G = 0;
                    }
                    if (isset($event['Tr2G']) && $event['Tr2G'] != "") { 
                        $Tr2G = $event['Tr2G'];
                    } else {
                        $Tr2G = 0;
                    }

                // if (!$isExists) {
                    DB::table('games')->where('eid', $event['Eid'])->update([
                        'Tr1' => (isset($event['Tr1']) && $event['Tr1'] != "") ? $event['Tr1'] : "",
                        'Tr2' => (isset($event['Tr2']) && $event['Tr2'] != "") ? $event['Tr2'] : "",
                        'Tr1G' => (isset($event['Tr1G']) && $event['Tr1G'] != "") ? $event['Tr1G'] : "",
                        'Tr2G' => (isset($event['Tr1G']) && $event['Tr2G'] != "") ? $event['Tr2G'] : "",
                        'updated_at' => now(),
                    ]);
                // }
            }
        }
        */
    }

    public function updateGame($data)
    {
    }

    public function getSportID($sport)
    {
        $sport_id = DB::table('sports')
            ->where('slug', $sport)
            ->get('id');
        return $sport_id[0]->id;
    }

    public function setScores(array $data)
    {
        if ($data['sport'] != 'cricket') {
            $isExists = DB::table('scores')
                ->where('game_id', $data['game_id'])
                ->exists();

            if (!$isExists) {
                DB::table('scores')->insert([
                    'game_id' => $data['game_id'],
                    'Tr1' => $data['Tr1'],
                    'Tr2' => $data['Tr2'],
                    'Tr1G' => $data['Tr1G'],
                    'Tr2G' => $data['Tr2G'],
                    'created_at' => now(),
                ]);
            } else {
                DB::table('scores')
                    ->where('game_id', $data['game_id'])
                    ->update([
                        'Tr1' => $data['Tr1'],
                        'Tr2' => $data['Tr2'],
                        'Tr1G' => $data['Tr1G'],
                        'Tr2G' => $data['Tr2G'],
                        'updated_at' => now(),
                    ]);
            }
        } else {
            $isExists = DB::table('scores_cricket')
                ->where('game_id', $data['game_id'])
                ->exists();
            $data['Tr1'] = 1;
            if (!$isExists) {
                DB::table('scores_cricket')->insert([
                    'game_id' => $data['game_id'],
                    't1i1r' => $data['t1i1r'],
                    't2i1r' => $data['t2i1r'],
                    't1i2r' => $data['t1i2r'],
                    't2i2r' => $data['t2i2r'],
                    't1i1w' => $data['t1i1w'],
                    't2i1w' => $data['t2i1w'],
                    't1i2w' => $data['t1i2w'],
                    't2i2w' => $data['t2i2w'],
                    't1i1o' => $data['t1i1o'],
                    't2i1o' => $data['t2i1o'],
                    't1i2o' => $data['t1i2o'],
                    't2i2o' => $data['t2i2o'],
                    't1i1d' => $data['t1i1d'],
                    't2i1d' => $data['t2i1d'],
                    't1i2d' => $data['t1i2d'],
                    't2i2d' => $data['t2i2d'],
                    'created_at' => now(),
                ]);
            } else {
                DB::table('scores_cricket')
                    ->where('game_id', $data['game_id'])
                    ->update([
                        't1i1r' => $data['t1i1r'],
                        't2i1r' => $data['t2i1r'],
                        't1i2r' => $data['t1i2r'],
                        't2i2r' => $data['t2i2r'],
                        't1i1w' => $data['t1i1w'],
                        't2i1w' => $data['t2i1w'],
                        't1i2w' => $data['t1i2w'],
                        't2i2w' => $data['t2i2w'],
                        't1i1o' => $data['t1i1o'],
                        't2i1o' => $data['t2i1o'],
                        't1i2o' => $data['t1i2o'],
                        't2i2o' => $data['t2i2o'],
                        't1i1d' => $data['t1i1d'],
                        't2i1d' => $data['t2i1d'],
                        't1i2d' => $data['t1i2d'],
                        't2i2d' => $data['t2i2d'],
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    public function setTeams(array $data)
    {
        $isExists = DB::table('teams')
            ->where('id', $data['id'])
            ->exists();

        if (!$isExists) {
            DB::table('teams')->insert([
                'id' => $data['id'],
                'name' => $data['name'],
            ]);
        }
    }

    public function saveJson($array, $sport, $date)
    {
        $path = 'data/' . $sport . '/' . $date;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file = $path . '/data.json';

        if (file_put_contents($file, json_encode($array))) {
            echo json_encode($array);
            // echo "JSON file created successfully...";
        } else {
            // echo "Oops! Error creating json file...";
        }
    }
}
