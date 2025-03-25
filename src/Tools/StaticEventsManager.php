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

namespace Splash\Framework\Tools;

/**
 * Minimal Events Manager for Splash Micro Framework
 *
 * Register callback for being executed on, specific system events
 */
class StaticEventsManager
{
    /**
     * @var array<string, array<int, array{callback: callable, priority: int}>> Stores callbacks grouped by context
     */
    private static array $callbacks = array();

    /**
     * Registers a new action for a specific context with a given priority.
     *
     * @param class-string $context  The context under which the callback is registered.
     * @param array        $action   The callable function to execute.
     * @param int          $priority The priority of the callback (higher values = higher priority).
     *
     * @return void
     */
    public static function register(string $context, array $action, int $priority = 0): void
    {
        self::registerCallable($context, $action, $priority);
    }

    /**
     * Registers a new callback for a specific context with a given priority.
     *
     * @param class-string $context  The context class under which the callback is registered.
     * @param callable     $callback The callback function to execute.
     * @param int          $priority The priority of the callback (higher values = higher priority).
     *
     * @return void
     */
    public static function registerCallback(string $context, callable $callback, int $priority = 0): void
    {
        self::registerCallable($context, $callback, $priority);
    }

    /**
     * Executes all registered callbacks for a specific context in order of priority.
     *
     * @param object $context The context object for which callbacks should be executed.
     */
    public static function execute(object $context): object
    {
        $contextClass = get_class($context);
        //====================================================================//
        // No callbacks registered for this context
        if (!isset(self::$callbacks[$contextClass])) {
            return $context;
        }

        //====================================================================//
        // Execute the callback and pass the context as argument
        foreach (self::$callbacks[$contextClass] as $entry) {
            call_user_func($entry['callback'], $context);
        }

        return $context;
    }

    /**
     * Clears registered callbacks for a specific context, or for all contexts if none is provided.
     *
     * @param null|string $context The context to clear (null to clear all contexts).
     */
    public static function clearCallbacks(string $context = null): void
    {
        if (null === $context) {
            //====================================================================//
            // Clear all callbacks
            self::$callbacks = array();
        } else {
            //====================================================================//
            // Clear callbacks for a specific context
            unset(self::$callbacks[$context]);
        }
    }

    /**
     * Registers a new callback for a specific context with a given priority.
     *
     * @param class-string   $context  The context class under which the callback is registered.
     * @param array|callable $callback The callback function to execute.
     * @param int            $priority The priority of the callback (higher values = higher priority).
     *
     * @return void
     */
    private static function registerCallable(string $context, $callback, int $priority = 0): void
    {
        //====================================================================//
        // Ensure Context Class Exists
        assert(
            class_exists($context),
            sprintf("Context Class %s does not exists", $context)
        );
        //====================================================================//
        // Ensure Callback is Valid
        assert(
            is_callable($callback),
            sprintf("Callback %s is not callable", print_r($callback, true))
        );
        //====================================================================//
        // Ensure Context Init
        if (!isset(self::$callbacks[$context])) {
            self::$callbacks[$context] = array();
        }
        //====================================================================//
        // Register Callback
        self::$callbacks[$context][] = array(
            'callback' => $callback,
            'priority' => $priority,
        );
        //====================================================================//
        // Sort callbacks by priority (higher priority first)
        usort(self::$callbacks[$context], function ($valueA, $valueB) {
            return $valueB['priority'] <=> $valueA['priority'];
        });
    }
}
