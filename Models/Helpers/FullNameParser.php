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

namespace Splash\Models\Helpers;

use Splash\Components\FieldsFactory;

/**
 * ThirdParty Helper to Store & Update Encoded Customers Names on a Single String
 *
 * Could be:
 * - Company
 * - Firstname, Lastname - Company
 */
class FullNameParser
{
    /**
     * Temporary Company Name
     */
    private string $companyName = "";

    /**
     * Temporary First Name
     */
    private ?string $firstName = null;

    /**
     * Temporary Last Name
     */
    private ?string $lastName = null;

    /**
     * Class Constructor
     */
    public function __construct(string $fullName)
    {
        $this->decode($fullName);
    }

    /**
     * Get the Company Name
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * Set the Company Name
     */
    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get the First Name
     */
    public function getFirstName(): ?string
    {
        return $this->firstName ?: null;
    }

    /**
     * Set the First Name
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the Last Name
     */
    public function getLastName(): ?string
    {
        return $this->lastName ?: null;
    }

    /**
     * Set the Last Name
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the Full Name
     */
    public function getFullName(): string
    {
        return $this->encode();
    }

    /**
     * Register Full Name Fields
     */
    public static function registerFullNameFields(FieldsFactory $factory): void
    {
        self::registerCompanyNameField($factory);
        self::registerFirstNameField($factory);
        self::registerLastNameField($factory);
    }

    /**
     * Register Company Name Field
     */
    public static function registerCompanyNameField(FieldsFactory $factory): FieldsFactory
    {
        $factory->create(SPL_T_VARCHAR)
            ->identifier("companyName")
            ->description('Company Name')
            ->microData("http://schema.org/Organization", "legalName")
            ->isRequired()
            ->isIndexed()
        ;

        return $factory;
    }

    /**
     * Register First Name Field
     */
    public static function registerFirstNameField(FieldsFactory $factory): void
    {
        $factory->create(SPL_T_VARCHAR)
            ->identifier('firstName')
            ->description('First Name')
            ->microData("http://schema.org/Person", "familyName")
            ->association("firstName", "lastName")
            ->isLogged()
        ;
    }

    /**
     * Register Last Name Field
     */
    public static function registerLastNameField(FieldsFactory $factory): void
    {
        $factory->create(SPL_T_VARCHAR)
            ->identifier('lastName')
            ->description('Last Name')
            ->microData("http://schema.org/Person", "givenName")
            ->association("firstName", "lastName")
            ->isLogged()
        ;
    }

    /**
     * Encode the Full name from companyName, firstName and lastName
     */
    public function encode(): string
    {
        $fullName = "";

        if (!empty($this->lastName) && !empty($this->firstName)) {
            $fullName .= sprintf("%s, %s", $this->lastName, $this->firstName);
        }

        if (!empty($this->companyName)) {
            $fullName .= $fullName ? " - " : "";
            $fullName .= $this->companyName;
        }

        return $fullName;
    }

    /**
     * Decode/Explode the Full name into companyName, firstName and lastName
     */
    protected function decode(string $fullName): void
    {
        // Init
        $this->reset();

        // Detect Single Company Name
        if (!str_contains($fullName, ' - ') && !str_contains($fullName, ', ')) {
            $this->companyName = $fullName;

            return;
        }

        // Detect Company Name
        if (false !== ($pos = strpos($fullName, ' - '))) {
            $this->companyName = substr($fullName, $pos + 3);
            $fullName = substr($fullName, 0, $pos);
        }

        // Detect Last Name and First Name
        if (false !== ($pos = strpos($fullName, ', '))) {
            $this->lastName = substr($fullName, 0, $pos);
            $this->firstName = substr($fullName, $pos + 2);
        }
    }

    /**
     * Reset Storage
     */
    private function reset(): void
    {
        $this->companyName = "";
        $this->firstName = $this->lastName = null;
    }
}
