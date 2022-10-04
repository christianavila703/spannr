<div class="mt-5">
    @if (!\Illuminate\Support\Facades\Route::is('admin.longevity-index.all'))
        <a href="{{ route('admin.longevity-index.all') }}" class="btn btn-secondary">View Past Snapshots</a>
    @endif
    @if (\Illuminate\Support\Facades\Route::is('admin.longevity-index.show'))
        <a href="{{ route('admin.longevity-index.edit', $marketSnapshot->id) }}" class="btn btn-quaternary">Edit Market Snapshot</a>
    @endif
    <a href="{{ route('admin.longevity-index.snapshots.hourly') }}" class="btn btn-tertiary confirm-button">Get Hourly Snapshot</a>
    <a href="{{ route('admin.longevity-index.snapshots.eod') }}" class="btn btn-dark confirm-button">Get EOD Snapshot</a>
    <a href="{{ route('admin.longevity-index.stocks.create') }}" class="btn btn-info">Add Stock to Index</a>
</div>
