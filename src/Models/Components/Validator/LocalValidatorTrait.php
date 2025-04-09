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

namespace Splash\Core\Models\Components\Validator;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplDefinition;
use Splash\Core\Interfaces\Local\LocalClassInterface;

/**
 * Collection of Validator Methods Focused on Local Class
 */
trait LocalValidatorTrait
{
    /**
     * List of Validated Local Class
     *
     * @var array
     */
    private array $validLocalClass = array();

    /**
     * Is Local Path Validated ?
     *
     * @var null|bool
     */
    private ?bool $validLocalPath = null;

    /**
     * Verify Local Core Class Exists & Is Valid
     *
     * @return bool
     */
    public function isValidLocalClass(): bool
    {
        //====================================================================//
        // Guess Local Class Name
        $className = SplDefinition::CLASS_PREFIX.'\\Local';
        //====================================================================//
        // Verify Results in Cache
        if (isset($this->validLocalClass[$className])) {
            return $this->validLocalClass[$className];
        }
        $this->validLocalClass[$className] = false;
        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (!class_exists($className)) {
            return Splash::log()->err(Splash::trans('ErrLocalClass', $className));
        }
        //====================================================================//
        // Verify Splash Local Core Extends LocalClassInterface
        if (!is_subclass_of($className, LocalClassInterface::class)) {
            return Splash::log()->err(
                Splash::trans('ErrLocalClass', $className, LocalClassInterface::class)
            );
        }

        return $this->validLocalClass[$className] = true;
    }

    /**
     * Verify Local Path Exists
     */
    public function isValidLocalPath(): bool
    {
        //====================================================================//
        // Verify no result in Cache
        if (!isset($this->validLocalPath)) {
            //====================================================================//
            // Guess Local Path from Local Class Location
            $path = Splash::getLocalPath();
            //====================================================================//
            // Verify Local Path Exist
            if (is_null($path) || !is_dir($path)) {
                $this->validLocalPath = false;

                return Splash::log()->err(Splash::trans('ErrLocalPath', (string) $path));
            }

            $this->validLocalPath = true;
        }

        return $this->validLocalPath;
    }
}
