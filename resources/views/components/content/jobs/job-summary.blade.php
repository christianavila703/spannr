<div>
    <div class="row fw-bold">
        <div class="col-12 col-lg-6">
            <div>
                <div class="text-muted">Job Category</div>
                <div>{{ $job->category->name }}</div>
            </div>
            <div class="mt-4 mb-4 mb-lg-0">
                <div class="text-muted">Posted Date</div>
                <div>{{ $job->pretty_posted_date }}</div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div>
                <div class="text-muted">Location</div>
                <div>
                    @foreach ($job->locations as $location)
                        <div>
                            {{ $location->name }}
                        </div>
                    @endforeach
                </div>
                <div>
                    @if($job->salary_min)
                        <div class="mt-4">
                            <div class="text-muted">Salary</div>
                            <div>
                                ${{ number_format($job->salary_min, 0) }}{{$job->salary_max ? ' - ' . number_format($job->salary_max, 0) : null }}
                            </div>
                        </div>
                    @endif
                </div>
                <div>
                    @if($job->hourly)
                        <div class="mt-4">
                            <div class="text-muted">Pay</div>
                            <div>
                                ${{ number_format($job->hourly, 0) }} / hour
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
