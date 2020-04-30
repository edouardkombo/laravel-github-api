<?php

namespace App\Services;

use GuzzleHttp\Exception\ClientException;
use Guzzle;
use Config;
use Illuminate\Support\Collection;

class GithubService
{
    /**
     * @bool
     */
    public $success = true;

    /**
     * @integer
     */
    public $message;

    /**
     * @integer
     */
    public $httpStatusCode;

    /**
     * @string
     */
    protected $username;

    public function load(string $username)
    {
        $githubApiUrl = str_replace('__USERNAME__', $username, Config::get('github.api'));
        
        $this->username = $username;

        try {
            $call = Guzzle::get($githubApiUrl);
            $this->message = json_decode($call->getBody());
            $this->httpStatusCode = $call->getStatusCode();

        } catch (ClientException $exception) {
            $this->message = json_decode($exception->getResponse()->getBody(true))->message;
            $this->httpStatusCode = $exception->getResponse()->getStatusCode();
            $this->success = false; 
        }

        return $this;
    }

    public function calculate(Collection $query)
    {
        $rules = Config::get('github.rules');

        //Group results by type, count each type then multiply each count by rules
        return $query->where('actor.login', $this->username)->groupBy('type')->map->count()->map(function($item, $key) use ($rules) {
            $lowerCaseKey = strtolower($key);
            return (in_array($lowerCaseKey, array_keys($rules))) ? $item * $rules[$lowerCaseKey] : $item;
        });        
    }
}