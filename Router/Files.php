<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Router;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\FileProviderInterface;

/**
 * Server Request Routiung Class, Execute/Route actions on Objects Service Requests.
 * This file is included only in case on NuSOAP call to slave server.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
class Files
{
    /**
     * @abstract   Task execution router. Receive task detail and execute requiered task operations.
     *
     * @param ArrayObject $task Full Task Request Array
     *
     * @return ArrayObject Task results, or False if KO
     */
    public static function action($task)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Initial Response
        $response = self::getEmptyResponse($task);

        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        $inputs = self::validateInputs($task);
        if (!$inputs) {
            return $response;
        }

        //====================================================================//
        // Execute Action
        switch ($task->name) {
            //====================================================================//
            //  READING A FILE INFORMATIONS
            case SPL_F_ISFILE:
                //====================================================================//
                //  IF LOCAL SYSTEM PROVIDE FILES
                $local = Splash::local();
                if ($local instanceof FileProviderInterface) {
                    //====================================================================//
                    //  CHECK IF FILE AVAILABLE ON LOCAL SYSTEM
                    $response->data = $local->hasFile($inputs['path'], $inputs['md5']);
                    if ($response->data) {
                        break;
                    }
                }
                $response->data = Splash::file()->isFile($inputs['path'], $inputs['md5']);

                break;
            //====================================================================//
            //  READING A FILE CONTENTS
            case SPL_F_GETFILE:
                //====================================================================//
                //  IF LOCAL SYSTEM PROVIDE FILES
                $local = Splash::local();
                if ($local instanceof FileProviderInterface) {
                    //====================================================================//
                    //  CHECK IF FILE AVAILABLE ON LOCAL SYSTEM
                    $response->data = $local->readFile($inputs['path'], $inputs['md5']);
                    if (is_array($response->data)) {
                        break;
                    }
                }
                $response->data = Splash::file()->readFile($inputs['path'], $inputs['md5']);

                break;
            default:
                Splash::log()->err('File - Requested task not found => '.$task->name);

                break;
        }

        //====================================================================//
        // Task results prot treatment
        if (false != $response->data) {
            $response->result = true;
        }

        return $response;
    }

    /**
     * @abstract   Verify Task Inputs.
     *
     * @param ArrayObject $task Full Task Request Array
     *
     * @return array|false
     */
    private static function validateInputs($task)
    {
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($task->params)) {
            Splash::log()->err('File Router - Missing Task Parameters... ');

            return false;
        }
        //====================================================================//
        // Verify Requested File Path is Available
        $filePath = self::detectFilePath($task->params);
        if (!$filePath) {
            Splash::log()->err('File Router - Missing File Path... ');

            return false;
        }
        //====================================================================//
        // Verify Requested File Md5 is Available (but Says File Missing, for safety)
        if (empty($task->params->md5)) {
            Splash::log()->err('File Router - Missing File Path... ');

            return false;
        }
        //====================================================================//
        // Verify Requested Object Type is Valid
        if (true != Splash::validate()->isValidLocalClass()) {
            Splash::log()->err('File Router - Local Core Class is Invalid... ');

            return false;
        }
        //====================================================================//
        // Return Parameters
        return array(
            'path' => $filePath,
            'md5' => $task->params->md5,
        );
    }

    /**
     * Detect File Path from Parameters.
     *
     * @param ArrayObject $params
     *
     * @return false|string
     */
    private static function detectFilePath($params)
    {
        if (isset($params->path) && !empty($params->path)) {
            return $params->path;
        }
        if (isset($params->file) && !empty($params->file)) {
            return $params->path;
        }

        return false;
    }

    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     *      @abstract     Build an Empty Task Response
     *
     *      @param  ArrayObject     $task       Task To Execute
     *
     *      @return ArrayObject   Task Result ArrayObject
     */
    private static function getEmptyResponse($task)
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
}
