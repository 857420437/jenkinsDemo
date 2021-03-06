<?php

class MainTask extends \Phalcon\Cli\Task
{

    public function mainAction()
    {
        return "mainAction";
    }

    public function requestDiAction()
    {
        return $this->di->get('data') ;
    }

    public function helloAction($world = "", $symbol = "!")
    {
        return "Hello " . $world . $symbol;
    }
}
