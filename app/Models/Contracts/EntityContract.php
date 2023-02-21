<?php
/**
 * TODO: in the future need to describe here methods specific only for "Entities" type of model, like getMergeMapping() now
 */
namespace App\Models\Contracts;

interface EntityContract
{
    /**
     * Required to use Entity Merge functionality
     * Should return array with specific structure
     *
     * To have possibility merge model attributes:
     * 'attribute_name' => [
     *     'type' => FieldsMapping::TYPE_CONSTANT, // required
     *     'label' => 'Custom label', // optional, attribute name used by default
     * ],
     *
     * AND/OR
     *
     * To have possibility merge model relations:
     * 'relation_name' => [
     *     'type' => FieldsMapping::TYPE_RELATION, // required
     *     'relationField' => 'field_name', // required, define which field's value to show from related model
     *     'label' => 'Custom label', // optional, relation name used by default
     * ],
     *
     * @return array
     */
    public static function getFieldsMapping();
}
