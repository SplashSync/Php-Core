<?php

namespace Splash\Core\Interfaces\Scopes;

/**
 * Interface for Server Scope Documentation
 */
interface DocumentableInterface
{
    /**
     * Get Icon Class
     */
    public function getIconClass(): string;

    /**
     * Get Markdown Description
     * -> Field Documentation / Description as Markdown Text
     */
    public function getMdDescription(string $isoLang = null): string;
}