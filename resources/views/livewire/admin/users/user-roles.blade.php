<div>
    <div class="row">
        @foreach($options as $optionId => $option)
            <div wire:poll.visible wire:key="role-{{ $optionId }}" class="col-12 col-md-6">
                @if(in_array($option, $current))
                    <button wire:click="removeOption('{{ $optionId }}', '{{ $option }}')" aria-label="Remove Treatment" class="btn checkbox-choice-button">
                        <i class="fa-sharp fa-solid fa-check fa-lg text-success fa-fw"></i>
                        <span class="badge {{ $colors[$option] ?? 'bg-secondary' }}">{{ $option }}</span>
                    </button>
                @else
                    <button wire:click="addOption('{{ $optionId }}', '{{ $option }}')" aria-label="Add Treatment" class="btn checkbox-choice-button">
                        <i class="fa-sharp fa-light fa-circle-xmark fa-lg text-danger fa-fw"></i>
                        <span class="badge bg-body-secondary text-body-emphasis">{{ $option }}</span>
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</div>
