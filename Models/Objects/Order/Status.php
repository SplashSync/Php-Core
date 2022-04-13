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

namespace Splash\Models\Objects\Order;

/**
 * Customers Orders Status List
 */
class Status
{
    /**
     * @var string Order is Canceled
     */
    const CANCELED = "OrderCanceled";

    /**
     * @var string Order is Draft
     */
    const DRAFT = "OrderDraft";

    /**
     * @var string Order is Validated but Payment Due
     */
    const PAYMENT_DUE = "OrderPaymentDue";

    /**
     * @var string Order is Validated & Being Processed
     */
    const PROCESSING = "OrderProcessing";

    /**
     * @var string Order is Validated & is Prepared
     */
    const PROCESSED = "OrderProcessed";

    /**
     * @var string Order is Validated but Out Of Stock
     */
    const OUT_OF_STOCK = "OrderOutOfStock";

    /**
     * @var string Order is In Transit
     */
    const IN_TRANSIT = "OrderInTransit";

    /**
     * @var string Order is Available for PickUp
     */
    const PICKUP = "OrderPickupAvailable";

    /**
     * @var string Order is to be Shipped
     */
    const TO_SHIP = "OrderToShip";

    /**
     * @var string Order is Delivered to Customer
     */
    const DELIVERED = "OrderDelivered";

    /**
     * @var string Order is Returned to Seller
     */
    const RETURNED = "OrderReturned";

    /**
     * @var string Order has Delivery Problems
     */
    const PROBLEM = "OrderProblem";

    /**
     * @var string Order Status is Unknown
     */
    const UNKNOWN = "";

    /**
     * Get a List of All Possible Order Status Codes
     *
     * @return array
     */
    public static function getAll()
    {
        return array(
            self::CANCELED,
            self::DRAFT,
            self::PAYMENT_DUE,
            self::PROCESSING,
            self::PROCESSED,
            self::OUT_OF_STOCK,
            self::IN_TRANSIT,
            self::PICKUP,
            self::TO_SHIP,
            self::DELIVERED,
            self::RETURNED,
            self::PROBLEM,
        );
    }

    /**
     * Get a List of All Possible Order Status Codes
     *
     * @param bool $showExpended
     *
     * @return array
     */
    public static function getAllChoices(bool $showExpended = false): array
    {
        $choices = array(
            self::CANCELED => "Canceled",
            self::DRAFT => "Draft",
            self::PAYMENT_DUE => "Payment Due",
            self::PROCESSING => "Processing",
            self::OUT_OF_STOCK => "Out of Stock",
            self::IN_TRANSIT => "In Transit",
            self::PICKUP => "Pickup Available",
            self::DELIVERED => "Delivered",
            self::RETURNED => "Returned",
            self::PROBLEM => "In Error",
        );

        $expended = array(
            self::PROCESSED => "Processed",
            self::TO_SHIP => "To Ship",
        );

        return $showExpended ? array_merge($choices, $expended) : $choices;
    }

    /**
     * Get a List of All Expended Order Status Codes
     *
     * @return array
     */
    public static function getExpended()
    {
        return array(
            self::PROCESSED,
            self::TO_SHIP,
        );
    }

    /**
     * Get a List of Validated Order Status Codes
     *
     * @return array
     */
    public static function getValidated()
    {
        return array(
            self::PAYMENT_DUE,
            self::PROCESSING,
            self::PROCESSED,
            self::OUT_OF_STOCK,
            self::IN_TRANSIT,
            self::PICKUP,
            self::TO_SHIP,
            self::DELIVERED,
            self::RETURNED,
            self::PROBLEM,
        );
    }

    /**
     * Check if Order Status Code is Validated
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isValidated(string $status)
    {
        return in_array($status, self::getValidated(), true);
    }

    /**
     * Get a List of Canceled Order Status Codes
     *
     * @return array
     */
    public static function getCanceled()
    {
        return array(
            self::CANCELED,
        );
    }

    /**
     * Check if Order Status Code is Canceled
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isCanceled(string $status)
    {
        return in_array($status, self::getCanceled(), true);
    }

    /**
     * Get a List of Draft Order Status Codes
     *
     * @return array
     */
    public static function getDraft()
    {
        return array(
            self::DRAFT,
        );
    }

    /**
     * Check if Order Status Code is Draft
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isDraft(string $status)
    {
        return in_array($status, self::getDraft(), true);
    }

    /**
     * Get a List of Processing Order Status Codes
     *
     * @return array
     */
    public static function getProcessing()
    {
        return array(
            self::PROCESSING,
            self::PROCESSED,
            self::OUT_OF_STOCK,
        );
    }

    /**
     * Check if Order Status Code is Processing
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isProcessing(string $status)
    {
        return in_array($status, self::getProcessing(), true);
    }

    /**
     * Get a List of Shipped Order Status Codes
     *
     * @return array
     */
    public static function getShipped()
    {
        return array(
            self::IN_TRANSIT,
            self::PICKUP,
            self::TO_SHIP,
            self::PROBLEM,
        );
    }

    /**
     * Check if Order Status Code is Shipped
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isShipped(string $status)
    {
        return in_array($status, self::getShipped(), true);
    }

    /**
     * Get a List of Delivered Order Status Codes
     *
     * @return array
     */
    public static function getDelivered()
    {
        return array(
            self::DELIVERED,
        );
    }

    /**
     * Check if Order Status Code is Delivered
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isDelivered(string $status)
    {
        return in_array($status, self::getDelivered(), true);
    }

    /**
     * Get a List of Returned Order Status Codes
     *
     * @return array
     */
    public static function getReturned()
    {
        return array(
            self::RETURNED,
        );
    }

    /**
     * Check if Order Status Code is Returned
     *
     * @param string $status Order Status Code
     *
     * @return bool
     */
    public static function isReturned(string $status)
    {
        return in_array($status, self::getReturned(), true);
    }
}
