<?php

return [

    'default' => 'reverb',

    'connections' => [

        'reverb' => [

            'url' => env('REVERB_URL', 'http://127.0.0.1:8000'),

            'key' => env('REVERB_KEY'),

            'secret' => env('REVERB_SECRET'),

        ],

    ],

];
