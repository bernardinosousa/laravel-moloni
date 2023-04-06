<?php

namespace Tiagosimoesdev\Moloni;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Traits\Macroable;
use Tiagosimoesdev\Moloni\Models\MoloniModel;


class Moloni
{
    use Macroable;

    private bool $sandbox;

    private string $api;

    private ?string $access_token;

    private ?string $expires_at;

    private ?string $token_type;

    private ?string $company_id;

    private ?string $refresh_token;

    /**
     * @param string $viewId
     *
     * @return $this
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }


    public function __construct()
    {
        $this->sandbox = config('moloni.sandbox', true);

        if($this->sandbox) {
            $this->api = 'https://api.moloni.pt/sandbox/';
        } else {
            $this->api = 'https://api.moloni.pt/v1/';
        }
        
        $this->access_token = null;
        $this->expires_at = null;
        $this->token_type = null;
        $this->refresh_token = null;
        $this->company_id = null;

        $moloni = MoloniModel::first();

        if ($moloni) {
            $this->access_token = $moloni->access_token;
            $this->expires_at = $moloni->expires_in;
            $this->token_type = $moloni->token_type;
            $this->refresh_token = $moloni->refresh_token;
            $this->company_id = $moloni->company_id;


            $segundos = Carbon::parse($moloni->expires_at)->subHour(1)->diffInSeconds();

            if ($segundos >= 3500) {
                $this->refreshToken();
            }
        } else {
            $this->getToken();
        }
    }



    public function refreshToken()
    {



        $params = [
            'grant_type' => 'password',
            'client_id' => config('moloni.client_id'),
            'client_secret' =>  config('moloni.client_secret'),
            'username'      =>  config('moloni.username'),
            'password'      =>  config('moloni.password')
        ];

        $response = Http::asForm()->get($this->api . '/grant/?refresh_token=' . $this->refresh_token, $params)->json();



        MoloniModel::first()->update([
            'access_token'  =>  $response['access_token'],
            'expires_at'    =>  Carbon::now()->addSeconds($response['expires_in']),
            'token_type'    =>  $response['token_type'],
            'refresh_token' =>  $response['refresh_token']
        ]);

        $this->access_token = $response['access_token'];
        $this->expires_at  = Carbon::now()->addSeconds($response['expires_in']);
        $this->refresh_token = $response['refresh_token'];
    }


    /**
     * getToken
     *
     * @param string $phrase Phrase to return
     * @return string Returns the phrase passed in
     */
    public function getToken()
    {

        $params = [
            'grant_type' => 'password',
            'client_id' => config('moloni.client_id'),
            'client_secret' =>  config('moloni.client_secret'),
            'username'      =>  config('moloni.username'),
            'password'      =>  config('moloni.password')
        ];

        $response = Http::asForm()->get($this->api . '/grant', $params)->json();

        $moloni = MoloniModel::first();


        $this->acces_token = $response['access_token'];
        $this->expires_at  = Carbon::now()->addSeconds($response['expires_in']);
        $this->refresh_token = $response['refresh_token'];


        if ($moloni) {
            $moloni->update([
                'access_token'  =>  $response['access_token'],
                'expires_at'    =>  Carbon::now()->addSeconds($response['expires_in']),
                'token_type'    =>  $response['token_type'],
                'refresh_token' =>  $response['refresh_token'],
                'company_id'    =>  $this->curl()
            ]);
        } else {
            MoloniModel::create([
                'access_token'  =>  $response['access_token'],
                'expires_at'    =>  Carbon::now()->addSeconds($response['expires_in']),
                'token_type'    =>  $response['token_type'],
                'refresh_token' =>  $response['refresh_token']
            ]);
        }
    }


    public function curl($url, $method, $params = null)
    {
        $http = new \GuzzleHttp\Client(['verify' => false]);

        $paramsHTTP = [
            'form_params' => [
                'company_id' => config('moloni.company_id'),
            ]
        ];

        if ($params) {
            foreach ($params as $param => $val) {
                $paramsHTTP['form_params'][$param] = $val;
            }
        }

        try {
            $response = $http->post($this->api . $url . '/' . $method . '/?access_token=' . $this->access_token, $paramsHTTP);
            $resposta = json_decode($response->getBody());

            return $resposta;
        } catch (\Exception $e) {
            if ($e->getCode() === 401) {
                $this->refreshToken();
                $this->curl($url, $method, $params);
            } else {
                abort(569, $e->getMessage());
            }
        }
    }
}
