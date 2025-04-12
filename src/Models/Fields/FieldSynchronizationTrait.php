<?php

namespace Splash\Core\Models\Fields;

/**
 * Splash Object Field Features Flags Definition
 */
trait FieldSynchronizationTrait
{
    /**
     * Field Required Flag
     */
    private bool $required = false;

    /**
     * Field Read Flag
     */
    private bool $read = true;

    /**
     * Field Write Flag
     */
    private bool $write = true;

    /**
     * Field Indexed Flag
     */
    private bool $index = false;

    /**
     * Field Primary Flag
     */
    private bool $primary = false;

    /**
     * Field Log / Archive Flag
     */
    private bool $log = false;

    /**
     * @inheritDoc
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @inheritDoc
     */
    public function setRead(bool $read): static
    {
        $this->read = $read;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRead(): bool
    {
        return $this->read;
    }

    /**
     * @inheritDoc
     */
    public function setWrite(bool $write): static
    {
        $this->write = $write;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isWrite(): bool
    {
        return $this->write;
    }

    /**
     * @inheritDoc
     */
    public function setIndex(bool $index): static
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isIndex(): bool
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function setPrimary(bool $primary): static
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * @inheritDoc
     */
    public function setLogged(bool $logged): static
    {
        $this->log = $logged;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isLogged(): bool
    {
        return $this->log;
    }
}