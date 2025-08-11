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

namespace Splash\Local\Fields\Templates;

use Splash\Core\Dictionary\Fields\SplFieldProps as Props;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Models\Fields\AbstractFieldTemplate;

class DummyTemplate extends AbstractFieldTemplate
{
    /**
     * @inheritDoc
     */
    public function getName(?string $isoLang = null): string
    {
        return "Dummy Field";
    }

    /**
     * @inheritDoc
     */
    public function getShortDescription(?string $isoLang = null): string
    {
        return "Just a Dummy Field Template";
    }

    /**
     * @inheritDoc
     */
    protected function getCoreConfiguration(string $isoLang): array
    {
        return array(
            Props::TYPE => SplFields::TEXT,
            Props::MICRODATA_URL => uniqid("http://schema.org/"),
            Props::MICRODATA_PROP => uniqid("property"),
        );
    }
}
