<?php

namespace Splash\Core\Models\Scopes;

use Splash\Core\Helpers\ScopesHelper;
use Splash\Core\Interfaces\Scopes\ChainableInterface;
use Splash\Core\Interfaces\Scopes\ScopeInterface;

abstract class AbstractScope implements ScopeInterface, ChainableInterface
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public static function getCode(): string
    {
        return ScopesHelper::getCode(static::class);
    }

    /**
     * @inheritDoc
     */
    public function getMdDescription(string $isoLang = null): string
    {
        return $this->getShortDescription($isoLang);
    }

    /**
     * @inheritDoc
     */
    public function getTechnicalDescription(): string
    {
        return $this->getShortDescription();
    }

    /**
     * @inheritDoc
     */
    public function getChildCodes(): array
    {
        return array();
    }

    /**
     * @inheritDoc
     */
    public function getChildren(string $objectType = null): array
    {
        $children = array();
        foreach ($this->getChildCodes() as $childCode) {
            //====================================================================//
            // Convert Code into PHP Class
            $child = ScopesHelper::fromCode($childCode);
            if(!$child) {
                continue;
            }
            //====================================================================//
            // Filter on Impacted Object Types
            if ($objectType && !in_array($objectType, $child->getImpactedTypes(), true)) {
                continue;
            }

            $children[$child->getCode()] = $child;
        }

        return array_filter($children);
    }

    /**
     * @inheritDoc
     */
    public function getAllChildren(string $objectType = null): array
    {
        $children = $this->getChildren($objectType);
        foreach ($children as $child) {
            $children = array_replace_recursive($children, $child->getAllChildren($objectType));
        }

        return $children;
    }
}