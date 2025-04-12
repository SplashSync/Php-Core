<?php

namespace Splash\Core\Interfaces\Fields;

use Splash\Core\Interfaces\Fields\Field\FieldCoreInterface;
use Splash\Core\Interfaces\Fields\Field\FieldMetadataInterface;
use Splash\Core\Interfaces\Fields\Field\FieldOptionsInterface;
use Splash\Core\Interfaces\Fields\Field\FieldSynchronizationInterface;
use Splash\Core\Interfaces\Fields\Field\FieldListingInterface;
use Splash\Core\Interfaces\Fields\Field\FieldSyncModeInterface;
use Splash\Core\Interfaces\Fields\Field\FieldTestInterface;

/**
 * Interface for Splash Object Field Definition
 *
 * @template FIELD of array{
 *          type: string,
 *          id: string,
 *          name: string,
 *          desc: string,
 *          group: null|string,
 *          required: null|bool|string,
 *          read: null|bool|string,
 *          write: null|bool|string,
 *          index: null|bool|string,
 *          inlist: null|bool|string,
 *          hlist: null|bool|string,
 *          log: null|bool|string,
 *          notest: null|bool|string,
 *          primary: null|bool|string,
 *          syncmode: string,
 *          itemprop: null|string,
 *          itemtype: null|string,
 *          tag: null|string,
 *          choices: null|array{key: string, value: scalar},
 *          asso: null|string[],
 *          options: array<string, scalar>
 *  }
 */
interface FieldInterface extends
    FieldCoreInterface,
    FieldSynchronizationInterface,
    FieldSyncModeInterface,
    FieldListingInterface,
    FieldMetadataInterface,
    FieldOptionsInterface,
    FieldTestInterface
{
    /**
     * Field Constructor
     *
     * @param string $type Field Type Code
     * @param string|null $identifier Field Identifier
     */
    public function __construct(string $type, ?string $identifier = null);
}