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

namespace Splash\Core\Dictionary\Fields;

/**
 * Dictionary for Constraint to Apply on Fields Generated Data
 */
class SplFieldConstraints
{
    /**
     * Apply a Min length Constraint to Varchar Field
     */
    public const LENGTH_MIN = "minLength";

    /**
     * Apply a Min length Constraint to Varchar Field
     */
    public const LENGTH_MAX = "maxLength";

    /**
     * Apply a Lower Case Constraint to Varchar Field
     */
    public const CASE_LOWER = "isLowerCase";

    /**
     * Apply an Upper Case Constraint to Varchar Field
     */
    public const CASE_UPPER = "isUpperCase";

    /**
     * Apply an HTML Constraint to Varchar Field
     */
    public const HTML = "isHtml";

    /**
     * Apply a Sort Constraint to Varchar Field
     */
    public const SORT_ASC = "isOrdered";

    /**
     * Apply a Reverse Sort Constraint to Varchar Field
     */
    public const SORT_DESC = "isOrderedReverse";
}
