<?php namespace KDuma\Cron;

use Illuminate\Queue\Worker;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\Job;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process queue';

    /**
     * The queue worker instance.
     *
     * @var \Illuminate\Queue\Worker
     */
    protected $worker;

    /**
     * Create a new queue listen command.
     *
     * @param  \Illuminate\Queue\Worker  $worker
     * @return void
     */
    public function __construct(Worker $worker)
    {
        parent::__construct();

        $this->worker = $worker;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $queue = $this->option('queue');

        $delay = $this->option('delay');

        $connection = $this->argument('connection');

        $this->worker->setCache($this->laravel['cache']->driver());

        $this->worker->setDaemonExceptionHandler(
            $this->laravel['Illuminate\Contracts\Debug\ExceptionHandler']
        );

        $this->worker->cron(
            $connection, $queue, $delay,
            $this->option('sleep'), $this->option('tries'),
            $this->option('timelimit'), $this->option('runlimit')
        );
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('connection', InputArgument::OPTIONAL, 'The name of connection', null),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('queue', null, InputOption::VALUE_OPTIONAL, 'The queue to listen on'),

            array('delay', null, InputOption::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 0),

            array('force', null, InputOption::VALUE_NONE, 'Force the worker to run even in maintenance mode'),

            array('sleep', null, InputOption::VALUE_OPTIONAL, 'Number of seconds to sleep when no job is available', 3),

            array('tries', null, InputOption::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 0),

            array('timelimit', 't', InputOption::VALUE_OPTIONAL, 'A time limited to run all commands in seconds.', 60),

            array('runlimit', 'r', InputOption::VALUE_OPTIONAL, 'Maximum runs.', null),
        );
    }

}