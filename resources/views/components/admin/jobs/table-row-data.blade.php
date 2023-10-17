<tr wire:key="job-{{ $job->id }}">
    <td class="text-nowrap">
        {{ Carbon\Carbon::parse($job->posted_date)->format('M d, Y') }}
    </td>
    <td>
        <div class="d-flex max-width-450">
            <div class="max-width-400">
                @can('edit records')
                    <a href="{{ route('admin.jobs.show', $job->slug) }}">{{ $job->name }}</a>
                @else
                    <a wire:click="showRecruitingJobDetails('{{ $job->id }}')" class="text-tertiary">
                        {{ $job->name }}
                    </a>
                @endif
                @if ($job->status === \App\Models\Job::STATUS_PENDING)
                    <span class="badge bg-info text-dark ms-2">Pending</span>
                @endif
            </div>
            <div>
                @if ($job->status === \App\Models\Job::STATUS_ARCHIVED)
                    <span class="badge bg-gray-medium text-dark">
                        Archived
                        @if ($job->exp_date)
                            {{ $job->pretty_exp_date }}
                        @endif
                    </span>
                @endif
            </div>
            @can('recruiting')
                <div class="ms-auto">
                    @if($job->jobApplications->count() > 0)
                        <a href="{{ route('admin.recruiting.applications', 'job='. $job->id) }}" class="badge bg-warning-neon text-dark text-decoration-none">{{ $job->jobApplications->count() }}</a>
                    @endif
                </div>
            @endcan
        </div>
    </td>
    <td>{{ $job->type }}</td>
    <td>
        @if($job->remote)
            <p class="m-0 small text-quaternary">Remote / Virtual</p>
        @endif
        @if($job->locations->count() > 0)
            <x-entities.show.relationship-list :records="$job->locations" class="small" />
        @endif
    </td>
    <td>
        <div class="max-width-300">
            {{ $job->category->name }}
        </div>
    </td>
    <td>
        @can('edit records')
            <a href="{{ $job->getOwnerShowLink() }}">{{ $job->owner->name }}</a>
        @else
            {{ $job->owner->name }}
        @endif
    </td>
    <td>
        @if($job->salary_min)
            ${{ number_format($job->salary_min, 0) }}{{$job->salary_max ? ' - ' . number_format($job->salary_max, 0) : null }}
        @endif
        @if($job->hourly)
            ${{ number_format($job->hourly, 0) }} / hour
        @endif
    </td>
    <td class="text-right">
        @can('edit records')
            @if($job->isLocked && $job->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$job" />
            @else
                <a href="{{ route('admin.jobs.edit', $job->id) }}">edit</a>
            @endif
        @endif
    </td>
</tr>
