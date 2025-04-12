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
 * Dictionary for Splash Field Preferred Synchronisation Mode
 */
class SplSyncMode
{
    /**
     * Both => Field Prefers Bi directional synchronisation
     */
    const BOTH = "both";

    /**
     * Read => Field Prefers being only read & exported to other servers
     */
    const READ = "export";

    /**
     * Write => Field Prefers being only written & imported from other servers
     */
    const WRITE = "import";

    /**
     * None => Ok! Field Prefers being left unchanged, without any synchronization
     */
    const NONE = "none";

    /**
     * Get All Available Synchronisation Modes
     *
     * @return string[]
     */
    static public function getAll(): array
    {
        return array(
            self::BOTH,
            self::READ,
            self::WRITE,
            self::NONE,
        );
    }

    /**
     * Check if Synchronisation Mode is Valid
     */
    static public function isValid(string $syncMode): bool
    {
        return in_array($syncMode, self::getAll());
    }

}
