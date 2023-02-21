<?php

namespace App\Helpers\Entity;

class FieldsMapping
{
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DATE = 'date';
    const TYPE_ENUM = 'enum';
    const TYPE_FLOAT = 'float';
    const TYPE_IMAGE = 'image';
    const TYPE_INTEGER = 'integer';
    const TYPE_RELATION = 'relation';
    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_TEXT_EDITOR = 'text_editor';

    const RELATION_ONE_ONE = 'one_one';
    const RELATION_ONE_N = 'one_n';
    const RELATION_N_N = 'n_n';
    const RELATION_ONE_ONE_MORPHABLE = 'one_one_morphable';
    const RELATION_ONE_N_MORPHABLE = 'one_n_morphable';
    const RELATION_N_N_MORPHABLE = 'n_n_morphable';
}
