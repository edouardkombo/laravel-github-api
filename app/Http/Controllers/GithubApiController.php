<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\GithubService;

class GithubApiController extends BaseController
{
    public function read(Request $request, GithubService $githubService) 
    {
        $username = $request->route('username');
        
        $apiResponse = $githubService->load($username);

        $collection = collect($apiResponse->message);
        
        $body = (!$apiResponse->success) ? $apiResponse->message : $githubService->calculate($collection);            
        
        return response()->json($body, $apiResponse->httpStatusCode);
    }
}