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

namespace Splash\Tests\Tools\Fields;

/**
 * Varchar Field : Basic text
 */
class OoVarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Varchar';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data): ?string
    {
        if (!empty($data) && !is_string($data)) {
            return "Field Data is not a String.";
        }

        return null;
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        //==============================================================================
        //      generate Random String
        $data = preg_replace('/[^A-Za-z\-]/', '', base64_encode((string) mt_rand(900, mt_getrandmax())));
        $data .= preg_replace('/[^A-Za-z\-]/', '', base64_encode((string) mt_rand(900, mt_getrandmax())));

        //==============================================================================
        //      Apply Constraints
        self::applyLengthConstrains($settings, $data);
        self::applyHtmlConstrains($settings, $data);
        self::applyCaseConstrains($settings, $data);

        return $data;
    }

    //==============================================================================
    //      DATA COMPARATOR (OPTIONAL)
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, array $settings): bool
    {
        //====================================================================//
        //  Both Are Empty
        if (empty($source) && empty($target)) {
            return true;
        }
        //====================================================================//
        //  Both Are Scalar
        if (!is_scalar($source) || !is_scalar($target)) {
            return false;
        }
        //====================================================================//
        //  Raw text Compare
        return $source === $target;
    }

    /**
     * Apply Case Constrains
     *
     * @param array  $settings User Defined Faker Settings
     * @param string $data
     *
     * @return void
     */
    public static function applyLengthConstrains(array $settings, string &$data)
    {
        //==============================================================================
        //      Apply Min Length Constraint
        if (isset($settings["minLength"]) && is_numeric($settings["minLength"])) {
            while (strlen($data) < $settings["minLength"]) {
                $data .= preg_replace('/[^A-Za-z\-]/', '', base64_encode((string) mt_rand(900, mt_getrandmax())));
            }
        }
        //==============================================================================
        //      Apply Max Length Constraint
        if (isset($settings["maxLength"]) && is_numeric($settings["maxLength"])) {
            $data = substr($data, 0, (int) $settings["maxLength"]);
        }
    }

    /**
     * Apply Case Constrains
     *
     * @param array  $settings User Defined Faker Settings
     * @param string $data
     *
     * @return void
     */
    public static function applyCaseConstrains(array $settings, string &$data)
    {
        //==============================================================================
        //      Apply Case Constraint
        if (isset($settings["isLowerCase"]) && !empty($settings["isLowerCase"])) {
            $data = strtolower($data);
        }
        if (isset($settings["isUpperCase"]) && !empty($settings["isUpperCase"])) {
            $data = strtoupper($data);
        }
    }

    /**
     * Apply Case Constrains
     *
     * @param array  $settings User Defined Faker Settings
     * @param string $data
     *
     * @return void
     */
    public static function applyHtmlConstrains(array $settings, string &$data)
    {
        //==============================================================================
        //      Apply Case Constraint
        if (isset($settings["isHtml"]) && !empty($settings["isHtml"])) {
            $data = sprintf("<p>%s</p>", $data);
        }
    }
}
