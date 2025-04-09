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

namespace Splash\Core\Server\Router;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Components\Router;
use Splash\Core\Dictionary\Methods\SplFilesMethods as Methods;
use Splash\Core\Interfaces\FileProviderInterface;
use Splash\Core\Interfaces\Server\RouterInterface;

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
            case Methods::EXISTS:
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
            case Methods::GET:
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
     * @return null|array
     */
    private static function validateInputs(array $task): ?array
    {
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($task['params'])) {
            Splash::log()->err('File Router - Missing Task Parameters... ');

            return null;
        }
        //====================================================================//
        // Verify Requested File Path is Available
        $filePath = self::detectFilePath($task['params']);
        if (!$filePath) {
            Splash::log()->err('File Router - Missing File Path... ');

            return null;
        }
        //====================================================================//
        // Verify Requested File Md5 is Available (but Says File Missing, for safety)
        if (empty($task['params']['md5'])) {
            Splash::log()->err('File Router - Missing File Path... ');

            return null;
        }
        //====================================================================//
        // Verify Requested Object Type is Valid
        if (!Splash::validate()->isValidLocalClass()) {
            Splash::log()->err('File Router - Local Core Class is Invalid... ');

            return null;
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
        if (!empty($params['path'])) {
            return (string) $params['path'];
        }
        if (!empty($params['file'])) {
            return (string) $params['file'];
        }

        return null;
    }
}
