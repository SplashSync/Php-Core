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

namespace Splash\Core\Models\Components\FieldsFactory;

use Splash\Core\Interfaces\Fields\FieldInterface;
use Splash\Core\Models\AbstractConfigurator;

/**
 * Manage Configurators for Fields factory
 *
 * @phpstan-import-type FIELD from FieldInterface
 */
trait ConfiguratorsTrait
{
    /**
     * Fields Configurators
     *
     * @var array
     */
    private array $configurators = array();

    /**
     * Register a Configurator for Fields Override
     */
    public function registerConfigurator(string $objectType, AbstractConfigurator $configurator): self
    {
        $this->configurators[] = array(
            "objectType" => $objectType,
            "configurator" => $configurator,
        );

        return $this;
    }

    /**
     * Execute All Registered Configurators for Fields Overrides
     */
    protected function executeConfigurators(): void
    {
        foreach ($this->configurators as $sequence) {
            $objectType = $sequence['objectType'] ?? null;
            $configurator = $sequence['configurator'] ?? null;
            if (($configurator instanceof AbstractConfigurator) && is_string($objectType)) {
                $configurator->overrideFieldsCollection($objectType, $this->getCollection());
            }
        }
    }
}
