<?php
/**
 * @var object $entity
 * @var string $name
 * @var array $options
 */

use App\Helpers\Entity\FieldsMapping;
?>
@if(isset($options['relationField']))
    @if(in_array($options['relation'], [FieldsMapping::RELATION_ONE_ONE, FieldsMapping::RELATION_ONE_ONE_MORPHABLE]))
        @if($relation = $entity->{$name})
            <ul>
                <li>
                    @if(is_array($options['relationField']))
                        <ul>
                            @foreach($options['relationField'] as $field)
                                <li>{{ ucfirst($field) }}: {{ $relation->{$field} }}
                                    @if(isset($options['pivotColumns']))
                                        <ul>
                                            @foreach($options['pivotColumns'] as $pivotColumn)
                                                <li>{{ $relation->pivot->{$pivotColumn} }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        {{ $relation->{$options['relationField']} }}
                        @if(isset($options['pivotColumns']))
                            <ul>
                                @foreach($options['pivotColumns'] as $pivotColumn)
                                    <li>{{ $relation->pivot->{$pivotColumn} }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </li>
            </ul>
        @endif
    @else
        @if($entity->{$name} && $entity->{$name}->count() > 0)
            <ul>
                @foreach($entity->{$name} as $relation)
                    <li>
                        @if(is_array($options['relationField']))
                            <ul>
                                @foreach($options['relationField'] as $field)
                                    <li>{{ ucfirst($field) }}: {{ $relation->{$field} }}
                                        @if(isset($options['pivotColumns']))
                                            <ul>
                                                @foreach($options['pivotColumns'] as $pivotColumn)
                                                    <li>{{ $relation->pivot->{$pivotColumn} }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            {{ $relation->{$options['relationField']} }}
                            @if(isset($options['pivotColumns']))
                                <ul>
                                    @foreach($options['pivotColumns'] as $pivotColumn)
                                        <li>{{ $relation->pivot->{$pivotColumn} }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p><span class="badge bg-secondary">No data</span></p>
        @endif
    @endif
@else
    <div class="alert alert-danger">"relationField" is missing in field mapping configuration!</div>
@endif
