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

    public function __construct(?string $fullName = null)
    {
        if ($fullName) {
            $this->decodeFullName($fullName);
        }
    }

    public function decodeFullName(string $fullName): void
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

        // Detect Last Name
        if (false !== ($pos = strpos($fullName, ', '))) {
            $this->lastName = substr($fullName, 0, $pos);
            $fullName = substr($fullName, $pos + 2);
        }

        // Set remaining part as first name
        $this->firstName = $fullName;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName(): string
    {
        $parts = array_filter([$this->lastName, $this->firstName]);
        $name = implode(', ', $parts);
        if (!empty($this->companyName)) {
            $name .= ' - ' . $this->companyName;
        }
        return $name;
    }

    public static function registerCompanyNameField(FieldsFactory $factory): void
    {
        $factory->create('companyName')->description('Company Name');
    }

    public static function registerFirstNameField(FieldsFactory $factory): void
    {
        $factory->create('firstName')->description('First Name');
    }

    public static function registerLastNameField(FieldsFactory $factory): void
    {
        $factory->create('lastName')->description('Last Name');
    }
}

