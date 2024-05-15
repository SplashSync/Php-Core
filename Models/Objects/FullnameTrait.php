<?php

namespace Splash\Models\Objects;

use Splash\Client\Splash;

trait FullnameTrait
{
    use SimpleFieldsTrait;

    /**
     * @var null|string
     */
    private ?string $companyName = null;

    /**
     * @var null|string
     */
    private ?string $firstName = null;

    /**
     * @var null|string
     */
    private ?string $lastName = null;

    /**
     * @var null|string
     */
    private ?string $fullName = null;

    //====================================================================//
    // Class Trait Functions
    //====================================================================//

    /**
     * Build Full Name
     *
     * @param array|null $object
     * @return void
     */
    public function buildFullName(?array $object): void
    {
        if (empty($object)) {
            return;
        }

        $company = $object['name'] ?? "";
        $firstname = $object['firstname'] ?? "";
        $lastname = $object['lastname'] ?? "";

        if (empty($company)) {
            Splash::log()->war("Full Name Generation Error: Company Name is Empty");
            return;
        }

        if (!empty($firstname) && !empty($lastname)) {
            $this->fullName = $lastname.", ".$firstname." - ".$company;
        } elseif (!empty($firstname)) {
            $this->fullName = $firstname." - ".$company;
        } elseif (!empty($lastname)) {
            $this->fullName = $lastname." - ".$company;
        } else {
            $this->fullName = $company;
        }

        $this->setCompanyName($company);
        $this->setFirstName($firstname);
        $this->setLastName($lastname);
    }

    public function decodeFullName(): ?array
    {
        $fullName = $this->getFullName();
        //====================================================================//
        // Safety Checks
        if (empty($fullName)) {
            return null;
        }

        //====================================================================//
        // Init
        $result = array('name' => "",'firstname' => "" , 'lastname' => "");

        //====================================================================//
        // Detect Single Company Name
        if ((!str_contains($fullName, ' - ')) && (!str_contains($fullName, ', '))) {
            $result['name'] = $fullName;
            return $result;
        }

        //====================================================================//
        // Detect Company Name
        if (false !== ($pos = strpos($fullName, ' - '))) {
            $result['name'] = substr($fullName, $pos + 3);
            $fullName = substr($fullName, 0, $pos);
        }

        //====================================================================//
        // Detect Last Name
        if (false !== ($pos = strpos($fullName, ', '))) {
            $result['lastname'] = substr($fullName, 0, $pos);
            $fullName = substr($fullName, $pos + 2);
        }

        if ($fullName === $this->getLastName()) {
            $result['lastname'] = $fullName;
            $result['firstname'] = $this->getFirstName();
        } else {
            $result['firstname'] = $fullName;
        }
        return $result;
    }

    public function updateFullName(): void
    {
        $company = $this->getCompanyName() ?? "";
        $firstname = $this->getFirstName() ?? "";
        $lastname = $this->getLastName() ?? "";

        $this->buildFullName(["firstname" => $firstname, "lastname" => $lastname, "name" => $company]);
    }

    //====================================================================//
    // Getters and Setters
    //====================================================================//

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

}