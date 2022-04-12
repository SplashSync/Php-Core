<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Components;

use ArrayObject;
use Exception;
use Splash\Core\SplashCore      as Splash;
use Splash\Router\RouterInterface;

/**
 * Server Request Routing Class, Execute/Route actions on Splash Server requests.
 * This file is included only in case on SOAP call to slave server.
 */
class Router
{
    //====================================================================//
    // Tasks Counters
    //====================================================================//

    /**
     * Input Task Counter
     *
     * @var int
     */
    private int $count = 0;

    /**
     * Succeeded Task Counter
     *
     * @var int
     */
    private int $success = 0;

    //====================================================================//
    // Tasks Statistics
    //====================================================================//

    /**
     * Task Batch Execution Start Timestamp
     *
     * @var float
     */
    private float $batchTimer;

    /**
     * Current Task Execution Start Timestamp
     *
     * @var float
     */
    private float $taskTimer;

    //====================================================================//
    //  PUBLIC METHODS
    //====================================================================//

    /**
     * Execute Server Requested Tasks
     *
     * @param string $router Name of the router function to use for task execution
     * @param array  $input  Pointer to Server Input Buffer
     * @param array  $output Pointer to Server Output Buffer
     *
     * @return bool Global tasks Result
     */
    public function execute(string $router, array $input, array &$output): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Tasks Counters Initialisation
        $this->count = 0;           // Input Task Counter
        $this->success = 0;         // Succeeded Task Counter
        //====================================================================//
        // Initiate Performance Timer
        $this->batchTimer = microtime(true);
        //====================================================================//
        // Safety Checks - Validate Request
        $routerClass = $this->validate($router, $input);
        if (!$routerClass) {
            return false;
        }
        //====================================================================//
        // Init output task results
        $output['tasks'] = array();
        //====================================================================//
        // Step by Step Execute Tasks
        foreach ($input['tasks'] as $index => $task) {
            //====================================================================//
            // Tasks Execution
            $output['tasks'][$index] = $this->executeTask($routerClass, $task);
        }
        //====================================================================//
        // Build Complete Task Batch Information Array
        $output['tasksinfos'] = $this->getBatchInfos();
        //====================================================================//
        // Return Global Batch Result
        return $this->count == $this->success;
    }

    /**
     * Execute a Single Tasks
     *
     * @param class-string $router Name of the router function to use for task execution
     * @param array        $task   Task To Execute
     *
     * @return null|array
     */
    public function executeTask(string $router, array $task): ?array
    {
        //====================================================================//
        // Safety Check
        if (empty($task) || !is_subclass_of($router, RouterInterface::class)) {
            return null;
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
            $result = $router::action($task);
        } catch (Exception $exc) {
            $result = $this->getEmptyResponse($task);
            Splash::log()->err($exc->getMessage().' on File '.$exc->getFile().' Line '.$exc->getLine());
            Splash::log()->err($exc->getTraceAsString());
        }
        //====================================================================//
        // Store Task Results
        if ($result) {
            //====================================================================//
            // Insert Task Main Informations
            $result['id'] = $task['id'];
            //====================================================================//
            // Insert Task Performance Informations
            $result['delayms'] = $this->getDelayTaskStarted();
            $result['delaystr'] = sprintf('%.2f %s', $this->getDelayTaskStarted(), ' ms');
            //====================================================================//
            // Increment Success Tasks Counter
            if ($result['result']) {
                ++$this->success;
            }

            return $result;
        }

        return null;
    }

    /**
     * Build an Empty Task Response
     *
     * @param array $task Task To Execute
     *
     * @return array Task Result ArrayObject
     */
    public static function getEmptyResponse(array $task): array
    {
        return array(
            //====================================================================//
            // Set Default Result to False
            'result' => false,
            'data' => null,
            //====================================================================//
            // Insert Task Description Informations
            'name' => $task['name'],
            'desc' => $task['desc'],
        );
    }

    //====================================================================//
    //  SERVER TASKING MANAGER
    //====================================================================//

    /**
     * Validate Received Server Request
     *
     * @param string $router Name of the router function to use for task execution
     * @param array  $input  Pointer to Server Input Buffer
     *
     * @return null|class-string
     */
    private function validate(string $router, array $input): ?string
    {
        //====================================================================//
        // Safety Checks - Verify tasks array exists
        if (empty($input['tasks'])) {
            return Splash::log()->errNull('Unable to perform requested action, task list is empty.');
        }
        Splash::log()->deb('Found '.count($input['tasks']).' tasks in request.');
        //====================================================================//
        // Safety Checks - Verify Each Tasks is an ArrayObject
        foreach ($input['tasks'] as $index => $task) {
            if (!is_array($task)) {
                return Splash::log()->errNull(
                    'Unable to perform requested action. Task '.$index.' is not an Array.'
                );
            }
        }
        //====================================================================//
        // Safety Check - Verify Router Exists
        $routerClass = '\\Splash\\Router\\'.ucwords($router);
        if (!class_exists($routerClass)) {
            return Splash::log()->errNull(
                "Unable to perform requested tasks,"
                ." given router doesn't exist(".ucwords($router).'). '
                ."Check your server configuration & methods"
            );
        }
        if (!is_subclass_of($routerClass, RouterInterface::class)) {
            return Splash::log()->errNull(
                "Unable to perform requested tasks,"
                ." router (".ucwords($router).") must implement ".RouterInterface::class
                ." Check your server configuration & methods"
            );
        }

        return $routerClass;
    }

    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     * Build Complete Task Batch Information Array
     *
     * @return array
     */
    private function getBatchInfos(): array
    {
        return array(
            'DelayMs' => $this->getDelayStarted(),
            'DelayStr' => sprintf('%.2f %s', $this->getDelayStarted(), ' ms'),
            'Performed' => $this->count,
            'Ok' => $this->success,
        );
    }

    /**
     * Delay in MilliSecond Since Router Started
     *
     * @return float
     */
    private function getDelayStarted(): float
    {
        return 1000 * (microtime(true) - $this->batchTimer);
    }

    /**
     * Delay in MilliSecond Since Task Started
     *
     * @return float
     */
    private function getDelayTaskStarted(): float
    {
        return 1000 * (microtime(true) - $this->taskTimer);
    }
}
