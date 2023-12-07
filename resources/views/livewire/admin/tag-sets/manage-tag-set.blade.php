<div>
    <h1 class="text-primary">{{ $tagSet->name }}</h1>
    <div class="lead mb-3">
        {{ $tagSet->tags->count() }} tags
    </div>
    <div class="max-width-700">
        <ul class="list-group">
            @foreach ($options as $firstLevelTag)
                <li wire:key="tag-{{ $firstLevelTag['id'] }}" class="list-group-item">
                    <div class="py-2 h6">
                        {{ $firstLevelTag['name'] }}
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($firstLevelTag['children'] as $secondLevelTag)
                            <li wire:key="tag-{{ $secondLevelTag['id'] }}" class="list-group-item">
                                @if(in_array($secondLevelTag['name'], $current))
                                    <button wire:click="removeOption('{{ $secondLevelTag['id'] }}')" aria-label="Remove Age Group" class="btn btn-sm text-success">
                                        <i class="fa-sharp fa-solid fa-check fa-lg"></i>
                                    </button>
                                    <span class="text-success">{{ $secondLevelTag['name'] }}</span>
                                @else
                                    <button wire:click="addOption('{{ $secondLevelTag['id'] }}')" aria-label="Add Age Group" class="btn btn-sm text-secondary me-1">
                                        <i class="fa-sharp fa-solid fa-plus fa-lg"></i>
                                    </button>
                                    <span class="text-secondary">{{ $secondLevelTag['name'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
</div>
