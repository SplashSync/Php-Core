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

use Splash\Client\Splash;

/**
 * Splash Generic Payment Methods List
 *
 * @see https://schema.org/PaymentMethod
 */
class PaymentMethods
{
    //====================================================================//
    // CODES DEFINITIONS
    //====================================================================//

    /**
     * Payment by bank transfer in advance
     * This is equivalent to payment by wire transfer.
     *
     * @see http://purl.org/goodrelations/v1#ByBankTransferInAdvance
     */
    const BANK = "ByBankTransferInAdvance";

    /**
     * Payment by sending a check in advance.
     *
     * @see http://purl.org/goodrelations/v1#CheckInAdvance
     */
    const CHECK = "CheckInAdvance";

    /**
     * Payment by cash upon delivery or pickup.
     *
     * @see http://purl.org/goodrelations/v1#Cash
     */
    const CASH = "Cash";

    /**
     * Collect on delivery / Cash on delivery
     *
     * @see http://purl.org/goodrelations/v1#COD
     */
    const COD = "COD";

    /**
     * Payment by bank transfer after delivery.
     * Inform the buying party about the due amount and their bank account details,
     * and expect payment shortly after delivery.
     *
     * @see http://purl.org/goodrelations/v1#ByInvoice
     */
    const INVOICE = "ByInvoice";

    /**
     * Payment by direct debit
     *
     * @see http://purl.org/goodrelations/v1#DirectDebit
     */
    const DIRECT_DEBIT = "DirectDebit";

    /**
     * Credit Card, alias of Payment by direct debit
     */
    const CREDIT_CARD = "CreditCard";

    /**
     * VISA Card
     *
     * @see http://purl.org/goodrelations/v1#VISA
     */
    const VISA = "VISA";

    /**
     * Payment by credit or debit cards issued by the American Express network.
     *
     * @see http://purl.org/goodrelations/v1#AmericanExpress
     */
    const AMEX = "AmericanExpress";

    /**
     * PayPal CheckOut
     *
     * @see http://purl.org/goodrelations/v1#PayPal
     */
    const PAYPAL = "PayPal";

    /**
     * Google CheckOut
     *
     * @see http://purl.org/goodrelations/v1#GoogleCheckout
     */
    const GOOGLE = "GoogleCheckout";

    /**
     * Amazon Payments
     */
    const AMAZON = "AmazonPay";

    /**
     * Apple Pay
     */
    const APPLE = "ApplePay";

    //====================================================================//
    // CODES CHOICES
    //====================================================================//

    /**
     * List of All Known Payment Modes Code for Choices
     *
     * @return string[]
     */
    public static function getChoices(): array
    {
        static $choices;

        if (!isset($choices)) {
            Splash::translator()->load("payments");
            $choices = array();
            foreach (self::all() as $code) {
                $choices[$code] = sprintf(
                    "[%s] %s",
                    $code,
                    Splash::trans($code),
                );
            }
        }

        return $choices;
    }

    //====================================================================//
    // CODES GROUPS
    //====================================================================//

    /**
     * List of All Known Payment Modes Code
     *
     * @return string[]
     */
    public static function all(): array
    {
        return array_merge(
            self::inAdvance(),
            self::card(),
            self::online(),
            self::after(),
        );
    }

    /**
     * List of Live Payment Modes Code
     * Payment is done on Order Validation
     *
     * @return string[]
     */
    public static function live(): array
    {
        return array(
            self::DIRECT_DEBIT,
            self::CREDIT_CARD,
            self::VISA,
            self::AMEX,
            self::PAYPAL,
            self::GOOGLE,
            self::AMAZON,
            self::APPLE,
        );
    }

    /**
     * List of In Advance Payment Modes Code
     * Payment is done before Order Validation
     *
     * @return string[]
     */
    public static function inAdvance(): array
    {
        return array(
            self::BANK,
            self::CHECK,
            self::CASH,
        );
    }

    /**
     * List of Payment Modes Code that are Paid After Delivery
     * Payment is done after Order Validation
     *
     * @return string[]
     */
    public static function after(): array
    {
        return array(
            self::COD,
            self::INVOICE,
        );
    }

    /**
     * List of Credit Cards Payment Modes Code
     *
     * @return string[]
     */
    public static function card(): array
    {
        return array(
            self::DIRECT_DEBIT,
            self::CREDIT_CARD,
            self::VISA,
            self::AMEX,
        );
    }

    /**
     * List of Online Only Payment Modes Code
     *
     * @return string[]
     */
    public static function online(): array
    {
        return array(
            self::PAYPAL,
            self::GOOGLE,
            self::AMAZON,
            self::APPLE,
        );
    }

    //====================================================================//
    // CODES CHECKS
    //====================================================================//

    /**
     * Check if Paid Before Order Validation Payment Code
     */
    public static function isInAdvance(string $code): bool
    {
        return in_array($code, self::inAdvance(), true);
    }

    /**
     * Check if Paid After Delivery Payment Code
     */
    public static function isAfter(string $code): bool
    {
        return in_array($code, self::after(), true);
    }

    /**
     * Check if Credit Card Payment Code
     */
    public static function isCard(string $code): bool
    {
        return in_array($code, self::card(), true);
    }

    /**
     * Check if Online Only Payment Code
     */
    public static function isOnline(string $code): bool
    {
        return in_array($code, self::online(), true);
    }
}
