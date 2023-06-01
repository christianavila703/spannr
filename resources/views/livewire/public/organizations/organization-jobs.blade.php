<div>
    <div id="organization-jobs" class="organization-related-items-container mt-3">
        @foreach ($records as $record)
             <div wire:key="job-{{ $record->id }}" class="organization-related-item mb-3 pb-3">
                <h2 class="h6">{{ $record->name }}</h2>
                 <x-content.jobs.job-summary :job="$record" />
             </div>
         @endforeach
    </div>
</div>
