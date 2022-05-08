<div class="row">
    <div class="col-12 col-md-6">
        <table class="table table-borderless width-auto">
            <tr>
                <td colspan="2">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Details</small>
                </td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-coins"></i></td>
                <td>{{ $fundraisingRound->round }}</td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-calendar-days"></i></td>
                <td>{{ $fundraisingRound->date }}</td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-money-check-dollar-pen"></i></td>
                <td>${{ number_format($fundraisingRound->amount_raised, 0) }}</td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-sack-dollar"></i></td>
                <td>${{ number_format($fundraisingRound->valuation, 0) }}</td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-typewriter"></i></td>
                <td>
                    <a href="{{ $fundraisingRound->announcement_url }}" target="_blank" rel="noopener noreferrer">Announcement<i class="fa fa-arrow-up-right-from-square ms-1"></i></a>
                </td>
            </tr>
            <tr>
                <td><i class="fa-duotone fa-building"></i></td>
                <td><a href="{{ route('organizations.show', $fundraisingRound->organization->slug) }}">{{ $fundraisingRound->organization->name }}</a></td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3">
                    <small class="h6 mb-0 d-block fw-bold text-uppercase">Investors</small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @foreach ($fundraisingRound->investors as $investor)
                        <a href="{{ route('investors.show', $investor->slug) }}">{{ $investor->name }}</a> @if (!$loop->last) <br> @endif
                    @endforeach
                </td>
            </tr>
        </table>

        <div>
            <small class="h6 mb-0 d-block fw-bold text-uppercase">Description</small>
            {!! nl2br(e($fundraisingRound->description)) !!}
        </div>
    </div>
</div>
