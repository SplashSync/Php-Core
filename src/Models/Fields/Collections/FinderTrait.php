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

namespace Splash\Core\Models\Fields\Collections;

use Splash\Core\Dictionary\Fields\SplFieldProps;
use Splash\Core\Helpers\FieldTemplatesHelper;
use Splash\Core\Models\Fields\AbstractField;

/**
 * A set of Fields Collection Searching Methods
 */
trait FinderTrait
{
    /**
     * @inheritDoc
     */
    public function find(string $fieldId): ?AbstractField
    {
        return $this->filterIdentifiers(array($fieldId))->unique();
    }

    /**
     * @inheritDoc
     */
    public function findOneByPrimary(): ?AbstractField
    {
        return $this->filterPrimary()->unique();
    }

    /**
     * @inheritDoc
     */
    public function findOneByMetadata(string $itemType, string $itemProp): ?AbstractField
    {
        return $this->filterMetadata($itemType, $itemProp)->unique();
    }

    /**
     * @inheritDoc
     */
    public function findOneByTag(string $tag): ?AbstractField
    {
        return $this->filterTag($tag)->unique();
    }

    /**
     * @inheritDoc
     */
    public function findOneByTemplate(string $templateCodeOrClass): ?AbstractField
    {
        //====================================================================//
        // Load Field Template from Code or Class
        if (!$variantList = FieldTemplatesHelper::resolve($templateCodeOrClass)) {
            return null;
        }
        //====================================================================//
        // Load Field Template Metadata
        $itemType = $variantList->getConfiguration()[SplFieldProps::MICRODATA_URL] ?? null;
        $itemProp = $variantList->getConfiguration()[SplFieldProps::MICRODATA_PROP] ?? null;
        if (empty($itemType) || !is_string($itemType) || empty($itemProp) || !is_string($itemProp)) {
            return null;
        }

        //====================================================================//
        // Filter Fields by Metadata
        return $this->findOneByMetadata($itemType, $itemProp);
    }
}
