<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        ])->get('https://covid-193.p.rapidapi.com/countries');
        if ($response->ok()) {

            foreach ($response->json()['response'] as $c) {
                $this->countries[] = [
                    "code" => $c,
                    "name" => str_replace("-"," ",$c)
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

        return $response;

    }

    public function index()
    {
        $covid_data = $this->getCovidAPI();

        $data=['covid_data'=>json_encode([]),'countries'=> $this->countries];

        if($covid_data->ok()){
            $data['covid_data']= $covid_data->json();
        }

        return response()->json($data);
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
    public function show($country)
    {
        if(!empty($country)){
            $api = $this->getCovidAPI($country);

            if($api->ok()){
                return response()->json(["covid_api"=>$api->json()]);
            }
            else{
                return response()->json(["message" => "The request is failed!"]);
            }
        }
        else{
            return response()->json(["message" => "The field country is required"]);
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
