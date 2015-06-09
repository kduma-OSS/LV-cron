<?php namespace KDuma\Cron;

use Illuminate\Queue\Worker;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CronCommand
 * @package KDuma\Cron
 */
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
    protected $description = 'Process queue jobs with time or job limit.';

    /**
     * The queue worker instance.
     *
     * @var Worker
     */
    protected $worker;

    /**
     * Create a new queue listen command.
     *
     * @param  Worker $worker
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
            $connection,
            $queue,
            $delay,
            $this->option('sleep'),
            $this->option('tries'),
            $this->option('timelimit'),
            $this->option('runlimit')
        );
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['connection', InputArgument::OPTIONAL, 'The name of connection', null],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['queue', null, InputOption::VALUE_OPTIONAL, 'The queue to listen on'],
            ['delay', null, InputOption::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 0],
            ['force', null, InputOption::VALUE_NONE, 'Force the worker to run even in maintenance mode'],
            ['sleep', null, InputOption::VALUE_OPTIONAL, 'Number of seconds to sleep when no job is available', 3],
            ['tries', null, InputOption::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 0],
            ['timelimit', 't', InputOption::VALUE_OPTIONAL, 'Maximum time this command can work in seconds.', 60],
            ['runlimit', 'r', InputOption::VALUE_OPTIONAL, 'Maximum queue jobs to run in. (default: no limit)', null],
        ];
    }

}
