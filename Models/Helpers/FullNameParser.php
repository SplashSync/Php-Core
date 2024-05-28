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

class FullNameParser
{
    private ?string $companyName = null;
    private ?string $firstName = null;
    private ?string $lastName = null;

    /**
     * @param null|string $fullName
     *
     * Class Constructor
     */
    public function __construct(?string $fullName = null)
    {
        if ($fullName) {
            $this->decodeFullName($fullName);
        }
    }

    /**
     * @return null|string
     *
     * Get the Company Name
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * @param null|string $companyName
     *
     * @return $this
     *
     * Set the Company Name
     */
    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return null|string
     *
     * Get the First Name
     */
    public function getFirstName(): ?string
    {
        return $this->firstName ?: null;
    }

    /**
     * @param null|string $firstName
     *
     * @return $this
     *
     * Set the First Name
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return null|string
     *
     * Get the Last Name
     */
    public function getLastName(): ?string
    {
        return $this->lastName ?: null;
    }

    /**
     * @param null|string $lastName
     *
     * @return $this
     *
     * Set the Last Name
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return null|string
     *
     * Get the Full Name
     */
    public function getFullName(): ?string
    {
        $parts = array_filter(array($this->lastName, $this->firstName));
        $fullname = implode(', ', $parts);

        if (!empty($fullname) && str_contains($fullname, ', ')) {
            $fullname .= " - ".$this->companyName;
        } else {
            $fullname = $this->companyName;
        }

        return $fullname;
    }

    /**
     * @param FieldsFactory $factory
     *
     * @return void
     *
     * Register Full Name Fields
     */
    public static function registerFullNameFields(FieldsFactory $factory): void
    {
        self::registerCompanyNameField($factory);
        self::registerFirstNameField($factory);
        self::registerLastNameField($factory);
    }

    /**
     * @param FieldsFactory $factory
     *
     * @return FieldsFactory
     *
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
     * @param FieldsFactory $factory
     *
     * @return void
     *
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
     * @param FieldsFactory $factory
     *
     * @return void
     *
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
     * @param string $fullName
     *
     * @return void
     *
     * Decode the fullname into companyName, firstName and lastName
     */
    protected function decodeFullName(string $fullName): void
    {
        // Init
        $this->companyName = null;
        $this->firstName = null;
        $this->lastName = null;

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
}
