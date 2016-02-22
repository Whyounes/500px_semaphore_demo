<?php

use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Client;
use \Illuminate\Support\Collection;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use GuzzleHttp\Subscriber\Log\LogSubscriber;

class PxOAuth{

    private $host;

    public $client;

    private $consumer_key;

    private $consumer_secret;

    private $token;

    private $token_secret;

    public function __construct($host, $consumer_key, $consumer_secret, $token, $token_secret){
        $this->host = $host;
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->token = $token;
        $this->token_secret = $token_secret;

        $params = [
            'consumer_key'      => $this->consumer_key,
            'consumer_secret'   => $this->consumer_secret,
            'token'             => $this->token,
            'token_secret'      => $this->token_secret
        ];

        /*if($token){
            $params['token'] = $token;
        }*/

        $oauth = new Oauth1($params);

        $this->client = new Client([ 'base_url' => $this->host, 'defaults' => ['auth' => 'oauth']]);
        $this->client->getEmitter()->attach($oauth);

        // if app is in debug mode, we do logging
        if( App::make('config')['app']['debug'] ) {
            $this->addLogger();
        }
    }

    public function get($url, $data){
        $request = $this->client->createRequest('GET', $url);
        $query = $request->getQuery();

        (new Collection($data))->map(function($v, $k) use($query){
            $query->set($k, $v);
        });

        return $this->client->send($request);
    }

    private function addLogger(){
        $log = new Logger('guzzle');
        $log->pushHandler(new StreamHandler(__DIR__.'/../storage/logs/guzzle.log'));

        $subscriber = new LogSubscriber($log, Formatter::DEBUG);
        $this->client->getEmitter()->attach($subscriber);
    }
}//class
