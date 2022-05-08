<div class="row">
    <div class="col-12 col-md-6">
        <p class="lead mb-2 text-muted">
            {{ $investor->summary }}
        </p>

        <table class="table table-borderless width-auto">
            <tr>
                <td colspan="2">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Details</small>
                </td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-buildings"></i></td>
                <td>{{ $investor->type }}</td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-money-check-dollar-pen"></i></td>
                <td>{total raised or something}</td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Links</small>
                </td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-earth-americas"></i></td>
                <td><a href="{{ $investor->website }}">{{ $investor->website }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-facebook"></i></td>
                <td><a href="{{ $investor->facebook }}">{{ $investor->facebook }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-twitter"></i></td>
                <td><a href="{{ $investor->twitter }}">{{ $investor->twitter }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-linkedin"></i></td>
                <td><a href="{{ $investor->linkedin }}">{{ $investor->linkedin }}</a></td>
            </tr>
            <tr>
                <td><i class="fa-brands fa-instagram"></i></td>
                <td><a href="{{ $investor->instagram }}">{{ $investor->instagram }}</a></td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Organizations</small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @foreach ($investor->organizations as $organization)
                        <a href="{{ route('organizations.show', $organization->slug) }}">{{ $organization->name }}</a> @if (!$loop->last) <br> @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Fundraising Rounds</small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @foreach ($investor->fundraisingRounds as $fundraisingRound)
                        <a href="">{{ $fundraisingRound->name }}</a> - ${{ number_format($fundraisingRound->amount_raised, 0) }} @if (!$loop->last) <br> @endif
                    @endforeach
                </td>
            </tr>
        </table>

        <div>
            <small class="h6 mb-0 d-block fw-bold text-uppercase">Description</small>
            {!! nl2br(e($investor->description)) !!}
        </div>
    </div>
</div>
