<div class="row">
    @foreach($options as $optionId => $option)
        <div wire:poll.visible wire:key="Option-option-{{ $optionId }}" class="col-12 col-md-6">
            @if(in_array($option, $current))
                <button wire:click="removeOption('{{ $optionId }}', '{{ $option }}')" aria-label="Remove Age Group" class="btn checkbox-choice-button text-success">
                    <i class="fa-sharp fa-solid fa-check fa-lg"></i>
                    <span>{{ $option }}</span>
                </button>
            @else
                <button wire:click="addOption('{{ $optionId }}', '{{ $option }}')" aria-label="Add Age Group" class="btn checkbox-choice-button text-secondary">
                    <i class="fa-sharp fa-solid fa-plus fa-lg"></i>
                    <span>{{ $option }}</span>
                </button>
            @endif
        </div>
    @endforeach
</div>
