@props([
    'record',
])
<span class="text-danger me-3"><i class="fa-sharp fa-solid fa-lock me-1"></i>{{ $record->lockedByName }}<span class="text-muted d-block">({{ $record->unlockedIn }} min)</span></span>

