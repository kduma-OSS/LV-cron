<?php

namespace KDuma\Cron;

use Illuminate\Contracts\Console\Kernel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class WebCronController
 * @package KDuma\Cron
 */
class WebCronController extends Controller
{
    /**
     * @var Kernel
     */
    private $artisan;

    /**
     * @param Kernel $artisan
     */
    function __construct(Kernel $artisan)
    {
        $this->artisan = $artisan;
    }

    /**
     * @param bool $secret
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function webCron($secret = false){
        if(config('webcron.secret') === false)
            abort(500, "Webcron secret isn't configured. For security purposes you need to set it.");

        if($secret != config('webcron.secret'))
            abort(404);

        $output = new BufferedOutput;

        $this->artisan->call('queue:cron', [
            '-t' => config('webcron.time_limit'),
            '-r' => config('webcron.run_limit')
        ]);

        return response($output->fetch(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8'
        ]);
    }
}
