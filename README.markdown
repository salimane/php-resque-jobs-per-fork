resque-jobs-per-fork: PHP Resque Worker
===========================================

If you have very frequent and fast resque jobs, the overhead of forking, run 1 job then fork again to run another job, might get too big.
Using this resque plugin, you can have your workers perform more than one job, before terminating their child process.
This is a superset of php-resque (https://github.com/salimane/php-resque). So everything you could do with php-resque is still doable,
just that now, you can run more jobs per child process before it gets terminated.

##Dependency:##

    php-resque : https://github.com/salimane/php-resque
    
    make sure you've setup sucessfully php-reque and all your workers are working well.

## Workers ##

Workers work in the exact same way as on php-resque. Just add the included file WorkerJobsPerFork.php in your Resque folder,
 the same folder as your original Worker.php . If you have setup php-resque correctly, it should be in "lib/Resque/".
Then load it your "resque.php" like this :

    require_once 'lib/Resque.php';
    require_once 'lib/Resque/Worker.php';
    require_once 'lib/Resque/WorkerJobsPerFork.php';

    $jobs_per_fork = 1;
    $JOBS_PER_FORK = getenv('JOBS_PER_FORK');
    if(!empty($JOBS_PER_FORK)) {
        $jobs_per_fork = $JOBS_PER_FORK;
    }


to instantiate a new worker object, do :

    $worker = new Resque_WorkerJobsPerFork($queues, $jobs_per_fork);


For your convenience, a basic "up-and-running" resque.php file is also included that sets up a
running worker environment.


To start a worker, You simply specify the number of jobs you want each fork to run using the JOBS_PER_FORK environment variable:

    $ QUEUE=* JOBS_PER_FORK=5 php resque.php

This will have each fork run 5 jobs, before terminating.

If the JOBS_PER_FORK environment variable is not specified, only 1 job is run per worker process fork

For complete documentation on php-resque workers, see the original documentation on php-resque.


## Event/Hook System ##

php-resque has a basic event system that can be used by your application
to customize how some of the php-resque internals behave.

You listen in on events (as listed below) by registering with `Resque_Event`
and supplying a callback that you would like triggered when the event is
raised:

  Resque_Event::listen('eventName', [callback]);

When running workers, if you run workers via the default `resque.php` script,
your `APP_INCLUDE` script should initialize and register any listeners required
for operation. If you have rolled your own worker manager, then it is again your
responsibility to register listeners.

For more documentation on this, please check php-resque (https://github.com/salimane/php-resque) docs.

resque-jobs-per-fork provide 2 new hooks: beforePerformJobsPerFork, afterPerformJobsPerFork.


### Events Provided ###

#### beforePerformJobsPerFork ####

Called before the `perform` method on multiple jobs is run.

#### afterPerformJobsPerFork ####

Called after the `perform` method on a multiple jobs is run. 

Any exceptions thrown will be treated as if they were thrown in a job, causing the job
to be marked as having failed.

== Copyleft

Copyleft (c) 2012 Salimane Adjao Moustapha. All Wrongs reserved.