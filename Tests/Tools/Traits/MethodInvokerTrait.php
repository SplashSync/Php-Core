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

namespace Splash\Tests\Tools\Traits;

use ReflectionException;

/**
 * Invoke Private Class Methods during Test Cases
 */
trait MethodInvokerTrait
{
    /**
     * Call protected/private method of a class.
     *
     * @param object $object     Instantiated object that we will run method on.
     * @param string $methodName Method to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(object &$object, string $methodName, array $parameters = array())
    {
        try {
            $reflection = new \ReflectionClass(get_class($object));
            $method = $reflection->getMethod($methodName);
            $method->setAccessible(true);

            return $method->invokeArgs($object, $parameters);
        } catch (ReflectionException $e) {
            return null;
        }
    }

    /**
     * Call protected/private method of a class.
     *
     * @param class-string $class      Class that we will run method on.
     * @param string       $methodName Method to call
     * @param array        $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethodStatic(string $class, string $methodName, array $parameters = array())
    {
        try {
            $reflection = new \ReflectionClass($class);
            $method = $reflection->getMethod($methodName);
            $method->setAccessible(true);

            return $method->invokeArgs(null, $parameters);
        } catch (ReflectionException $e) {
            return null;
        }
    }
}
