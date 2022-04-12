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

namespace Splash\Client;

use DateTime;
use Exception;
use Splash\Components\ExtensionsManager;
use Splash\Core\SplashCore as Splash;

/**
 * Storage for Splash Module Objects Commits Events
 */
class CommitEvent
{
    /**
     * Retry Delay in Seconds
     */
    const RETRY_DELAY = 3600;

    /**
     * Object Type Name
     *
     * @var string
     */
    private string $type;

    /**
     * Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     *
     * @var string
     */
    private string $action;

    /**
     * Local Objects Ids
     *
     * @var string[]
     */
    private array $id;

    /**
     * User Name
     *
     * @var string
     */
    private string $user;

    /**
     * Operation Comment for Logs
     *
     * @var string
     */
    private string $comment;

    /**
     * Server Ws Identifier
     *
     * @var string
     */
    private string $wsIdentifier;

    /**
     * Web Service Task Description
     *
     * @var string
     */
    private string $description;

    /**
     * @var int
     */
    private int $failCount = 0;

    /**
     * @var null|DateTime
     */
    private ?DateTime $retryAt = null;

    /**
     * Build Commit Event
     *
     * @param string                    $objectType Object Type Name
     * @param int|int[]|string|string[] $objectIds  Local Objects Ids
     * @param string                    $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string                    $user       User Name
     * @param string                    $comment    Operation Comment for Logs
     */
    public function __construct(string $objectType, $objectIds, string $action, string $user, string $comment)
    {
        //====================================================================//
        // Store Current Server Id
        $this->wsIdentifier = (string) Splash::configuration()->WsIdentifier;
        //====================================================================//
        // Store Commit Parameters
        $this->type = $objectType;
        $this->action = $action;
        $this->user = $user;
        $this->comment = $comment;
        //====================================================================//
        // Parse Objects Ids as String
        $this->id = array_map(function ($objectId) {
            return (string) $objectId;
        }, is_array($objectIds) ? $objectIds : array((string) $objectIds));
        //====================================================================//
        // Build Task Description
        $this->description = Splash::trans(
            'MsgSchRemoteCommit',
            $this->action,
            $this->type,
            (string) Splash::count($this->id)
        );
    }

    //====================================================================//
    // Basic Getters
    //====================================================================//

    /**
     * Get Committed Object Type
     *
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->type;
    }

    /**
     * Get Committed Object Action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get Committed Object IDs
     *
     * @return string[]
     */
    public function getObjectIds(): array
    {
        return $this->id;
    }

    /**
     * Get Webserver Id
     *
     * @return string
     */
    public function getWsIdentifier(): string
    {
        return $this->wsIdentifier;
    }

    /**
     * Get Commit Task Description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Convert Commit Event to Commit Parameters
     *
     * @return array
     */
    public function toArray(): array
    {
        return array(
            "type" => $this->type,          // Type of the Object
            "id" => $this->id,              // IDs of Modified object
            "action" => $this->action,      // Action Type On this Object
            "user" => $this->user,          // Operation Username for Logs
            "comment" => $this->comment,    // Operation Comment for Logs
        );
    }

    //====================================================================//
    // MD5 Builder
    //====================================================================//

    /**
     * Get Commit Event Discriminator
     *
     * @return string
     */
    public function getMd5(): string
    {
        return md5(serialize(array(
            "wsId" => $this->wsIdentifier,  // Server Identifier
            "type" => $this->type,          // Type of the Object
            "id" => $this->id,              // IDs of Modified object
            "action" => $this->action,      // Action Type On this Object
        )));
    }

    //====================================================================//
    // Locks Detection
    //====================================================================//

    /**
     * Check if Commit Event is Allowed Local Object
     *
     * @return bool
     */
    public function isAllowed(): bool
    {
        try {
            $splashObject = Splash::object($this->type);
        } catch (Exception $exception) {
            return false;
        }
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        //====================================================================//
        foreach ($this->id as $value) {
            //====================================================================//
            // Check if Object is Locked
            if ($splashObject->isLocked($value)) {
                return false;
            }
            //====================================================================//
            // Check if Object is Filtered
            if (ExtensionsManager::isFiltered($this->type, $value)) {
                return false;
            }
        }
        //====================================================================//
        // Verify Create Object is Locked ==> No Action on this Node
        if ((SPL_A_CREATE === $this->action) && $splashObject->isLocked()) {
            return false;
        }

        return true;
    }

    //====================================================================//
    // Retry Management
    //====================================================================//

    /**
     * Mark Event as Failed
     *
     * @return void
     */
    public function setFail(): void
    {
        if ($this->failCount) {
            $this->setRetryAt(new DateTime("+1 hour"));
        } else {
            $this->setRetryAt(new DateTime("+10 seconds"));
        }
        $this->failCount++;
    }

    /**
     * Check if Commit Event is Ready to Retry
     *
     * @return bool
     */
    public function isReady(): bool
    {
        $now = new DateTime();

        return empty($this->failCount) || ($this->retryAt < $now);
    }

    /**
     * Check if Commit Event is to Delete
     *
     * @return bool
     */
    public function isObsolete(): bool
    {
        return ($this->failCount > 5);
    }

    /**
     * Get next try Date
     *
     * @return null|DateTime
     */
    public function getRetryAt(): ?DateTime
    {
        return $this->retryAt;
    }

    /**
     * Set next try Date
     *
     * @param DateTime $retryAt
     *
     * @return self
     */
    public function setRetryAt(DateTime $retryAt): self
    {
        $this->retryAt = $retryAt;

        return $this;
    }
}
