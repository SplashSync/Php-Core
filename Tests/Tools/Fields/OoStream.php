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
 * File Field : Define Access to a Stream File
 *
 * @example
 *
 * //====================================================================//
 * // File Structure
 * // Sample :
 * // $data["file"]["name"]         =>      File Name
 * // $data["file"]["file"]         =>      File Identifier to Require File from Server
 * // $data["file"]["filename"]     =>      File Filename with Extension
 * // $data["file"]["path"]         =>      File Full path on local system
 * // $data["file"]["md5"]          =>      File Md5 Checksum
 * // $data["file"]["size"]         =>      File Size
 * // $data["file"]["ttl"]          =>      Time to Live (in Days)
 * //====================================================================//
 */
class OoStream extends OoFile
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Stream';

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings): array
    {
        //====================================================================//
        // Create a Fake File
        /** @var array $fakeFile */
        $fakeFile = parent::fake($settings);
        //====================================================================//
        // ADD TTL INFOS
        $fakeFile['ttl'] = 3;

        return $fakeFile;
    }

    /**
     * @param array $file
     *
     * @return null|string
     */
    protected static function validateContents(array $file): ?string
    {
        //====================================================================//
        // Execute Validation for File
        $result = parent::validateContents($file);
        if (!$result) {
            return $result;
        }
        //====================================================================//
        // Verify Ttl is Available
        if (!isset($file["ttl"])) {
            return "Stream Field => 'ttl' is missing.";
        }

        return null;
    }
}
