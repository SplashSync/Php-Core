<?php

namespace Splash\Core\Models\Fields;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\ListsHelper;
use Splash\Core\Helpers\StringConverter;

/**
 * Splash Object Field Core Definition
 */
trait FieldCoreTrait
{
    /**
     * Field Identifier
     */
    private string $id;

    /**
     * Field Type
     */
    private string $type;

    /**
     * Field Name
     */
    private string $name;

    /**
     * Field Description
     */
    private ?string $desc = null;

    /**
     * Field Group
     */
    private ?string $group = null;

    /**
     * @inheritdoc
     */
    public function setIdentifier(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier(): string
    {
        return $this->id ?? "";
    }

    /**
     * @inheritdoc
     */
    private function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): static
    {
        $this->name = StringConverter::toUtf8($name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name ?? $this->getIdentifier();
    }

    /**
     * @inheritDoc
     */
    public function setDesc(string $desc): static
    {
        $this->desc = StringConverter::toUtf8($desc);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDesc(): ?string
    {
        return $this->desc;
    }

    /**
     * @inheritDoc
     */
    public function setGroup(string $group): self
    {
        $this->group = StringConverter::toUtf8($group);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    //==============================================================================
    // LIST FIELD Management
    //==============================================================================

    /**
     * Push Field Inside a List
     */
    public function setInlist(string $listName): static
    {
        //====================================================================//
        // Safety Checks ==> Verify List Name Not Empty
        if (empty($listName)) {
            return $this;
        }
        //====================================================================//
        // Update New Field Identifier
        $fieldId = ListsHelper::fieldName($this->id) ?? $this->id;
        $this->setIdentifier((string) ListsHelper::encode($listName, $fieldId));
        //====================================================================//
        // Update New Field Type
        $fieldType = ListsHelper::fieldName($this->type) ?? $this->type;
        $this->setType((string) ListsHelper::encode(SplFields::LIST, $fieldType));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isInlist(): bool
    {
        return ListsHelper::isList($this->type);
    }

    /**
     * @inheritDoc
     */
    public function getListName(): ?string
    {
        return ListsHelper::listName($this->id);
    }

    /**
     * @inheritDoc
     */
    public function getListFieldName(): ?string
    {
        return ListsHelper::fieldName($this->id);
    }

    /**
     * @inheritDoc
     */
    public function getListFieldType(): ?string
    {
        return ListsHelper::fieldName($this->type);
    }


}