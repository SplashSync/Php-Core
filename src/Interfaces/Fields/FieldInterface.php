<?php

namespace Splash\Core\Interfaces\Fields;

/**
 * Interface for Splash Object Field Definition
 */
interface FieldInterface extends FieldCoreInterface
{
    /**
     * Field Constructor
     *
     * @param string $type Field Type Code
     * @param string|null $identifier Field Identifier
     */
    public function __construct(string $type, ?string $identifier = null);
}