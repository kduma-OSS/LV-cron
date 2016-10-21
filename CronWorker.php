<?php

namespace KDuma\Cron;

use Exception;
use Illuminate\Queue\Worker as Worker;

/**
 * Class CronWorker.
 */
class CronWorker extends Worker
{
    /**
     * @param $connectionName
     * @param null $queue
     * @param int $delay
     * @param int $sleep
     * @param int $maxTries
     * @param int|null $timelimit
     * @param int|null $runlimit
     * @throws \Exception
     */
    public function cron($connectionName, $queue = null, $delay = 0, $sleep = 3, $maxTries = 0, $timelimit = 60, $runlimit = null)
    {
        if (is_null($timelimit) && is_null($runlimit)) {
            throw new \Exception('You must set the limit. Neither time or run.');
        }
        $runs = 0;
        $time = microtime(true);
        while ($this->daemonShouldRun()) {
            try {
                $connection = $this->manager->connection($connectionName);
                $job = $this->getNextJob($connection, $queue);
                if (is_null($job)) {
                    return;
                }
                $this->process(
                    $this->manager->getName($connectionName), $job, $maxTries, $delay
                );
            } catch (Exception $e) {
                if ($this->exceptions) {
                    $this->exceptions->report($e);
                }
            }
            if (! is_null($runlimit) && ++$runs >= $runlimit) {
                break;
            }
            if (! is_null($timelimit) && (microtime(true) - $time) > $timelimit) {
                break;
            }
        }
    }
}
