<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Http;

class CovidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $countries;

    public function __construct()
    {
        $response = Http::withHeaders([
            'x-rapidapi-host' => 'covid-193.p.rapidapi.com',
            'x-rapidapi-key' => '03b4755197mshcb4b0f5c939eee6p12fc8ejsncc5b7b48c4b9',
            'useQueryString' => true
        ])->get('https://covid-193.p.rapidapi.com/statistics');
        if ($response->ok()) {

            foreach ($response->json()['response'] as $c) {
                $this->countries[] = [
                    "code" => $c['country'],
                    "name" => str_replace("-"," ",$c['country'])
                ];
            }
        }

    }
    private function getCovidAPI($country = ""){

        $args=[];
        if(!empty($country)){
            $args=['country'=>$country];
        }

       // Http::fake();

        $response = Http::withHeaders([
            'x-rapidapi-host' => 'covid-193.p.rapidapi.com',
            'x-rapidapi-key' => '03b4755197mshcb4b0f5c939eee6p12fc8ejsncc5b7b48c4b9',
            'useQueryString'=>true
        ])->get("https://covid-193.p.rapidapi.com/statistics", $args);

        $_data=[];

        if ($response->ok()) {

            foreach ($response->json()['response'] as $c) {
                if($c['continent'] == 'North-America' || $c['continent'] == 'South-America' || empty($c['continent'])){
                    $c['continent'] = "America";
                }
                if($c['country'] == $c['continent']){
                    continue;
                }

                $date = \Carbon\Carbon::now();
                $createdat = \Carbon\Carbon::parse($c['time']);
                $diff_month =  $createdat->diffInMonths($date);
                $diff_weeks =  $createdat->diffInWeeks($date);
                $diff_days =  $createdat->diffInDays($date);
                $diff_hours =  $createdat->diffInHours($date);
                $diff_minutes =  $createdat->diffInMinutes($date);
                $diff_seconds =  $createdat->diffInMinutes($date);

                $time_string="";

                if ($diff_month > 0) {

                    if ($diff_month == 1) {
                        $time_string = "1 month ago";
                    } else {
                        $time_string ="$diff_month months ago";
                    }
                } elseif ($diff_weeks > 0) {

                    if ($diff_weeks == 1) {
                        $time_string =  "1 week ago";
                    } else {
                        $time_string =  "$diff_weeks weeks ago";
                    }
                } elseif ($diff_days > 0) {

                    if ($diff_days == 1) {
                        $time_string =  "a day ago";
                    } else {
                        $time_string =  "$diff_days days ago";
                    }
                } elseif ($diff_minutes > 0) {

                    if ($diff_minutes == 1) {
                        $time_string =  "a minutes ago";
                    } else {
                        $time_string =  "$diff_minutes minutes ago";
                    }
                } elseif ($diff_seconds > 0) {

                    if ($diff_seconds == 1) {
                        $time_string =  "a second ago";
                    } else {
                        $time_string =  "$diff_seconds seconds ago";
                    }
                }


                $_data[] = [
                    "code" => $c['country'],
                    "country" => str_replace("-", " ", $c['country']),
                    "population" => $c['population'],
                    "cases" => $c['cases']['total'],
                    "time"=> $time_string,
                    'continent'=> $c['continent']
                ];
            }
        }

        return $_data;

    }

    public function index()
    {
        try{
            $covid_data = $this->getCovidAPI();

            $data = ['covid_data' => $covid_data];
            if (empty($covid_data)) {
                throw new Exception("There's no data to show");
            }
            return response()->json($data);
        }
        catch(Exception $ex){
            throw new Exception($ex->getMessage());
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($covid)
    {
        try{
            if (!empty($covid)) {
                $api = $this->getCovidAPI($covid);

                $data = ['covid_data' => $api];

                if (empty($api)) {
                    throw new Exception("There's no data to show");
                }

                return response()->json($data);
            } else {
                throw new Exception("The field country is required");
            }
        }
        catch(Exception $ex){
            throw new Exception($ex->getMessage());
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
