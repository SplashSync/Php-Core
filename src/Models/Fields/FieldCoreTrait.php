<?php

namespace Splash\Core\Models\Fields;

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
}