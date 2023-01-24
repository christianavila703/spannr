<span class="table-sort-buttons" wire:click="sortBy('{{ $columnName }}')">
    <i class="fa fa-arrow-down {{ $sortColumnName === $columnName && $sortDirection === 'asc' ? 'text-success' : 'text-muted' }}"></i>
    <i class="fa fa-arrow-up {{ $sortColumnName === $columnName && $sortDirection === 'desc' ? 'text-success' : 'text-muted' }}"></i>
</span>
