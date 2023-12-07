<div data-bs-toggle="offcanvas" data-bs-target="#job-description-{{ $job->slug }}" aria-controls="job-description-{{ $job->slug }}" class="job-posting-item @if($job->monthly) featured @endif text-dark text-decoration-none">
    <div class="job-posting-item-content">
        <h2 class="job-title">
            <span>{{ $job->name }}</span>
        </h2>
        <p class="lead mb-2">{{ $job->owner->name }}</p>
        <x-content.jobs.job-summary :job="$job" />
    </div>
</div>

<div class="job-description-offcanvas offcanvas offcanvas-bottom" tabindex="-1" id="job-description-{{ $job->slug }}"
     aria-labelledby="job-description-{{ $job->slug }}-label">
    <div class="offcanvas-header border-bottom border-dark">
        <div class="offcanvas-header-container">
            <div class="h4 mt-1 mb-0 ps-lg-3" id="job-description-{{ $job->slug }}-label">{{ $job->name }}</div>
            <div class="pe-2">
                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-sharp fa-regular fa-xmark fa-lg"></i></button>
            </div>
        </div>
    </div>
    <div class="offcanvas-body">
        <div class="pb-5 px-lg-3">
            <div class="my-4">
                <a href="{{ route('public.content.jobs.apply', $job->slug) }}"
                   class="btn btn-lg btn-neon-green" target="_blank" rel="noopener noreferrer">Apply Now</a>
            </div>
            <p class="lead mb-0">{{ $job->owner->name }}</p>
            <div class="mt-4">
                <x-content.jobs.job-summary :job="$job" />
            </div>
            <div class="mt-5">
                <h2 class="h5 border-bottom border-dark">Job Description</h2>
                <div>
                    @if ($job->description)
                        <div>{!! $job->description !!}</div>
                    @else
                        <div>{!! $job->trixRender('description') !!}</div>
                    @endif
                </div>
                @if ($job->qualifications)
                    <h2 class="h5 border-bottom border-dark">Minimum Qualifications</h2>
                    <div>{!! $job->qualifications !!}</div>
                @endif
            </div>
            <div class="mt-4">
                <a href="{{ route('public.content.jobs.apply', $job->slug) }}"
                   class="btn btn-lg btn-neon-green" target="_blank" rel="noopener noreferrer">Apply Now</a>
            </div>
        </div>
    </div>
</div>
