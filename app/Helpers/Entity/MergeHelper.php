<?php

namespace App\Helpers\Entity;

class MergeHelper
{
    const SOURCE_MASTER    = 'master';
    const SOURCE_SECONDARY = 'secondary';
    const SOURCE_MERGE     = 'merge';

    /**
     * @param string $type
     * @return string
     */
    public static function getViewByFieldType($type)
    {
        switch ($type) {
            case FieldsMapping::TYPE_DATE:
            case FieldsMapping::TYPE_ENUM:
            case FieldsMapping::TYPE_INTEGER:
            case FieldsMapping::TYPE_FLOAT:
            case FieldsMapping::TYPE_STRING:
                $view = 'text';
                break;
            case FieldsMapping::TYPE_TEXT:
            case FieldsMapping::TYPE_TEXT_EDITOR:
                $view = 'textarea';
                break;
            case FieldsMapping::TYPE_IMAGE:
                $view = 'image';
                break;
            case FieldsMapping::TYPE_BOOLEAN:
                $view = 'boolean';
                break;
            case FieldsMapping::TYPE_RELATION:
                $view = 'relation';
                break;
            default:
                $view = 'error';
                break;
        }

        return $view;
    }

    public static function makeLabelFromFieldName($name)
    {
        return ucwords(str_replace('_', ' ', $name));
    }

}
