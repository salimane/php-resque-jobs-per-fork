<?php
/**
 * Resque worker that handles multiples jobs per fork when working.
 * it extends Resque_Worker original class.
 * @author		Salimane Adjao Moustapha <me@salimane.com>
 * @copyleft	(c) 2012 Salimane Adjao Moustapha
 */
class Resque_WorkerJobsPerFork extends Resque_Worker {
  /**
   * number of jobs to run per fork
   * @var int
   * @access public
   */
  public static $jobs_per_fork = 1;

  /**
   * Instantiate a new worker, given a list of queues that it should be working
   * on. The list of queues should be supplied in the priority that they should
   * be checked for jobs (first come, first served)
   *
   * @param string|array $queues String with a single queue name, array with multiple.
   * @param int number of jobs to run per fork
   * @return object Return value from Resque_Worker::__construct().
   */
  public function __construct($queues, $jobs_per_fork) {
    self::$jobs_per_fork = $jobs_per_fork;
    return parent::__construct($queues);
  }

  /**
   * Process some jobs starting with the one provided
   * and processing more jobs as required by $jobs_per_fork amount
   *
   * @param object|null $job The job to be processed.
   */
  public function perform(Resque_Job $job) {
    Resque_Event::trigger('beforePerformJobsPerFork');
    $this->log("Starting PerformJobsPerFork... ");
    $jobs_performed = 0;
    while ($jobs_performed < self::$jobs_per_fork) {
      if($this->shutdown) {
        break;
      }
      if ($jobs_performed == 0) {
        parent::perform($job);
      }
      elseif ($job = $this->reserve()) {
        $this->log('got ' . $job);
        $this->workingOn($job);

        $status = 'Processing ' . $job->queue . ' since ' . strftime('%F %T');
        $this->log($status);

        parent::perform($job);

        $this->doneWorking();
      }
      $jobs_performed++;
    }
    $jobs_performed = null;
    $this->log("Ending PerformJobsPerFork... ");
    Resque_Event::trigger('afterPerformJobsPerFork');
  }

}
