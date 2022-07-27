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
        $url =
            'https://prod-public-api.livescore.com/v1/api/app/date/' .
            $sport .
            '/' .
            $date .
            '/5.30?MD=1';
        $sport_id = $this->getSportID($sport);
        echo $url;
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

                    $this->setTeams([
                        'id' => $team['ID'],
                        'name' => $team['Nm'],
                    ]);
                }
                foreach ($event['T2'] as $team) {
                    array_push($teams2_ids, $team['ID']);
                    array_push($teams2_names, $team['Nm']);

                    $this->setTeams([
                        'id' => $team['ID'],
                        'name' => $team['Nm'],
                    ]);
                }

                // https://stackoverflow.com/questions/21658926/storing-array-or-std-object-in-database-of-laravel-app

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
                        // 'stage_id' => $stage['Sid'],
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
                            : null,
                    't2i1r' =>
                        isset($event['Tr2C1']) && $event['Tr2C1'] != ''
                            ? $event['Tr2C1']
                            : null,
                    't1i2r' =>
                        isset($event['Tr1C2']) && $event['Tr1C2'] != ''
                            ? $event['Tr1C2']
                            : null,
                    't2i2r' =>
                        isset($event['Tr2C2']) && $event['Tr2C2'] != ''
                            ? $event['Tr2C2']
                            : null,
                    't1i1w' =>
                        isset($event['Tr1CW1']) && $event['Tr1CW1'] != ''
                            ? $event['Tr1CW1']
                            : null,
                    't2i1w' =>
                        isset($event['Tr2CW1']) && $event['Tr2CW1'] != ''
                            ? $event['Tr2CW1']
                            : null,
                    't1i2w' =>
                        isset($event['Tr1CW2']) && $event['Tr1CW2'] != ''
                            ? $event['Tr1CW2']
                            : null,
                    't2i2w' =>
                        isset($event['Tr2CW2']) && $event['Tr2CW2'] != ''
                            ? $event['Tr2CW2']
                            : null,
                    't1i1o' =>
                        isset($event['Tr1CO1']) && $event['Tr1CO1'] != ''
                            ? $event['Tr1CO1']
                            : null,
                    't2i1o' =>
                        isset($event['Tr2CO1']) && $event['Tr2CO1'] != ''
                            ? $event['Tr2CO1']
                            : null,
                    't1i2o' =>
                        isset($event['Tr1CO2']) && $event['Tr1CO2'] != ''
                            ? $event['Tr1CO2']
                            : null,
                    't2i2o' =>
                        isset($event['Tr2CO2']) && $event['Tr2CO2'] != ''
                            ? $event['Tr2CO2']
                            : null,
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
            }
        }
    }

    public function curlRequest($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'cache-control: no-cache',
                'postman-token: 4e970479-ebbc-4b14-79b0-e6c3b37d2131',
            ],
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
}
