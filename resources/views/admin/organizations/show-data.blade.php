<div class="row">
    <div class="col-12 col-md-6">
        <p class="lead mb-2 text-muted">
            {{ $organization->summary }}
        </p>

        <table class="table table-borderless width-auto">
            <tr>
                <td colspan="2">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Details</small>
                </td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-buildings"></i></td>
                <td>{{ $organization->type }}</td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-calendar-days"></i></td>
                <td>{{ $organization->founded_date }}</td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Links</small>
                </td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-earth-americas"></i></td>
                <td><a href="{{ $organization->website }}">{{ $organization->website }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-facebook"></i></td>
                <td><a href="{{ $organization->facebook }}">{{ $organization->facebook }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-twitter"></i></td>
                <td><a href="{{ $organization->twitter }}">{{ $organization->twitter }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-linkedin"></i></td>
                <td><a href="{{ $organization->linkedin }}">{{ $organization->linkedin }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-instagram"></i></td>
                <td><a href="{{ $organization->instagram }}">{{ $organization->instagram }}</a></td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Investors</small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @foreach ($organization->investors as $investor)
                        <a href="{{ route('investors.show', $investor->slug) }}">{{ $investor->name }}</a> @if (!$loop->last) <br> @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">People</small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @foreach ($organization->people as $person)
                        <a href="{{ route('people.show', $person->slug) }}">
                            {{ $person->pivot->role }} - {{ $person->name }}
                        </a> @if (!$loop->last) <br> @endif
                    @endforeach
                </td>
            </tr>
        </table>

        <div>
            <small class="h6 mb-0 d-block fw-bold text-uppercase">Description</small>
            {!! nl2br(e($organization->description)) !!}
        </div>
    </div>
</div>
