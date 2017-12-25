<?php

namespace Phalcon\Cli;

use \Phalcon\Di\Injectable;


/**
 * Phalcon\Cli\Task
 *
 * Every command-line task should extend this class that encapsulates all the task functionality
 *
 * A task can be used to run "tasks" such as migrations, cronjobs, unit-tests, or anything that you want.
 * The Task class should at least have a "mainAction" method
 *
 * <code>
 *
 * class HelloTask extends \Phalcon\Cli\Task
 * {
 *
 *  //This action will be executed by default
 *  public function mainAction()
 *  {
 *
 *  }
 *
 *  public function findAction()
 *  {
 *
 *  }
 *
 * }
 *
 * </code>
 *
 * @see https://github.com/phalcon/cphalcon/blob/1.2.6/ext/cli/task.c
 */
class Task extends Injectable implements TaskInterface
{

    /**
     * \Phalcon\Cli\Task constructor
     */
    final public function __construct()
    {
        if (method_exists($this,'onConstruct')){
            $this->{'onConstruct'}();
        }
        //Look at the original code...
    }

}