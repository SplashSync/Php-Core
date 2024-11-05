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

namespace Splash\Components;

use Exception;
use Splash\Core\SplashCore as Splash;

/**
 * Splash Core Integrated Translation Management Class.
 */
class Translator
{
    /**
     * Translations Storage Array
     *
     * @var array
     */
    private array $trans = array();

    /**
     * Loaded Translation Files array
     *
     * @var array
     */
    private array $loadedTranslations = array();

    //====================================================================//
    //  TRANSLATIONS MANAGEMENT
    //====================================================================//

    /**
     * Load translations from a specified INI file into Static array.
     * If data for file already loaded, do nothing.
     * All data in translation array are stored in UTF-8 format.
     * trans_loaded is completed with $file key.
     *
     * @param string      $fileName File name to load (.ini file).
     *                              Must be "file" or "file@local" for local language files:
     *                              If $FileName is "file@local" instead of "file" then we look for local lang file
     *                              in local path/langs/code_CODE/file.lang
     * @param null|string $language Force Loading of a specific ISO Language Code (Example en_US or fr_FR or es_ES)
     *
     * @return bool
     */
    public function load(string $fileName, string $language = null): bool
    {
        //====================================================================//
        // Check if File is Already in Cache
        //====================================================================//
        if (!empty($this->loadedTranslations[$fileName])) {
            return true;
        }

        //====================================================================//
        // Check parameters
        if (empty($fileName)) {
            return Splash::log()->err("ErrLangFileEmpty");
        }

        //====================================================================//
        // Select Language to Load
        if (null == $language) {
            //====================================================================//
            // Load Default Language from Local System
            if (empty(Splash::configuration()->DefaultLanguage)) {
                return Splash::log()->err(get_class($this)."::Load Translations Error No Default Lang Defined");
            }
            $language = Splash::configuration()->DefaultLanguage;
            $isForced = false;
        } else {
            $isForced = true;
        }
        //====================================================================//
        // Log Action
        Splash::log()->deb(
            get_class($this)."::Load Translations from ".$fileName." with Language ".$language."."
        );

        //====================================================================//
        // Build Language File Path
        $fullPath = $this->getLangFileName($fileName, $language);

        //====================================================================//
        // Load Language File Translations
        $loaded = $this->loadLangFile($fullPath);

        //====================================================================//
        // If Default Language Used
        if (null == $isForced) {
            //====================================================================//
            // Load English Language Fallback Translations
            if (SPLASH_DF_LANG != $language) {
                $this->load($fileName, SPLASH_DF_LANG);
            }

            //====================================================================//
            // Mark this file as Loaded (1) or Not Found (2)
            $this->loadedTranslations[$fileName] = $loaded?1:2;
        }

        return true;
    }

    /**
     * Return text translated of text received as parameter (and encode it into HTML)
     * Si il n'y a pas de correspondance pour ce texte, on cherche dans fichier alternatif
     * et si toujours pas trouve, il est retourne tel quel
     * Les parameters de cette methode peuvent contenir de balises HTML.
     *
     * @param string $key     Key to translate
     * @param string $param1  chaine de param1
     * @param string $param2  chaine de param2
     * @param string $param3  chaine de param3
     * @param string $param4  chaine de param4
     * @param int    $maxsize Max length of text
     *
     * @return string Translated string (encoded into HTML entities and UTF8)
     */
    public function translate(
        string $key,
        string $param1 = '',
        string $param2 = '',
        string $param3 = '',
        string $param4 = '',
        int $maxsize = 0
    ): string {
        //====================================================================//
        // Translation is not available
        if (!isset($this->trans[$key]) || empty($this->trans[$key])) {
            return $key;
        }
        //====================================================================//
        // Translation is available
        $str = $this->trans[$key];
        //====================================================================//
        // Replace %s and %d except for FormatXXX strings.
        if (!preg_match('/^Format/', $key)) {
            $str = sprintf($str, $param1, $param2, $param3, $param4);
        }
        //====================================================================//
        // Truncate string if too long.
        if ($maxsize) {
            $str = substr($str, 0, $maxsize);
        }
        //====================================================================//
        // We replace some HTML tags by __xx__ to avoid having them encoded by html entities
        $str = str_replace(array('<','>','"',), array('__lt__','__gt__','__quot__'), $str);
        //====================================================================//
        // Crypt string into HTML
        $str = htmlentities($str, ENT_QUOTES);

        //====================================================================//
        // Restore HTML tags
        return str_replace(array('__lt__','__gt__','__quot__'), array('<','>','"',), $str);
    }

    /**
     * Build Translation filename based on specified $file and ISO Language Code.
     *
     * @param string $fileName File name to load (.ini file).
     *                         Must be "file" or "file@local" for local language files:
     *                         If $FileName is "file@local" instead of "file" then we look for local lang file
     *                         in local path/langs/code_CODE/file.lang
     * @param string $language ISO Language Code (Example en_US or fr_FR or es_ES)
     *
     * @return string
     */
    private function getLangFileName(string $fileName, string $language): string
    {
        //====================================================================//
        // Search for Local Redirection
        //====================================================================//
        $isLocal = '';
        $regs = null;
        //====================================================================//
        // Search if a local directory is required into lang file name
        if (preg_match('/^([^@]+)@([^@]+)$/i', $fileName, $regs)) {
            $fileName = $regs[1];
            $isLocal = $regs[2];
        }

        //====================================================================//
        // Directory of translation files
        if (!empty($isLocal)) {
            try {
                return Splash::getLocalPath()."/Translations/".$language."/".$fileName.".ini";
            } catch (Exception $ex) {
                return dirname(__FILE__, 2)."/langs/".$language."/".$fileName.".ini";
            }
        }

        return dirname(__FILE__, 2)."/langs/".$language."/".$fileName.".ini";
    }

    /**
     * Load Specified file onto static language collection
     *
     * @param string $fullPath Full path to language file to load (.ini file).
     *
     * @return bool
     */
    private function loadLangFile(string $fullPath): bool
    {
        //====================================================================//
        // Check if File Exists
        if (!is_file($fullPath)) {
            return false;
        }
        //====================================================================//
        // Open File
        $file = @fopen($fullPath, "rt");
        if (!$file) {
            return false;
        }
        //====================================================================//
        // Import All New Translation Keys
        while ($line = fgets($file, 4096)) {
            $this->loadLangLine($line);
        }
        //====================================================================//
        // Close File
        fclose($file);

        return true;
    }

    /**
     * @param string $line
     *
     * @return void
     */
    private function loadLangLine(string $line): void
    {
        //====================================================================//
        // Filter empty lines
        if (("\n" == $line[0]) || (" " == $line[0]) || ("#" == $line[0]) || (";" == $line[0])) {
            return;
        }
        //====================================================================//
        // Explode Lines
        $tab = explode('=', $line, 2);
        $key = trim($tab[0]);
        //====================================================================//
        // Debug Line
        //print "Domain=$file, found a string for $tab[0] with value $tab[1]<br>";
        //====================================================================//
        // If translation was already found, we must not continue
        if (empty($this->trans[$key]) && isset($tab[1])) {
            //====================================================================//
            // Store Line Translation Key
            $value = trim((string) preg_replace('/\\n/', "\n", $tab[1]));
            $this->trans[$key] = $value;
        }
    }
}
