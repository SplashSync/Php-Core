<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Splash Core Integrated Translation Management Class. 
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Components;

use Splash\Core\SplashCore      as Splash;

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

/**
 *	\class      SplashTranslator
 *	\brief      Text Translations Functions Collector Class
 */
class Translator
{
    /**
     *      @abstract   Translations Storage Array
     *      @var        Array
     */    
    private     $trans;       
    
    /**
     *      @abstract   Loaded Translation Files array
     *      @var        Array
     */    
    private     $trans_loaded;

	
//====================================================================//
//  TRANSLATIONS MANAGEMENT
//====================================================================//

    
    /**
     *      @abstract       Load translations from a specified INI file into Static array.
     *                      If data for file already loaded, do nothing.
     *                      All data in translation array are stored in UTF-8 format.
     *                      trans_loaded is completed with $file key.
     * 
     *      @param	string	$FileName   File name to load (.ini file). Must be "file" or "file@local" for local language files:
     *                                      If $FileName is "file@local" instead of "file" then we look for local lang file
     *                                      in localpath/langs/code_CODE/file.lang
     * 
     *      @param	string	$Language   Forec Loading of a specific ISO Language Code (Example en_US or fr_FR or es_ES)
     * 
     *      @return	bool
     * 
     */
    public function Load($FileName, $Language = Null)
    {        
        //====================================================================//
        // Check if File is Already in Cache
        //====================================================================//
        if (! empty($this->trans_loaded[$FileName]))	
        {
                Splash::Log()->Deb("MsgLangAlreadyLoad",$FileName);
                return True;
        }

        
        //====================================================================//
        // Check parameters
        if (empty($FileName))   {
            return Splash::Log()->Err("ErrLangFileEmpty");
        }
        
        //====================================================================//
        // Select Language to Load
        if ( $Language == Null ) {
            //====================================================================//
            // Load Default Language from Local System
            if (empty(Splash::Configuration()->DefaultLanguage)) {
                return Splash::Log()->Err(get_class($this)."::Load Translations Error No Default Lang Defined");
            }
            $Language = Splash::Configuration()->DefaultLanguage;
            $IsForced = False;
        } else {
            $IsForced = True;
        }
        //====================================================================//
        // Log Action
        Splash::Log()->Deb(get_class($this)."::Load Translations from <b>" . $FileName . "</b> with Language <b> " . $Language . "</b>");

        //====================================================================//
        // Build Language File Path
        $FullPath = $this->getLangFileName($FileName, $Language);
        
        //====================================================================//
        // Load Language File Translations
        $Loaded = $this->LoadLangFile($FullPath);        
        
        //====================================================================//
        // If Default Language Used
        if ( $IsForced == Null ) {
            
            //====================================================================//
            // Load English Language Fallback Translations
            if ( $Language != SPLASH_DF_LANG ) {
                $this->Load($FileName,SPLASH_DF_LANG);
            }

            //====================================================================//
            // Mark this file as Loaded (1) or Not Found (2)            
            $this->trans_loaded[$FileName] = $Loaded?1:2;
                
        }
        
        return True;
    }

