<?php

namespace Splash\Core\Interfaces\Scopes;

/**
 * Interface for Server Scope Functional Testing
 */
interface TestableInterface
{
    /**
     * Get List of Phpunit Classes to Execute
     *
     * @return class-string[]
     */
    public function getTestClasses(): array;
}