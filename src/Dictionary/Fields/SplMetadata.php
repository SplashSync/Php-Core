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
 * Dictionary for Splash Metadata Fields Definitions
 *
 * Metadata Fields have Spécial behavior on Splash:
 * - receive a splash spécific data
 * - request for specific behavior (Technical)
 */
class SplMetadata
{
    /**
     * Splash Specific Schemas Item Type.
     */
    const TYPE = "http://splashync.com/schemas";

    /**
     * This field should receive Splash Object ID (UUID).
     */
    const OBJECT_ID = "ObjectId";

    /**
     * This Field should receive Splash Object Created Date.
     */
    const DATE_CREATED = "DateCreated";

    /**
     * This Field should receive Splash Object Source Server ID (INT).
     */
    const ORIGIN_NODE_ID = "SourceNodeId";

    /**
     * This Field should receive Splash Object Source Server Name.
     */
    const ORIGIN_NODE_NAME = "SourceNodeName";

    /**
     * This Field is used to Decide if Remote Objects should be present on remote node.
     *
     * As soon as set but empty, remote object should be deleted
     *
     * @since 2026 !
     */
    const PRESENCE = "SourceNodeName";
}