    /**
     *      @abstract   Return text translated of text received as parameter (and encode it into HTML)
     *                  Si il n'y a pas de correspondance pour ce texte, on cherche dans fichier alternatif
     *                  et si toujours pas trouve, il est retourne tel quel
     *                  Les parametres de cette methode peuvent contenir de balises HTML.
     *
     *      @param	string	$key        Key to translate
     *      @param  string	$param1     chaine de param1
     *      @param  string	$param2     chaine de param2
     *      @param  string	$param3     chaine de param3
     *      @param  string	$param4     chaine de param4
     *      @param  string	$param5     chaine de param5
     *      @param	int		$maxsize	Max length of text
     *      @return string      		Translated string (encoded into HTML entities and UTF8)
     */
    function Translate($key, $param1='', $param2='', $param3='', $param4='', $param5='', $maxsize=0)
    {
        //====================================================================//
        // Translation is available
        if (! empty($this->trans[$key]))	
        {
            $str = $this->trans[$key];
            //====================================================================//
            // Replace arrays by counts strings.
            if (is_array($param1) || is_a($param1,"ArrayObject")) {
                $param1 = "x " . count($param1);
            }
            if (is_array($param2) || is_a($param2,"ArrayObject")) {
                $param2 = "x " . count($param2);
            }
            if (is_array($param3) || is_a($param3,"ArrayObject")) {
                $param3 = "x " . count($param3);
            }
            if (is_array($param4) || is_a($param4,"ArrayObject")) {
                $param4 = "x " . count($param4);
            }
            if (is_array($param5) || is_a($param5,"ArrayObject")) {
                $param5 = "x " . count($param5);
            }
                
            //====================================================================//
            // Replace %s and %d except for FormatXXX strings.
            if (!preg_match('/^Format/', $key)) {
                $str = sprintf($str, $param1, $param2, $param3, $param4, $param5);
            }
            //====================================================================//
            // Truncate string if too long.
            if ($maxsize) $str = substr($str,0,$maxsize);
            //====================================================================//
            // We replace some HTML tags by __xx__ to avoid having them encoded by htmlentities
            $str = str_replace(array('<','>','"',),array('__lt__','__gt__','__quot__'),$str);
            //====================================================================//
            // Crypt string into HTML
            $str = htmlentities($str,ENT_QUOTES);
            //====================================================================//
            // Restore HTML tags
            $str = str_replace(array('__lt__','__gt__','__quot__'),array('<','>','"',),$str);

        return $str;
        //====================================================================//
        // Translation is not available
        } else {
            return $key;
        }
    }        

    
    /**
     *      @abstract       Build Translation filename based on specified $file and ISO Language Code.
     * 
     *      @param	string	$FileName   File name to load (.ini file). Must be "file" or "file@local" for local language files:
     *                                      If $FileName is "file@local" instead of "file" then we look for local lang file
     *                                      in localpath/langs/code_CODE/file.lang
     * 
     *      @param	string	$Language   ISO Language Code (Example en_US or fr_FR or es_ES)
     * 
     *      @return	bool
     * 
     */
    private function getLangFileName($FileName, $Language)
    {
        
        //====================================================================//
        // Search for Local Redirection
        //====================================================================//
        $IsLocal    =   '';
        $regs       =   Null;
        //====================================================================//
        // Search if a local directory is required into lang file name
        if (preg_match('/^([^@]+)@([^@]+)$/i',$FileName,$regs))
        {
                $FileName = $regs[1];
                $IsLocal = $regs[2];
        }

        //====================================================================//
        // Directory of translation files
        if ( !empty($IsLocal) )
        {
            return Splash::getLocalPath()."/Translations/".$Language."/".$FileName.".ini";
        }
        
        return dirname(dirname(__FILE__)) . "/langs/" . $Language . "/" . $FileName . ".ini";
    }
    
    /**
     *      @abstract   Load Speficied file onto static language collection
     * 
     *      @param	string	$FullPath   Full path to language file to load (.ini file).
     * 
     *      @return	bool
     * 
     */
    private function LoadLangFile($FullPath)
    {
        //====================================================================//
        // Check if File Exists
        if (!is_file($FullPath)) {
            return False;
        }

        //====================================================================//
        // Open File
        $fp = @fopen($FullPath,"rt");
        if ( !$fp ) {
            return False;
        }

        //====================================================================//
        // Import All New Translation Keys
        // Ex: Need 225ms for all fgets on all lang file for Third party page. Same speed than file_get_contents
        while ($line = fgets($fp,4096))	
        {
            //====================================================================//
            // Filter empty lines
            if ( ($line[0] == "\n") || ($line[0] == " ") || ($line[0] == "#") || ($line[0] == ";") )
            {
                continue;
            }                
            //====================================================================//
            // Explode Lines
            $tab=explode('=',$line,2);
            $key=trim($tab[0]);
            //====================================================================//
            // Debug Line
            //print "Domain=$file, found a string for $tab[0] with value $tab[1]<br>";
            //====================================================================//
            // If translation was already found, we must not continue
            if ( empty($this->trans[$key]) && isset($tab[1]) )    
            {
                //====================================================================//
                // Store Line Translation Key
                $value=trim(preg_replace('/\\n/',"\n",$tab[1]));
                $this->trans[$key]=$value;
            }
        }
        
        //====================================================================//
        // Close File
        fclose($fp);

    }    
    
}
?>