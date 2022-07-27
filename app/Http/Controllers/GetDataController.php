<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class GetDataController extends Controller
{
    public function saveJson($array, $sport, $date) {
        $path = "data/".$sport."/".$date;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file = $path."/data.json";
        
        if (file_put_contents($file, json_encode($array))){
            // echo "JSON file created successfully...";
        } else {
            // echo "Oops! Error creating json file...";
        }
    }
    
    public function getDataByDate($sport, $date)
    {
        $sport_id = DB::table('sports')
            ->where('slug', $sport)
            ->get('id');
        $games = DB::table('games')
            ->where('sport_id', $sport_id[0]->id)
            ->orderBy('id', 'DESC')->get();
        $stages = [];
        foreach ($games as $key => $game) {
           $stages['stages'] = $this->getStages($games);            
        }
        $this->saveJson($stages, $sport, '20220727');
        return json_encode($stages);
    }

    public function getStages($games)
    {
        $stage_array = [];
        $stages = [];
        foreach ($games as $key => $game) {
            if (!in_array($game->stage_id, $stages)) {
                array_push($stages, $game->stage_id);

                $stage = DB::table('stages')
                    ->where('id', $game->stage_id)
                    ->get();
                $category = DB::table('categories')
                    ->where('id', $stage[0]->category_id)
                    ->get();

                array_push($stage_array, [
                    'stage_id' => $game->stage_id,
                    'name' => $stage[0]->name,
                    'slug' => $stage[0]->slug,
                    'category_id' => $stage[0]->category_id,
                    'category_name' => $category[0]->name,
                    'category_slug' => $category[0]->slug,
                    'sport_id' => $category[0]->sport_id,
                    'games' => $this->getGames(
                        $games,
                        $game->stage_id,
                        $category[0]->sport_id
                    ),
                ]);
            }
        }
        return $stage_array;
    }

    public function getGames($games, $stage_id, $sport_id)
    {
        $games_array = [];
        foreach ($games as $key => $game) {
            if ($stage_id == $game->stage_id) {
                array_push($games_array, [
                    'game_id' => $game->id,
                    't1' => $this->getTeams($game->team_id_teams1),
                    't2' => $this->getTeams($game->team_id_teams2),
                    'score' => $this->getScore($game->id, $sport_id),
                    'cricket_phase' => $game->cricket_phase,
                    'cricket_phase_info' => $game->cricket_phase_info,
                    'live_time' => $game->live_time,
                    'live_status_comment' => $game->live_status_comment,
                    'start_date' => $game->start_date,
                    'end_date' => $game->end_date,
                ]);
            }
        }
        return $games_array;
    }

    public function getTeams($teams)
    {
        $teams_array = [];
        $teams = unserialize($teams);
        foreach ($teams as $key => $team) {
            $team_db = DB::table('teams')
                ->where('id', $team)
                ->get();
            array_push($teams_array, [
                'team_id' => $team,
                'name' => $team_db[0]->name,
            ]);
        }
        return $teams_array;
    }

    public function getScore($game_id, $sport_id)
    {
        $score_array = [];

        if ($sport_id == 5) {
            $score = DB::table('scores_cricket')
                ->where('game_id', $game_id)
                ->get();
            array_push($score_array, [
                't1i1r' => $score[0]->t1i1r,
                't2i1r' => $score[0]->t2i1r,
                't1i2r' => $score[0]->t1i2r,
                't2i2r' => $score[0]->t2i2r,
                't1i1w' => $score[0]->t1i1w,
                't2i1w' => $score[0]->t2i1w,
                't1i2w' => $score[0]->t1i2w,
                't2i2w' => $score[0]->t2i2w,
                't1i1o' => $score[0]->t1i1o,
                't2i1o' => $score[0]->t2i1o,
                't1i2o' => $score[0]->t1i2o,
                't2i2o' => $score[0]->t2i2o,
                't1i1d' => $score[0]->t1i1d,
                't2i1d' => $score[0]->t2i1d,
                't1i2d' => $score[0]->t1i2d,
                't2i2d' => $score[0]->t2i2d,
            ]);
        } else {
            $score = DB::table('scores')
                ->where('game_id', $game_id)
                ->get();
                array_push($score_array, [
                    'Tr1' => $score[0]->Tr1,
                    'Tr2' => $score[0]->Tr2,
                    'Tr1G' => $score[0]->Tr1G,
                    'Tr2G' => $score[0]->Tr2G,
                ]);
        }
        return $score_array;
    }

    public function today($sport)
    {
        $date = date_format(now(), 'Ymd');
        return $this->getDataByDate($sport, $date);
    }

    public function getDataLive($sport)
    {
        $sport_id = DB::table('sports')
            ->where('slug', $sport)
            ->get('id');
        $date = date_format(now(), 'Ymd');
        $games = DB::table('games')
            ->where('sport_id', $sport_id[0]->id)
            ->where('status', 1)
            ->get();
        return View::make('games', compact('games'));
    }
}
