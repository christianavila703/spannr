<div class="mt-5">
    @if (!\Illuminate\Support\Facades\Route::is('admin.longevity-index.all'))
        <a href="{{ route('admin.longevity-index.all') }}" class="btn btn-secondary">View past snapshots</a>
    @endif
    <a href="{{ route('admin.longevity-index.update') }}" class="btn btn-tertiary">Update Index</a>
    <a href="{{ route('admin.longevity-index.stocks.create') }}" class="btn btn-info">Add Stock to Index</a>
</div>
