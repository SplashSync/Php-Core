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

namespace   Splash\Router;

use Exception;
use Splash\Components\Router;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\FileProviderInterface;

/**
 * Server Request Routing Class, Execute/Route actions on Files Service Requests.
 * This file is included only in case on NuSOAP call to slave server.
 */
class Files implements RouterInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public static function action(array $task): ?array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Initial Response
        $response = Router::getEmptyResponse($task);
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        $inputs = self::validateInputs($task);
        if (!$inputs) {
            return $response;
        }

        //====================================================================//
        // Execute Action
        switch ($task['name']) {
            case SPL_F_ISFILE:
                //====================================================================//
                //  READING A FILE INFORMATION'S
                //====================================================================//
                //  IF LOCAL SYSTEM PROVIDE FILES
                $local = Splash::local();
                if ($local instanceof FileProviderInterface) {
                    //====================================================================//
                    //  CHECK IF FILE AVAILABLE ON LOCAL SYSTEM
                    $response['data'] = $local->hasFile($inputs['path'], $inputs['md5']);
                    if ($response['data']) {
                        break;
                    }
                }
                $response['data'] = Splash::file()->isFile($inputs['path'], $inputs['md5']);

                break;
            case SPL_F_GETFILE:
                //====================================================================//
                //  READING A FILE CONTENTS
                //====================================================================//
                //  IF LOCAL SYSTEM PROVIDE FILES
                $local = Splash::local();
                if ($local instanceof FileProviderInterface) {
                    //====================================================================//
                    //  CHECK IF FILE AVAILABLE ON LOCAL SYSTEM
                    $response['data'] = $local->readFile($inputs['path'], $inputs['md5']);
                    if (is_array($response['data'])) {
                        break;
                    }
                }
                $response['data'] = Splash::file()->readFile($inputs['path'], $inputs['md5']);

                break;
            default:
                Splash::log()->err('File - Requested task not found => '.$task['name']);

                break;
        }
        //====================================================================//
        // Task results post treatment
        $response['result'] = !empty($response['data']);

        return $response;
    }

    /**
     * Verify Task Inputs.
     *
     * @param array $task Full Task Request Array
     *
     * @return array|false
     */
    private static function validateInputs(array $task)
    {
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($task['params'])) {
            Splash::log()->err('File Router - Missing Task Parameters... ');

            return false;
        }
        //====================================================================//
        // Verify Requested File Path is Available
        $filePath = self::detectFilePath($task['params']);
        if (!$filePath) {
            Splash::log()->err('File Router - Missing File Path... ');

            return false;
        }
        //====================================================================//
        // Verify Requested File Md5 is Available (but Says File Missing, for safety)
        if (empty($task['params']['md5'])) {
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
            'md5' => $task['params']['md5'],
        );
    }

    /**
     * Detect File Path from Parameters.
     *
     * @param array $params
     *
     * @return null|string
     */
    private static function detectFilePath(array $params): ?string
    {
        if (isset($params['path']) && !empty($params['path'])) {
            return (string) $params['path'];
        }
        if (isset($params['file']) && !empty($params['file'])) {
            return (string) $params['file'];
        }

        return null;
    }
}
