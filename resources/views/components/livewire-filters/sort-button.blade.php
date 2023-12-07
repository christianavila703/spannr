<button wire:click="sortBy('{{ $field }}')" :direction="$sorts[{{ $field }}] ?? null" class="btn {{ array_key_exists($field, $sorts) ? 'text-quaternary' : 'text-dark' }} text-decoration-none fw-bold p-0">
    <span class="me-1">{{ $label }}</span>
    @if(array_key_exists($field, $sorts))
        @if ($sorts[$field] === 'asc')
            <span><i class="fa-sharp fa-solid fa-angle-up"></i></span>
        @else
            <span><i class="fa-sharp fa-solid fa-angle-down"></i></span>
        @endif
    @else
        <span><i class="fa-sharp fa-solid fa-angle-up opacity-50"></i></span>
    @endif
</button>
