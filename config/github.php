<?php

return [
    'api' => env('GITHUB_API_URL', null),
    'rules' => [
        'pushevent' => 10,
        'pullrequestevent' => 5,
        'issuecommentevent' => 4,
        'other' => 1
    ],
];