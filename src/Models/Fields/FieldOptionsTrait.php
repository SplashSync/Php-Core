<?php

namespace Splash\Core\Models\Fields;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\StringConverter;

/**
 * Splash Object Field Options
 */
trait FieldOptionsTrait
{
    /**
     * Field Possible Values
     *
     * @var array<string, array{key: string, value:string}>
     */
    private array $choices = array();

    /**
     * Field Options
     *
     * @var array<string, scalar>
     */
    private array $options = array();

    /**
     * @inheritdoc
     */
    public function setMultiLang(?string $isoCode, bool $isDefault): self
    {
        //====================================================================//
        // Safety Checks ==> Verify Language ISO Code
        if (!self::isValidIsoCode((string) $isoCode)) {
            return $this;
        }
        //====================================================================//
        // Safety Checks ==> Verify Field Type is Allowed
        $fieldType = $this->getListFieldType() ?? $this->getType();
        if (!self::isValidMultiLangType($fieldType)) {
            Splash::log()->err("ErrFieldsWrongLang");
            Splash::log()->err("Received: ".$fieldType);

            return $this;
        }
        //====================================================================//
        // Default Language ==> Only Setup Language Option
        $this->addOption("language", (string) $isoCode);
        //====================================================================//
        // Other Language ==> Complete Field Setup
        if (!$isDefault) {
            $this->setIdentifier($this->getIdentifier()."_".$isoCode);
            if (!empty($this->getItemType()) && !empty($this->getItemProp())) {
                $this->setMicroData(
                    $this->getItemType()."/".$isoCode,
                    $this->getItemProp()
                );
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setChoices(array $choices): static
    {
        foreach ($choices as $value => $description) {
            $this->addChoice(
                (string) $value,
                (string) ($description ?? ucfirst($value) ?: ucfirst($value))
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addChoice(string $value, string $description): static
    {
        $this->choices[] = array(
            "key" => $value,
            "value" => StringConverter::toUtf8($description)
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addOption(string $key, $value = true): static
    {
        //====================================================================//
        // Safety Checks ==> Verify Key
        if (empty($key)) {
            Splash::log()->err("Field Option Type Cannot be Empty");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->options[$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return array<string, string>
     */
    public function getChoices(): array
    {
        $choices = array();

        foreach ($this->choices as $choice) {
            $choices[$choice["key"]] = $choice["value"];
        }

        return $choices;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->options;
    }


}