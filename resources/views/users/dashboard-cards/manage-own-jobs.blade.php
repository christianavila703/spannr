<div class="card card-body">
    <h2 class="h3">Job Listings</h2>

    <div class="fs-5">
        <table class="table w-auto table-no-bottom-border mb-0">
            <tr>
                <th>Featured Jobs:</th>
                <td>{{ $user->job_subscriptions_count }}</td>
            </tr>
            <tr>
                <th>Total Listed:</th>
                <td>{{ $user->jobs_count }}</td>
            </tr>
        </table>
    </div>

    <div class="mt-3">
        <a href="{{ route('users.jobs.index') }}" class="btn btn-primary me-2">View Your Jobs</a>
        <a href="{{ route('public.content.jobs.submit') }}" class="btn btn-tertiary">List New Jobs</a>
    </div>
</div>
