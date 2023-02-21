<?php
/**
 * @var array $mapping
 * @var \Illuminate\Database\Eloquent\Model $masterEntity
 * @var \Illuminate\Database\Eloquent\Model $secondaryEntity
 */

use App\Helpers\Entity\MergeHelper;
use App\Helpers\Entity\FieldsMapping;
?>
<input type="hidden" name="master_id" value="{{ $masterEntity->id }}"/>
<input type="hidden" name="secondary_id" value="{{ $secondaryEntity->id }}"/>

@foreach($mapping as $name => $options)
    @php
        $fieldGroup = $options['type'] === FieldsMapping::TYPE_RELATION ? 'relations' : 'attributes';
    @endphp
    <div class="row">
        <div class="col-5">
            <div class="form-group">
                <label>{{ isset($options['label']) ? $options['label'] : MergeHelper::makeLabelFromFieldName($name) }}</label>
                @include('admin.entity-duplicate.fields.' . MergeHelper::getViewByFieldType($options['type']), ['entity' => $masterEntity])
            </div>
        </div>

        <div class="col-2 d-flex align-items-center justify-content-center merge-buttons-column">
            <div class="btn-group" data-toggle="buttons">
                <input type="radio" class="btn-check" id="{{ $name . MergeHelper::SOURCE_MASTER }}" name="{{ $fieldGroup . '[' . $name . ']' }}" value="{{ MergeHelper::SOURCE_MASTER }}" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="{{ $name . MergeHelper::SOURCE_MASTER }}">{{ ucfirst(MergeHelper::SOURCE_MASTER) }}</label>

                @if($options['type'] === FieldsMapping::TYPE_RELATION && !in_array($options['relation'], [FieldsMapping::RELATION_ONE_ONE, FieldsMapping::RELATION_ONE_ONE_MORPHABLE]))
                    <input type="radio" class="btn-check" id="{{ $name . MergeHelper::SOURCE_MERGE }}" name="{{ $fieldGroup . '[' . $name . ']' }}" value="{{ MergeHelper::SOURCE_MERGE }}" autocomplete="off">
                    <label class="btn btn-outline-primary" for="{{ $name . MergeHelper::SOURCE_MERGE }}">{{ ucfirst(MergeHelper::SOURCE_MERGE) }}</label>
                @endif

                <input type="radio" class="btn-check" id="{{ $name . MergeHelper::SOURCE_SECONDARY }}" name="{{ $fieldGroup . '[' . $name . ']' }}" value="{{ MergeHelper::SOURCE_SECONDARY }}" autocomplete="off">
                <label class="btn btn-outline-primary" for="{{ $name . MergeHelper::SOURCE_SECONDARY }}">{{ ucfirst(MergeHelper::SOURCE_SECONDARY) }}</label>
            </div>
        </div>

        <div class="col-5">
            <div class="form-group">
                <label>{{ isset($options['label']) ? $options['label'] : MergeHelper::makeLabelFromFieldName($name) }}</label>
                @include('admin.entity-duplicate.fields.' . MergeHelper::getViewByFieldType($options['type']), ['entity' => $secondaryEntity])
            </div>
        </div>
    </div>
    <hr>
@endforeach
