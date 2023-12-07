<div>
    @if($importResult->type === \App\Models\ImportResult::TYPE_LISTING_IMPORT)
        <div class="mb-3">
            <button wire:click="processImport" class="btn btn-sm btn-dark">Process Import</button>
        </div>
    @endif
    <div class="row">
        <div class="col-12 col-xl-6">
            <p class="h3 text-tertiary mb-4">Imported Data</p>
            <div class="my-4">
                <div>
                    <strong class="min-width-100 d-inline-block">Status</strong>
                    <span wire:poll.visible class="{{ $importResult->status_color }}">{{ $importResult->status }}</span>
                </div>
                <div>
                    <strong class="min-width-100 d-inline-block">Imported</strong>
                    <span>{{ $importResult->created_at }} by {{ $importResult->user->name }}</span>
                </div>
                <div>
                    <strong class="min-width-100 d-inline-block">Updated</strong>
                    <span>{{ $importResult->updated_at }}</span>
                </div>
            </div>
            <div class="mt-4">
                <p class="fs-5 mb-0 fw-bold text-uppercase">Data</p>
                <pre>{{ print_r($importResult->data, true) }}</pre>
            </div>
            <div>
                @foreach($errorFields as $label => $errorField)
                    @if($importResult->{$errorField})
                        <div class="mt-5">
                            <p class="fs-5 mb-0 fw-bold text-uppercase">{{ $label }} Errors</p>
                            <pre>{{ print_r($importResult->{$errorField}, true) }}</pre>
                        </div>
                        <div class="mt-3">
                            <button wire:click="clearErrors('{{ $errorField }}')" class="btn btn-sm btn-secondary me-2">Clear {{ $label }} Errors</button>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <p class="h3 text-tertiary">Listing Data</p>
            @if($importResult->entity)
                <div>
                    <a href="{{ $importResult->entity->show_url }}" class="text-uppercase">View Listing</a>
                    <table class="table">
                        @foreach($tableFields as $field => $fieldType)
                            <tr>
                                <th>{{ $field }}</th>
                                <td>
                                    @if($fieldType === 'array')
                                        @if($importResult->entity->{$field})
                                            <div>
                                                @foreach($importResult->entity->{$field} as $value)
                                                    <div>{{ $value }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @elseif($fieldType === 'url')
                                        <a href="{{ $importResult->entity->{$field} }}">
                                            <span class="truncate-300">{{ $importResult->entity->{$field} }}</span>
                                        </a>
                                    @elseif($fieldType === 'tel')
                                        <a href="tel:{{ $importResult->entity->{$field} }}">{{ $importResult->entity->{$field} }}</a>
                                    @elseif($fieldType === 'relationship')
                                        {{ $importResult->entity->{$field}->name ?? '' }}
                                    @else
                                        {{ $importResult->entity->{$field} }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="mt-5">
                        <p class="h5 text-tertiary">Locations</p>
                        @foreach($importResult->entity->addresses as $address)
                            <div>
                                <livewire:public.marketplace.manage-listing.edit-address :address="$address" />
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5">
                        <p class="h5 text-tertiary">Tags</p>
                        <div>
                            <livewire:public.marketplace.manage-listing.medical-treatments :listing="$importResult->entity" />
                        </div>
                    </div>
                </div>
            @else
                <div class="text-danger fs-5">No entity</div>
            @endif
        </div>
    </div>
</div>
