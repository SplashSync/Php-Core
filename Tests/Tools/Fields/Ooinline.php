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

namespace Splash\Tests\Tools\Fields;

use Splash\Models\Helpers\InlineHelper;

/**
 * Inline Field : Inline Simple Json List
 *
 * @example     ["tag1", "tag2", "tag3"]
 */
class Ooinline extends Oovarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'Inline';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        if (!empty($data) && !is_string($data)) {
            return "Field  Data is not a String.";
        }

        return true;
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        $fakeData = array();
        $max = (int) rand(2, 6);
        for ($i = 0; $i < $max; $i++) {
            $fakeData[] = Oovarchar::fake($settings);
        }

        return InlineHelper::fromArray($fakeData);
    }
}
