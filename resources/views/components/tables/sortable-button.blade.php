<span wire:click="sortBy('{{ $columnName }}')">
    <i class="fa fa-arrow-down {{ $sortColumnName === $columnName && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
    <i class="fa fa-arrow-up {{ $sortColumnName === $columnName && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
</span>
