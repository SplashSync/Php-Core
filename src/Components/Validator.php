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

namespace Splash\Core\Components;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Helpers\System\ServerInfos;
use Splash\Core\Helpers\System\SystemChecker;
use Splash\Core\Interfaces\ConfiguratorInterface;
use Splash\Core\Models\Components\Validator\LocalValidatorTrait;
use Splash\Core\Models\Components\Validator\ObjectsValidatorTrait;
use Splash\Core\Models\Components\Validator\WidgetsValidatorTrait;

/**
 * Tooling Class for Validation of Splash Php Module Contents
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Validator
{
    use LocalValidatorTrait;
    use ObjectsValidatorTrait;
    use WidgetsValidatorTrait;

    /**
     * Verify Local Core Parameters are Valid
     *
     * @param array $input
     *
     * @return bool
     */
    public function isValidParameterArray(array $input): bool
    {
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
     * Verify Webserver Informations are Valid
     *
     * @return bool
     */
    public function isValidServerInfos(): bool
    {
        //====================================================================//
        // Collect Server Infos
        $infos = ServerInfos::getInfos();
        //====================================================================//
        // Show Debug Infos
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
     * @param array $infos
     *
     * @return void
     */
    public function isLocalInstallation(array $infos): void
    {
        if (false !== strpos($infos['ServerHost'], 'localhost')) {
            Splash::log()->war(Splash::trans('WarIsLocalhostServer'));
        } elseif (false !== strpos($infos['ServerIP'] ?? "", '127.0.0.1')) {
            Splash::log()->war(Splash::trans('WarIsLocalhostServer'));
        }

        if ('https' === Splash::input('REQUEST_SCHEME')) {
            Splash::log()->msg(Splash::trans('WarIsHttpsServer'));
        }
    }

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE LOCAL SERVER
    // *******************************************************************//
    //====================================================================//

    /**
     * Verify System Configuration.
     *
     * @return bool
     */
    public function isValidSystem(): bool
    {
        return SystemChecker::isValidPHPVersion()
            && SystemChecker::isValidPHPExtensions()
        ;
    }

    /**
     * Verify WebService Library is Valid.
     *
     * @return bool
     */
    public function isValidSOAPMethod(): bool
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
     */
    public function isValidConfigurator(string $className): bool
    {
        //====================================================================//
        // Verify Class Exists
        if (!class_exists($className)) {
            return Splash::log()->err('Configurator Class Not Found: '.$className);
        }

        //====================================================================//
        // Verify Configurator Class Extends ConfiguratorInterface
        try {
            $class = new $className();
            if (!($class instanceof ConfiguratorInterface)) {
                return Splash::log()->err(
                    Splash::trans(
                        'ErrLocalInterface',
                        $className,
                        ConfiguratorInterface::class
                    )
                );
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();

            return Splash::log()->err($exc->getMessage());
        }

        return true;
    }
}
