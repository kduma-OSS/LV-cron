<?php

return [

    // Worker time limit in one run
    'time_limit' => env('WEBCRON_TIMELIMIT', 30),

    // Worker time limit in one run
    'run_limit' => env('WEBCRON_RUNLIMIT', null),

    // Secret is required to execute cron
    'secret' => env('WEBCRON_SECRET', false),

];
