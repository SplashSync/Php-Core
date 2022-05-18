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

namespace Splash\Models\Objects\Quotation;

/**
 * Customers Quotations Status List
 */
class Status
{
    /**
     * @var string Quotation is Canceled
     */
    const CANCELED = "QuoteCanceled";

    /**
     * @var string Quotation is Draft
     */
    const DRAFT = "QuoteDraft";

    /**
     * @var string Quotation is Validated by Admin
     */
    const VALIDATED = "QuoteValidated";

    /**
     * @var string Quotation is Validated and Send to Customer
     */
    const SEND = "QuoteSend";

    /**
     * @var string Quotation is Validated and Approuved by Customer
     */
    const APPROUVED = "QuoteApprouved";

    /**
     * @var string Quotation is Validated and Orderd by Customer
     */
    const ORDERED = "QuoteOrdered";

    /**
     * @var string Quotation is Validated and Refused by Customer
     */
    const REFUSED = "QuoteRefused";

    /**
     * @var string Quotation is Validated but Answer delay has Expired
     */
    const EXPIRED = "QuoteExpired";

    /**
     * @var string Quotation Status is Unknown
     */
    const UNKNOWN = "";

    /**
     * Get a List of All Possible Quotation Status Codes
     *
     * @return array
     */
    public static function getAll()
    {
        return array(
            self::CANCELED,
            self::DRAFT,
            self::VALIDATED,
            self::SEND,
            self::APPROUVED,
            self::ORDERED,
            self::REFUSED,
            self::EXPIRED,
        );
    }

    /**
     * Get a List of All Possible Quotation Status Codes
     *
     * @return array
     */
    public static function getAllChoices(): array
    {
        return array(
            self::CANCELED => "Canceled",
            self::DRAFT => "Draft",
            self::VALIDATED => "Validated",
            self::SEND => "Send to Customer",
            self::APPROUVED => "Approuved by Customer",
            self::ORDERED => "Ordered by Customer",
            self::REFUSED => "Refuseded by Customer",
            self::EXPIRED => "Answer delay Expired",
        );
    }

    /**
     * Get a List of Validated Quotation Status Codes
     *
     * @return array
     */
    public static function getValidated()
    {
        return array(
            self::VALIDATED,
            self::SEND,
            self::APPROUVED,
            self::ORDERED,
            self::REFUSED,
            self::EXPIRED,
        );
    }

    /**
     * Check if Quotation Status Code is Validated
     *
     * @param string $status Quotation Status Code
     *
     * @return bool
     */
    public static function isValidated(string $status)
    {
        return in_array($status, self::getValidated(), true);
    }

    /**
     * Get a List of Canceled Quotation Status Codes
     *
     * @return array
     */
    public static function getCanceled()
    {
        return array(
            self::CANCELED,
            self::REFUSED,
            self::EXPIRED,
        );
    }

    /**
     * Check if Quotation Status Code is Canceled
     *
     * @param string $status Quotation Status Code
     *
     * @return bool
     */
    public static function isCanceled(string $status)
    {
        return in_array($status, self::getCanceled(), true);
    }

    /**
     * Get a List of Draft Quotation Status Codes
     *
     * @return string[]
     */
    public static function getDraft(): array
    {
        return array(
            self::DRAFT,
        );
    }

    /**
     * Check if Quotation Status Code is Draft
     *
     * @param string $status Quotation Status Code
     *
     * @return bool
     */
    public static function isDraft(string $status)
    {
        return in_array($status, self::getDraft(), true);
    }

    /**
     * Get a List of Approved Quotation Status Codes
     *
     * @return array
     */
    public static function getApprouved()
    {
        return array(
            self::APPROUVED,
            self::ORDERED,
        );
    }

    /**
     * Check if Quotation Status Code is Approved
     *
     * @param string $status Quotation Status Code
     *
     * @return bool
     */
    public static function isApprouved(string $status)
    {
        return in_array($status, self::getApprouved(), true);
    }

    /**
     * Check if Quotation Status Code is Approved
     *
     * @param string $status Quotation Status Code
     *
     * @return bool
     */
    public static function isOrdered(string $status)
    {
        return (self::ORDERED == $status);
    }

    /**
     * Get a List of Refused Quotation Status Codes
     *
     * @return array
     */
    public static function getRefused()
    {
        return array(
            self::REFUSED,
            self::EXPIRED,
        );
    }

    /**
     * Check if Quotation Status Code is Refused
     *
     * @param string $status Quotation Status Code
     *
     * @return bool
     */
    public static function isRefused(string $status)
    {
        return in_array($status, self::getRefused(), true);
    }
}
