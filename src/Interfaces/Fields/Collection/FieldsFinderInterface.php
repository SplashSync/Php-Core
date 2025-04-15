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

namespace Splash\Core\Interfaces\Fields\Collection;

use Splash\Core\Models\Fields\AbstractField;

/**
 * Interface for Single Field Searching Methods
 */
interface FieldsFinderInterface
{
    /**
     * Find Field by Identifier
     *
     * @phpstan-impure
     */
    public function find(string $fieldId): ?AbstractField;

    /**
     * Find Field that is Marked as Primary Field
     *
     * @phpstan-impure
     */
    public function findOneByPrimary(): ?AbstractField;

    /**
     * Find Field by Specified Metadata
     *
     * @phpstan-impure
     */
    public function findOneByMetadata(string $itemType, string $itemProp): ?AbstractField;

    /**
     * Find Field by Specified Tag
     *
     * @phpstan-impure
     */
    public function findOneByTag(string $tag): ?AbstractField;
}
