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

namespace Splash\Models\Objects\Invoice;

/**
 * Customers Invoice Status List
 */
class Status
{
    /**
     * @var string Invoice is Canceled
     */
    const CANCELED = "PaymentCanceled";

    /**
     * @var string Invoice is Draft
     */
    const DRAFT = "PaymentDraft";

    /**
     * @var string Invoice is Validated but Payment Due
     */
    const PAYMENT_DUE = "PaymentDue";

    /**
     * @var string Invoice is Validated & Payment Completed
     */
    const COMPLETE = "PaymentComplete";

    /**
     * @var string Invoice is Validated but Payment Refused
     */
    const DECLINED = "PaymentDeclined";

    /**
     * @var string Invoice Status is Unknown
     */
    const UNKNOWN = "";

    /**
     * Get a List of All Possible Invoice Status Codes
     *
     * @return array
     */
    public static function getAll(): array
    {
        return array(
            self::CANCELED,
            self::DRAFT,
            self::PAYMENT_DUE,
            self::COMPLETE,
            self::DECLINED,
        );
    }

    /**
     * Get a List of All Possible Invoice Status Codes
     *
     * @return array
     */
    public static function getAllChoices(): array
    {
        return array(
            self::CANCELED => "Canceled",
            self::DRAFT => "Draft",
            self::PAYMENT_DUE => "Payment Due",
            self::COMPLETE => "Completed",
            self::DECLINED => "Declined",
        );
    }

    /**
     * Get a List of Validated Invoice Status Codes
     *
     * @return array
     */
    public static function getValidated(): array
    {
        return array(
            self::PAYMENT_DUE,
            self::COMPLETE,
            self::DECLINED,
        );
    }

    /**
     * Check if Invoice Status Code is Validated
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isValidated(string $status): bool
    {
        return in_array($status, self::getValidated(), true);
    }

    /**
     * Get a List of Canceled Invoice Status Codes
     *
     * @return array
     */
    public static function getCanceled(): array
    {
        return array(
            self::CANCELED,
        );
    }

    /**
     * Check if Invoice Status Code is Canceled
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isCanceled(string $status): bool
    {
        return in_array($status, self::getCanceled(), true);
    }

    /**
     * Get a List of Draft Invoice Status Codes
     *
     * @return array
     */
    public static function getDraft(): array
    {
        return array(
            self::DRAFT,
        );
    }

    /**
     * Check if Invoice Status Code is Draft
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isDraft(string $status): bool
    {
        return in_array($status, self::getDraft(), true);
    }

    /**
     * Get a List of Paid Invoice Status Codes
     *
     * @return array
     */
    public static function getPaid(): array
    {
        return array(
            self::COMPLETE,
        );
    }

    /**
     * Check if Invoice Status Code is Paid
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isPaid(string $status): bool
    {
        return in_array($status, self::getPaid(), true);
    }
}
