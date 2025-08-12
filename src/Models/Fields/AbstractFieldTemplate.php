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

namespace Splash\Core\Models\Fields;

use Splash\Core\Dictionary\Fields\SplFieldProps;
use Splash\Core\Helpers\FieldTemplatesHelper;
use Splash\Core\Interfaces\Fields\FieldTemplateInterface;

abstract class AbstractFieldTemplate implements FieldTemplateInterface
{
    /**
     * Template Constructor
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public static function getCode(): string
    {
        return FieldTemplatesHelper::getCode(static::class);
    }

    /**
     * @inheritDoc
     */
    public function getConfiguration(?string $isoLang = null): array
    {
        $isoLang ??= self::DF_LANG;

        return array_replace_recursive(
            $this->getCoreConfiguration($isoLang),
            array(
                SplFieldProps::NAME => $this->getName($isoLang),
                SplFieldProps::DESC => $this->getShortDescription($isoLang),
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getMdDescription(?string $isoLang = null): string
    {
        return $this->getShortDescription($isoLang);
    }

    /**
     * @inheritDoc
     */
    public function getTechnicalDescription(): string
    {
        return $this->getShortDescription();
    }

    /**
     * Get Field Core Configuration to Apply
     *
     * @return array<string, array|scalar>
     */
    abstract protected function getCoreConfiguration(string $isoLang): array;
}
