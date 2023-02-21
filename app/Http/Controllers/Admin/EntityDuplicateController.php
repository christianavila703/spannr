<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Entity\FieldsMapping;
use App\Helpers\Entity\MergeHelper;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\Request;

class EntityDuplicateController extends Controller
{
    /**
     * @var string[]
     */
    protected $entityTypes = [
        'event' => Event::class,
        'fundraising_round' => FundraisingRound::class,
        'investor' => Investor::class,
        'job' => Job::class,
        'organization' => Organization::class,
        'person' => Person::class,
    ];

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.entity-duplicate.index', [
            'entityTypes' => $this->entityTypes
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntityListJson(Request $request)
    {
        try {
            $entityType = $request->input('entity_type');
            $entityModel = $this->getEntityClassByType($entityType);
        } catch (\Throwable $t) {
            return response()->json(['status' => 'error'], 404);
        }

        $data = $entityModel::all()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getEntityForm(Request $request)
    {
        try {
            $entityType = $request->input('entity_type');
            $entityModel = $this->getEntityClassByType($entityType);
        } catch (\Throwable $t) {
            return redirect()->back()->withException($t);
        }

        $masterId = $request->input('master_id');
        $secondaryId = $request->input('secondary_id');

        $masterEntity = $entityModel::findOrFail($masterId);
        $secondaryEntity = $entityModel::findOrFail($secondaryId);
        $mapping = $entityModel::getFieldsMapping();

        $view = view('admin.entity-duplicate.entity-form', compact('masterEntity', 'secondaryEntity', 'mapping'))->render();

        return response()->json([
            'status' => 'success',
            'data' => $view
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function merge(Request $request)
    {
        try {
            $entityType = $request->input('entity_type');
            $entityModel = $this->getEntityClassByType($entityType);
        } catch (\Throwable $t) {
            return redirect()->back()->withException($t);
        }

        $masterId = $request->input('master_id');
        $secondaryId = $request->input('secondary_id');
        $attributes = $request->input('attributes');
        $relations = $request->input('relations');

        $masterEntity = $entityModel::findOrFail($masterId);
        $secondaryEntity = $entityModel::findOrFail($secondaryId);

        $masterEntity = $this->mergeEntities($masterEntity, $secondaryEntity, $attributes, $relations);
        $successMessage = "Merged succesfully! \"{$masterEntity->name}\" was updated. \"{$secondaryEntity->name}\" was removed.";

        // delete secondaryEntity first to avoid unique fields duplicate error
        $secondaryEntity->delete();
        $masterEntity->update();

        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $masterEntity
     * @param \Illuminate\Database\Eloquent\Model $secondaryEntity
     * @param array $attributes
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function mergeEntities($masterEntity, $secondaryEntity, $attributes, $relations)
    {
        $mapping = $masterEntity::getFieldsMapping();

        foreach ($attributes as $attribute => $source) {
            //TODO: maybe need to rename image, which will be merged to master?
            if ($mapping[$attribute]['type'] === FieldsMapping::TYPE_IMAGE && method_exists($masterEntity, 'removeImageFile')) {
                if ($source === MergeHelper::SOURCE_MASTER) {
                    $secondaryEntity->removeImageFile();
                } elseif ($source === MergeHelper::SOURCE_SECONDARY) {
                    $masterEntity->removeImageFile();
                }
            }

            if ($source === MergeHelper::SOURCE_SECONDARY) {
                $masterEntity->{$attribute} = $secondaryEntity->{$attribute};
            }
        }

        foreach ($relations as $relationName => $source) {
            //add secondary entity's relations only if source marked as not 'master'
            if ($source === MergeHelper::SOURCE_SECONDARY || $source === MergeHelper::SOURCE_MERGE) {
                $pivotColumns = isset($mapping[$relationName]['pivotColumns']) ? $mapping[$relationName]['pivotColumns'] : [];

                switch ($mapping[$relationName]['relation']) {
                    case FieldsMapping::RELATION_ONE_ONE:
                        $masterEntity = $this->mergeRelationOneToOne($masterEntity, $secondaryEntity, $relationName, $source);
                        break;
                    case FieldsMapping::RELATION_ONE_ONE_MORPHABLE:
                        $masterEntity = $this->mergeRelationOneToOneMorphable($masterEntity, $secondaryEntity, $relationName, $source);
                        break;
                    case FieldsMapping::RELATION_ONE_N:
                    case FieldsMapping::RELATION_ONE_N_MORPHABLE:
                        $masterEntity = $this->mergeRelationOneToMany($masterEntity, $secondaryEntity, $relationName, $source);
                        break;
                    case FieldsMapping::RELATION_N_N:
                    case FieldsMapping::RELATION_N_N_MORPHABLE:
                        $masterEntity = $this->mergeRelationManyToMany($masterEntity, $secondaryEntity, $relationName, $source, $pivotColumns);
                        break;
                }
            }
        }

        return $masterEntity;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $masterEntity
     * @param \Illuminate\Database\Eloquent\Model $secondaryEntity
     * @param string $relationName
     * @param string $source
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function mergeRelationOneToOne($masterEntity, $secondaryEntity, $relationName, $source)
    {
        $field = $masterEntity->{$relationName}()->getForeignKeyName();
        $masterEntity->{$field} = $secondaryEntity->{$field};

        return $masterEntity;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $masterEntity
     * @param \Illuminate\Database\Eloquent\Model $secondaryEntity
     * @param string $relationName
     * @param string $source
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function mergeRelationOneToOneMorphable($masterEntity, $secondaryEntity, $relationName, $source)
    {
        $fieldId = $masterEntity->{$relationName}()->getForeignKeyName();
        $fieldType = $masterEntity->{$relationName}()->getMorphType();

        $masterEntity->{$fieldId} = $secondaryEntity->{$fieldId};
        $masterEntity->{$fieldType} = $secondaryEntity->{$fieldType};

        return $masterEntity;
    }


    /**
     * @param \Illuminate\Database\Eloquent\Model $masterEntity
     * @param \Illuminate\Database\Eloquent\Model $secondaryEntity
     * @param string $relationName
     * @param string $source
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function mergeRelationOneToMany($masterEntity, $secondaryEntity, $relationName, $source)
    {
        if ($source === MergeHelper::SOURCE_SECONDARY) {
            // clear master entity relation's data to save only secondary entity relations
            $masterEntity->{$relationName}()->delete();
        }

        $masterEntity->{$relationName}()->saveMany($secondaryEntity->{$relationName});

        return $masterEntity;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $masterEntity
     * @param \Illuminate\Database\Eloquent\Model $secondaryEntity
     * @param string $relationName
     * @param string $source
     * @param array $pivotColumns
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function mergeRelationManyToMany($masterEntity, $secondaryEntity, $relationName, $source, $pivotColumns)
    {
        if ($source === MergeHelper::SOURCE_SECONDARY) {
            // clear master entity relation's data to save only secondary entity relations
            $masterEntity->{$relationName}()->detach();
        }

        $relationData = [];
        // add relation's data from secondary entity to master entity
        foreach ($secondaryEntity->{$relationName} as $relation) {

            if (!empty($pivotColumns)) {
                foreach ($pivotColumns as $pivotColumn) {
                    $relationData[$relation->getKey()][$pivotColumn] = $relation->pivot->{$pivotColumn};
                }
            } else {
                $relationData[] = $relation->getKey();
            }
        }

        $masterEntity->{$relationName}()->syncWithoutDetaching($relationData);
        $masterEntity->touch();

        return $masterEntity;
    }

    private function getEntityClassByType($type)
    {
        if (isset($this->entityTypes[$type]) === false) {
            throw new \Exception('Wrong entity type!');
        }

        return $this->entityTypes[$type];
    }
}
