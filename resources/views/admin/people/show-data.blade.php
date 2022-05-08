<div class="row">
    <div class="col-12 col-md-6">
        <p class="lead mb-2 text-muted">
            {{ $person->byline }}
        </p>

        <table class="table table-borderless width-auto">
            <tr>
                <td><i class="fa-duotone fa-briefcase"></i></td>
                <td>{{ $person->job_type }}</td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Links</small>
                </td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-earth-americas"></i></td>
                <td><a href="{{ $person->website }}">{{ $person->website }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-facebook"></i></td>
                <td>{!! $person->facebookLink !!}</td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-twitter"></i></td>
                <td>{!! $person->twitterLink !!}</td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-linkedin"></i></td>
                <td>{!! $person->linkedinLink !!}</td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-instagram"></i></td>
                <td>{!! $person->instagramLink !!}</td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-google"></i></td>
                <td>{!! $person->googleScholarLink !!}</td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Organizations</small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @foreach ($person->organizations as $organization)
                        <a href="{{ route('organizations.show', $organization->slug) }}">
                            {{ $organization->pivot->role }} at {{ $organization->name }}
                        </a> @if (!$loop->last) <br> @endif
                    @endforeach
                </td>
            </tr>
        </table>

        <div>
            <small class="h6 mb-0 d-block fw-bold text-uppercase">Bio</small>
            {!! nl2br(e($person->bio)) !!}
        </div>
    </div>
</div>
