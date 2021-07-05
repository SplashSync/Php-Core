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

namespace   Splash\Components;

use ArrayObject;
use Exception;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\ConfiguratorInterface;
use Splash\Models\LocalClassInterface;
use Splash\Models\Objects\ObjectInterface;
use Splash\Models\ObjectsProviderInterface;
use Splash\Models\Widgets\WidgetInterface;
use Splash\Models\WidgetsProviderInterface;

/**
 * Tooling Class for Validation of Splash Php Module Contents
 *
 * @author      B. Paquier <contact@splashsync.com>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Validator
{
    /** @var array */
    private $ValidLocalClass;
    /** @var array */
    private $ValidLocalObject;
    /** @var array */
    private $ValidLocalWidget;
    /** @var bool */
    private $ValidLocalPath;
    /** @var array */
    private $ValidLocalFunctions;

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE LOCAL CORE CLASS & FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify Local Core Class Exists & Is Valid
     *
     * @return bool
     */
    public function isValidLocalClass()
    {
        $className = SPLASH_CLASS_PREFIX.'\\Local';
        //====================================================================//
        // Verify Results in Cache
        if (isset($this->ValidLocalClass[$className])) {
            return $this->ValidLocalClass[$className];
        }

        $this->ValidLocalClass[$className] = false;

        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (false == class_exists($className)) {
            return Splash::log()->err(Splash::trans('ErrLocalClass', $className));
        }

        //====================================================================//
        // Verify Splash Local Core Extends LocalClassInterface
        try {
            $class = new $className();
            if (!($class instanceof LocalClassInterface)) {
                return Splash::log()->err(Splash::trans('ErrLocalInterface', $className, LocalClassInterface::class));
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();

            return Splash::log()->err($exc->getMessage());
        }

        $this->ValidLocalClass[$className] = true;

        return $this->ValidLocalClass[$className];
    }

    /**
     * Verify Local Core Parameters are Valid
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function isValidLocalParameterArray($input)
    {
        //====================================================================//
        // Verify Array Given
        if (!is_array($input)) {
            return Splash::log()->err(Splash::trans('ErrorCfgNotAnArray', get_class($input)));
        }

        //====================================================================//
        // Required Parameters are Available
        //====================================================================//
        if (!array_key_exists('WsIdentifier', $input)) {
            return Splash::log()->err(Splash::trans('ErrWsNoId'));
        }

        if (!array_key_exists('WsEncryptionKey', $input)) {
            return Splash::log()->err(Splash::trans('ErrWsNoKey'));
        }

        return true;
    }

    /**
     * Verify Local Test Parameters are Valid
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function isValidLocalTestParameterArray($input)
    {
        //====================================================================//
        // Verify Array Given
        if (!is_array($input)) {
            return Splash::log()->err(Splash::trans('ErrorCfgNotAnArray', get_class($input)));
        }

        return true;
    }

    /**
     * Verify Webserver Informations are Valid
     *
     * @return bool
     */
    public function isValidServerInfos()
    {
        $infos = Splash::ws()->getServerInfos();

        //====================================================================//
        // Verify Array Given
        if (!is_a($infos, 'ArrayObject')) {
            return Splash::log()->err(
                Splash::trans('ErrInfosNotArrayObject', (string) get_class($infos))
            );
        }

        if (Splash::isDebugMode()) {
            Splash::log()->war('Host : '.$infos['ServerHost']);
            Splash::log()->war('Path : '.$infos['ServerPath']);
        }

        //====================================================================//
        // Required Parameters are Available
        //====================================================================//
        if (empty($infos['ServerHost'])) {
            Splash::log()->err(Splash::trans('ErrEmptyServerHost'));

            return Splash::log()->err(Splash::trans('ErrEmptyServerHostDesc'));
        }

        if (empty($infos['ServerPath'])) {
            Splash::log()->err(Splash::trans('ErrEmptyServerPath'));

            return Splash::log()->err(Splash::trans('ErrEmptyServerPathDesc'));
        }

        //====================================================================//
        // Detect Local Installations
        //====================================================================//
        $this->isLocalInstallation($infos);

        return true;
    }

    /**
     * Verify Webserver is a LocalHost
     *
     * @param mixed $infos
     *
     * @return void
     */
    public function isLocalInstallation($infos)
    {
        if (false !== strpos($infos['ServerHost'], 'localhost')) {
            Splash::log()->war(Splash::trans('WarIsLocalhostServer'));
        } elseif (false !== strpos($infos['ServerIP'], '127.0.0.1')) {
            Splash::log()->war(Splash::trans('WarIsLocalhostServer'));
        }

        if ('https' === Splash::input('REQUEST_SCHEME')) {
            Splash::log()->msg(Splash::trans('WarIsHttpsServer'));
        }
    }

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE OBJECTS CLASSES & FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify this parameter is a valid object type name
     *
     * @param string $objectType Object Class/Type Name
     *
     * @return bool
     */
    public function isValidObject($objectType)
    {
        //====================================================================//
        // Verify Result in Cache
        if (isset($this->ValidLocalObject[$objectType])) {
            return $this->ValidLocalObject[$objectType];
        }

        $this->ValidLocalObject[$objectType] = false;

        //====================================================================//
        // Verify Local Core Class Exist & Is Valid
        if (!$this->isValidLocalClass()) {
            return false;
        }
        //====================================================================//
        // Check if Object Manager is NOT Overriden
        if (!(Splash::local() instanceof ObjectsProviderInterface)) {
            //====================================================================//
            // Verify Object File Exist & is Valid
            if (!$this->isValidObjectFile($objectType)) {
                return false;
            }
        }
        //====================================================================//
        // Verify Object Class Exist & is Valid
        if (!$this->isValidObjectClass($objectType)) {
            return false;
        }

        $this->ValidLocalObject[$objectType] = true;

        return true;
    }

    /**
     * Verify this Object Type is valid in Local Syetem
     *
     * @param string $objectType Object Type Name String
     *
     * @return bool
     */
    public static function isValidObjectType($objectType)
    {
        //====================================================================//
        // Verify Type Name is in List
        return in_array($objectType, Splash::objects(), true);
    }

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE OBJECTS I/O
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify Object Identifier
     *
     * @param mixed $objectId Object Identifier
     *
     * @return bool
     */
    public function isValidObjectId($objectId)
    {
        //====================================================================//
        // Checks Id is not Null
        if (is_null($objectId)) {
            return Splash::log()->err('ErrEmptyObjectId');
        }
        //====================================================================//
        // Checks Id is String or Int
        if (!is_string($objectId) && !is_numeric($objectId)) {
            return Splash::log()->err('ErrWrongObjectId');
        }
        //====================================================================//
        // Checks List Not Empty
        if (is_numeric($objectId) && ($objectId < 0)) {
            return Splash::log()->err('ErrNegObjectId');
        }

        return Splash::log()->deb('MsgObjectIdOk');
    }

    /**
     * Verify Object Field List
     *
     * @param null|array|ArrayObject $fieldsList Object Field List
     *
     * @return bool
     */
    public function isValidObjectFieldsList($fieldsList)
    {
        //====================================================================//
        // Checks List Type
        if (!is_array($fieldsList) && !($fieldsList instanceof ArrayObject)) {
            return Splash::log()->err('ErrWrongFieldList');
        }
        //====================================================================//
        // Checks List Not Empty
        if (empty($fieldsList)) {
            return Splash::log()->err('ErrEmptyFieldList');
        }

        return Splash::log()->deb('MsgFieldListOk');
    }

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE WIDGETS CLASSES & FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify this parameter is a valid widget type name
     *
     * @param string $widgetType Widget Class/Type Name
     *
     * @return bool
     */
    public function isValidWidget($widgetType)
    {
        //====================================================================//
        // Verify Result in Cache
        if (isset($this->ValidLocalWidget[$widgetType])) {
            return $this->ValidLocalWidget[$widgetType];
        }
        $this->ValidLocalWidget[$widgetType] = false;

        //====================================================================//
        // Verify Local Core Class Exist & Is Valid
        if (!$this->isValidLocalClass()) {
            return false;
        }
        //====================================================================//
        // Check if Widget Manager is NOT Overriden
        if (!(Splash::local() instanceof WidgetsProviderInterface)) {
            //====================================================================//
            // Verify Widget File Exist & is Valid
            if (!$this->isValidWidgetFile($widgetType)) {
                return false;
            }
        }

        //====================================================================//
        // Verify Widget Class Exist & is Valid
        if (!$this->isValidWidgetClass($widgetType)) {
            return false;
        }
        $this->ValidLocalWidget[$widgetType] = true;

        return true;
    }

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE COMMONS FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify Local Path Exists
     *
     * @return bool
     */
    public function isValidLocalPath()
    {
        //====================================================================//
        // Verify no result in Cache
        if (!isset($this->ValidLocalPath)) {
            $path = Splash::getLocalPath();
            //====================================================================//
            // Verify Local Path Exist
            if (is_null($path) || !is_dir($path)) {
                $this->ValidLocalPath = false;

                return Splash::log()->err(Splash::trans('ErrLocalPath', (string) $path));
            }

            $this->ValidLocalPath = true;
        }

        return $this->ValidLocalPath;
    }

    /**
     * Verify Availability of a local method/function prior to task execution.
     *
     * @param string $method    Function Name
     * @param string $className Optionnal Class Name
     * @param bool   $required  Indicate this Function is Required by Module (Or Optional)
     *
     * @return bool
     */
    public function isValidLocalFunction($method, $className = null, $required = true)
    {
        //====================================================================//
        // Prefill ClassName
        if (is_null($className)) {
            $className = SPLASH_CLASS_PREFIX.'\\Local';
        }
        //====================================================================//
        // Verify Result in Cache
        if (isset($this->ValidLocalFunctions[$className][$method])) {
            return $this->ValidLocalFunctions[$className][$method];
        }

        //====================================================================//
        // Verify Class Method Exists
        if (false == method_exists($className, $method)) {
            $this->ValidLocalFunctions[$className][$method] = false;

            return $required ? Splash::log()->err(Splash::trans('ErrLocalFunction', $className, $method)) : false;
        }
        $this->ValidLocalFunctions[$className][$method] = true;

        return $this->ValidLocalFunctions[$className][$method];
    }

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE LOCAL SERVER
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify PHP Version is Compatible.
     *
     * @return bool
     */
    public function isValidPHPVersion()
    {
        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            return Splash::log()->err(
                'PHP : Your PHP version is too low to use Splash ('.PHP_VERSION.'). PHP >5.6 is Requiered.'
            );
        }

        return Splash::log()->msg(
            'PHP : Your PHP version is compatible with Splash ('.PHP_VERSION.')'
        );
    }

    /**
     * Verify PHP Required are Installed & Active
     *
     * @return bool
     */
    public function isValidPHPExtensions()
    {
        $extensions = array('xml', 'soap', 'curl');
        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                return Splash::log()->err(
                    'PHP :'.$extension.' PHP Extension is required to use Splash PHP Module.'
                );
            }
        }

        return Splash::log()->msg(
            'PHP : Required PHP Extension are installed ('.implode(', ', $extensions).')'
        );
    }

    /**
     * Verify WebService Library is Valid.
     *
     * @return bool
     */
    public function isValidSOAPMethod()
    {
        if (!in_array(Splash::configuration()->WsMethod, array('SOAP', 'NuSOAP'), true)) {
            return Splash::log()->err(
                'Config : Your selected an unknown SOAP Method ('.Splash::configuration()->WsMethod.').'
            );
        }

        return Splash::log()->msg(
            'Config : SOAP Method is Ok ('.Splash::configuration()->WsMethod.').'
        );
    }

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE LOCAL CONFIGURATOR CLASS
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify Given Class Is a Valid Splash Configurator
     *
     * @param string $className Configurator Class Name
     *
     * @return bool
     */
    public function isValidConfigurator($className)
    {
        //====================================================================//
        // Verify Class Exists
        if (false == class_exists($className)) {
            return Splash::log()->err('Configurator Class Not Found: '.$className);
        }

        //====================================================================//
        // Verify Configurator Class Extends ConfiguratorInterface
        try {
            $class = new $className();
            if (!($class instanceof ConfiguratorInterface)) {
                return Splash::log()->err(Splash::trans('ErrLocalInterface', $className, ConfiguratorInterface::class));
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();

            return Splash::log()->err($exc->getMessage());
        }

        return true;
    }

    //====================================================================//
    // *******************************************************************//
    //  PRIVATE & CORE FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify a Local Object File is Valid.
     *
     * @param string $objectType Object Type Name
     *
     * @return bool
     */
    private function isValidObjectFile($objectType)
    {
        //====================================================================//
        // Verify Local Path Exist
        if (false == $this->isValidLocalPath()) {
            return false;
        }

        //====================================================================//
        // Verify Object File Exist
        $filename = Splash::getLocalPath().'/Objects/'.$objectType.'.php';
        if (false == file_exists($filename)) {
            $msg = 'Local Object File Not Found.</br>';
            $msg .= 'Current Filename : '.$filename.'';

            return Splash::log()->err($msg);
        }

        return true;
    }

    /**
     * Verify Availability of a Local Object Class.
     *
     * @param string $objectType Object Type Name
     *
     * @return bool
     */
    private function isValidObjectClass($objectType)
    {
        //====================================================================//
        // Check if Object Manager is Overriden
        if (Splash::local() instanceof ObjectsProviderInterface) {
            //====================================================================//
            // Retrieve Object Manager ClassName
            $className = get_class(Splash::local()->object($objectType));
        } else {
            $className = SPLASH_CLASS_PREFIX.'\\Objects\\'.$objectType;
        }
        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (false == class_exists($className)) {
            return Splash::log()->err(Splash::trans('ErrLocalClass', $objectType));
        }

        //====================================================================//
        // Verify Local Object Core Class Functions Exists
        //====================================================================//

        //====================================================================//
        // Read Object Disable Flag
        if (false == $this->isValidLocalFunction('getIsDisabled', $className)) {
            return false;
        }
        if (Splash::configurator()->isDisabled($objectType, $className::getIsDisabled())) {
            return false;
        }

        //====================================================================//
        // Verify Local Object Class Implements ObjectInterface
        return is_subclass_of($className, ObjectInterface::class);
    }

    /**
     * Verify a Local Widget File is Valid.
     *
     * @param string $widgetType Widget Type Name
     *
     * @return bool
     */
    private function isValidWidgetFile($widgetType)
    {
        //====================================================================//
        // Verify Local Path Exist
        if (false == $this->isValidLocalPath()) {
            return false;
        }
        //====================================================================//
        // Verify Object File Exist
        $filename = Splash::getLocalPath().'/Widgets/'.$widgetType.'.php';
        if (false == file_exists($filename)) {
            $msg = 'Local Widget File Not Found.</br>';
            $msg .= 'Current Filename : '.$filename.'';

            return Splash::log()->err($msg);
        }

        return true;
    }

    /**
     * Verify Availability of a Local Widget Class.
     *
     * @param string $widgetType Widget Type Name
     *
     * @return bool
     */
    private function isValidWidgetClass($widgetType)
    {
        //====================================================================//
        // Check if Widget Manager is Overriden
        if (Splash::local() instanceof WidgetsProviderInterface) {
            //====================================================================//
            // Retrieve Widget Manager ClassName
            $className = get_class(Splash::local()->widget($widgetType));
        } else {
            $className = SPLASH_CLASS_PREFIX.'\\Widgets\\'.$widgetType;
        }

        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (false == class_exists($className)) {
            return Splash::log()->err(Splash::trans('ErrLocalClass', $widgetType));
        }

        //====================================================================//
        // Verify Local Widget Core Class Functions Exists
        //====================================================================//

        //====================================================================//
        // Read Object Disable Flag
        if (false == $this->isValidLocalFunction('getIsDisabled', $className)) {
            $this->ValidLocalWidget[$widgetType] = false;

            return false;
        }
        if ($className::getIsDisabled()) {
            $this->ValidLocalWidget[$widgetType] = false;

            return false;
        }

        //====================================================================//
        // Verify Local Object Class Implements WidgetInterface
        return is_subclass_of($className, WidgetInterface::class);
    }
}
