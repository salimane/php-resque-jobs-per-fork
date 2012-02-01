resque-jobs-per-fork: PHP Resque Worker
===========================================

If you have very frequent and fast resque jobs, the overhead of forking and running your after_fork hook, might get too big.
Using this resque plugin, you can have your workers perform more than one job, before terminating.
This is a superset of php-resque (https://github.com/chrisboulton/php-resque). So everything you coould do with php-resque is still doable
just that now you can run more jobs per child process before it gets terminated.

Dependency:

    php-resque : https://github.com/chrisboulton/php-resque
    make sure you've setup sucessfully php-reque and all your workers are working well.

## Workers ##

Workers work in the exact same way as on php-resque. For complete
documentation on workers, see the original documentation.

just add the included file WorkerJobsPerFork.php in your Resque folder, the same folder 
as your original Worker.php . If you have setup php-resque it should be in "lib/Resque/"
and then include it your "resque.php". 
For your convenience, a basic "up-and-running" resque.php file is also included that sets up a
running worker environment.


To start a worker, You simply specify the number of jobs you want each fork to run using the JOBS_PER_FORK environment variable:

    $ QUEUE=* JOBS_PER_FORK=5 php resque.php

This will have each fork run 5 jobs, before terminating.

If the JOBS_PER_FORK environment variable is not specify, only 1 job is run per worker process fork


## Event/Hook System ##

php-resque has a basic event system that can be used by your application
to customize how some of the php-resque internals behave.

You listen in on events (as listed below) by registering with `Resque_Event`
and supplying a callback that you would like triggered when the event is
raised:

  Resque_Event::listen('eventName', [callback]);

`[callback]` may be anything in PHP that is callable by `call_user_func_array`:

Events may pass arguments (documented below), so your callback should accept
these arguments.

You can stop listening to an event by calling `Resque_Event::stopListening`
with the same arguments supplied to `Resque_Event::listen`.

It is up to your application to register event listeners. When enqueuing events
in your application, it should be as easy as making sure php-resque is loaded
and calling `Resque_Event::listen`.

When running workers, if you run workers via the default `resque.php` script,
your `APP_INCLUDE` script should initialize and register any listeners required
for operation. If you have rolled your own worker manager, then it is again your
responsibility to register listeners.

for more documentation on this please check php-resque (https://github.com/chrisboulton/php-resque) docs.


### Events Provided ###

#### beforePerformJobsPerFork ####

Called before the  `perform` method on multiple jobs is run. A

#### afterPerformJobsPerFork ####

Called after the `perform` method on a multiple jobs is run. 

Any exceptions thrown will be treated as if they were thrown in a job, causing the job
to be marked as having failed.

== Copyleft

Copyleft (c) 2012 Salimane Adjao Moustapha. All Wrongs reserved.