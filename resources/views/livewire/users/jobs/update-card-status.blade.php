<div>
    <div wire:poll.visible>
        @if($setupIntent->status === \App\Models\SetupIntent::STATUS_SUCCEEDED)
            <div>
                <div class="fs-3 text-success mb-1">
                    <i class="fa-sharp fa-solid fa-circle-check me-1"></i>
                    <span>Your card was updated!</span>
                </div>
                <p class="fs-5">Thanks and have a great day!</p>
                <div>
                    <a href="{{ route('users.jobs.index') }}" class="btn btn-dark">Back to your Jobs</a>
                </div>
            </div>
        @else
            <div>
                <div class="fs-3 text-tertiary mb-1">
                    <i class="fa-sharp fa-solid fa-hourglass-clock me-1"></i>
                    <span>We're processing your card...</span>
                </div>
                <div class="max-width-500 text-body-secondary mx-auto">You can wait for confirmation or close this window. We'll notify you if there's an issue. Thanks and have a great day!</div>
            </div>
        @endif
    </div>
</div>
