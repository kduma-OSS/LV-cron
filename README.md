# L5-cron
Laravel 5 queue runner for webcron (with runtime limit)

# Setup
Add the package to the require section of your composer.json and run `composer update`

    "kduma/cron": "~1.0"

Then add the Service Provider to the providers array in `config/app.php` but not before `Illuminate\Queue\QueueServiceProvider`:

    'KDuma\Cron\CronServiceProvider',


# Usage

Command syntax is like `queue:work --daemon` with 2 new options:

    artisan queue:cron [-t|--timelimit[="..."]] [-r|--runlimit[="..."]] [--queue[="..."]] [--delay[="..."]] [--force] [--sleep[="..."]] [--tries[="..."]] [connection]
    
- `--timelimit (-t)` - Maximum time this command can work in seconds. (default: 60)
- `--runlimit (-r)` - Maximum queue jobs to run in. (default: no limit)


# Best practices

- Split your jobs into small tasks that takes small amount of time
- When you choice time limit, subtract time of longest job


# Web Cron

In your routes file add:

    get('cron/{password}', function($secret){
    	if($secret != 'YOUR_SECRET')
    		abort(404);
    	ob_start();
    	\Artisan::call('queue:cron', array('-t' => YOU_TIME_LIMIT));
    	return response(ob_get_clean(), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    });

Replace `YOUR_SECRET` and `YOU_TIME_LIMIT` with your values.