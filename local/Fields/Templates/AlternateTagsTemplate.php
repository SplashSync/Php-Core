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

use Splash\Core\Dictionary\Fields\SplFieldConstraints;
use Splash\Core\Dictionary\Fields\SplFieldProps as Props;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\InlineHelper;
use Splash\Core\Models\Fields\AbstractField;
use Splash\Core\Models\Fields\AbstractFieldTemplate;

/**
 * Dummy Field Template declaring alternate auto-mapping tags
 */
class AlternateTagsTemplate extends AbstractFieldTemplate
{
    /**
     * Current Microdata of the Field
     */
    public const ITEM_TYPE = "http://schema.org/Organization";

    /**
     * Current Microdata of the Field
     */
    public const ITEM_PROP = "ID";

    /**
     * Legacy Microdata the Field still connects with
     */
    public const ALT_ITEM_TYPE = "http://schema.org/Invoice";

    /**
     * Legacy Microdata the Field still connects with
     */
    public const ALT_ITEM_PROP = "customer";

    /**
     * @inheritDoc
     */
    public function getName(?string $isoLang = null): string
    {
        return "Customer Link";
    }

    /**
     * @inheritDoc
     */
    public function getShortDescription(?string $isoLang = null): string
    {
        return "Customer link, also connecting with the legacy invoice customer tag";
    }

    /**
     * @inheritDoc
     */
    protected function getCoreConfiguration(string $isoLang): array
    {
        return array(
            Props::TYPE => SplFields::VARCHAR,
            Props::MICRODATA_URL => self::ITEM_TYPE,
            Props::MICRODATA_PROP => self::ITEM_PROP,
            Props::OPTIONS => array(
                SplFieldConstraints::ALT_TAGS => InlineHelper::fromArray(array(
                    AbstractField::toTag(self::ALT_ITEM_TYPE, self::ALT_ITEM_PROP),
                )),
            ),
        );
    }
}
