<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * @abstract    Server Request Routiung Class, Execute/Route actions uppon Splash Server requests.
 *              This file is included only in case on NuSOAP call to slave server.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Components;

use ArrayObject;
use Exception;
use Splash\Core\SplashCore      as Splash;

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

class Router
{
    //====================================================================//
    // Tasks Counters
    private $count = 0;              // Input Task Counter
    private $success = 0;              // Succeeded Task Counter

    //====================================================================//
    // Tasks Statistics
    private $batchTimer;                    // Task Batch Execution Start Timestamp
    private $taskTimer;                     // Current Task Execution Start Timestamp

    /**
     * @abstract     Execute Server Requested Tasks
     *
     * @param string      $router Name of the router function to use for task execution
     * @param ArrayObject $input  Poiner to Server Input Buffer
     * @param ArrayObject $output Poiner to Server Output Buffer
     *
     * @return bool Global tesks Result
     */
    public function execute($router, $input, $output)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Tasks Counters Initialisation
        $this->count = 0;                  // Input Task Counter
        $this->success = 0;                  // Succeeded Task Counter
        //====================================================================//
        // Task Batch Initialisation
        $this->batchTimer = microtime(true);     // Initiate Performance Timer
        //====================================================================//
        // Safety Checks - Validate Request
        if (!$this->validate($router, $input, $output)) {
            return false;
        }
        //====================================================================//
        // Init output task results
        $output->tasks = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Step by Step Execute Tasks
        foreach ($input->tasks as $index => $task) {
            //====================================================================//
            // Tasks Execution
            $output->tasks[$index] = $this->executeTask('\\Splash\\Router\\'.$router, $task);
        }
        //====================================================================//
        // Build Complete Task Batch Information Array
        $output->tasksinfos = $this->getBatchInfos();
        //====================================================================//
        // Return Global Batch Result
        return ($this->count == $this->success) ? true : false;
    }

    /**
     * @abstract     Execute a Single Tasks
     *
     * @param string      $router Name of the router function to use for task execution
     * @param ArrayObject $task   Task To Execute
     *
     * @return ArrayObject|false
     */
    public function executeTask($router, $task)
    {
        //====================================================================//
        // Safety Check
        if (empty($task)) {
            return false;
        }
        //====================================================================//
        // Init Tasks Timer
        $this->taskTimer = microtime(true);
        //====================================================================//
        // Increment Tried Tasks Counter
        ++$this->count;
        //====================================================================//
        // Tasks Execution
        try {
            $result = $router::Action($task);
        } catch (Exception $exc) {
            $result = $this->getEmptyResponse($task);
            Splash::log()->err($exc->getMessage().' on File '.$exc->getFile().' Line '.$exc->getLine());
            Splash::log()->err($exc->getTraceAsString());
        }
        //====================================================================//
        // Store Task Results
        if (is_a($result, 'ArrayObject')) {
            //====================================================================//
            // Insert Task Main Informations
            $result->id = $task['id'];
            //====================================================================//
            // Insert Task Performance Informations
            $result->delayms = $this->getDelayTaskStarted();
            $result->delaystr = sprintf('%.2f %s', $this->getDelayTaskStarted(), ' ms');
            //====================================================================//
            // Increment Success Tasks Counter
            if ($result->result) {
                ++$this->success;
            }

            return $result;
        }

        return false;
    }

    //====================================================================//
    //  SERVER TASKING MANAGER
    //====================================================================//

    /**
     * @abstract     Validate Received Server Request
     *
     * @param string      $router Name of the router function to use for task execution
     * @param ArrayObject $input  Poiner to Server Input Buffer
     * @param ArrayObject $output Poiner to Server Output Buffer
     *
     * @return bool
     */
    private function validate($router, $input, $output)
    {
        //====================================================================//
        // Safety Checks - Verify Inputs & Outputs are Valid
        if (!is_a($input, 'ArrayObject') || !is_a($output, 'ArrayObject')) {
            return Splash::log()->err('Unable to perform requested action. I/O Buffer not ArrayObject Type.');
        }
        //====================================================================//
        // Safety Checks - Verify tasks array exists
        if (!isset($input->tasks) || !count($input->tasks)) {
            return Splash::log()->war('Unable to perform requested action, task list is empty.');
        }
        Splash::log()->deb('Found '.count($input->tasks).' tasks in request.');
        //====================================================================//
        // Safety Checks - Verify Each Tasks is an ArrayObject
        foreach ($input->tasks as $index => $task) {
            if (!is_a($task, 'ArrayObject')) {
                return Splash::log()->err(
                    'Unable to perform requested action. Task '.$index.' is not ArrayObject Type.'
                );
            }
        }
        //====================================================================//
        // Safety Check - Verify Router Exists
        if (!class_exists('\\Splash\\Router\\'.ucwords($router))) {
            return Splash::log()->err(
                "Unable to perform requested tasks, given router doesn't exist(".ucwords($router).'). '
                .'Check your server configuration & methods'
            );
        }

        return true;
    }

    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     * @abstract    Build an Empty Task Response
     *
     * @param ArrayObject $task Task To Execute
     *
     * @return ArrayObject Task Result ArrayObject
     */
    private function getEmptyResponse($task)
    {
        //====================================================================//
        // Initial Tasks results ArrayObject
        $response = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Set Default Result to False
        $response->result = false;
        $response->data = null;
        //====================================================================//
        // Insert Task Description Informations
        $response->name = $task->name;
        $response->desc = $task->desc;

        return $response;
    }

    /**
     * @abstract    Build Complete Task Batch Information Array
     *
     * @return array
     */
    private function getBatchInfos()
    {
        return array(
            'DelayMs' => $this->getDelayStarted(),
            'DelayStr' => sprintf('%.2f %s', $this->getDelayStarted(), ' ms'),
            'Performed' => $this->count,
            'Ok' => $this->success,
        );
    }

    /**
     * @abstract    Delay in MilliSecond Since Router Started
     *
     * @return float
     */
    private function getDelayStarted()
    {
        return 1000 * (microtime(true) - $this->batchTimer);
    }

    /**
     * @abstract    Delay in MilliSecond Since Task Started
     *
     * @return float
     */
    private function getDelayTaskStarted()
    {
        return 1000 * (microtime(true) - $this->taskTimer);
    }
}
