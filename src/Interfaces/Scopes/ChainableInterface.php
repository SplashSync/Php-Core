<?php

namespace Splash\Core\Interfaces\Scopes;

/**
 * Interface for Chaining Scopes
 */
interface ChainableInterface
{
    /**
     * Get Child Scope Codes
     *
     * @return string[]
     */
    public function getChildCodes(): array;

    /**
     * Get Children Scopes
     *
     * @return array<string, ScopeInterface>
     */
    public function getChildren(string $objectType = null): array;

    /**
     * Get All Children Scopes (Recursive)
     *
     * @return array<string, ScopeInterface>
     */
    public function getAllChildren(string $objectType = null): array;
}