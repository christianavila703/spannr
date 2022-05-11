<div id="global-search-container" class="p-2 position-relative">
    <label for="search" class="visually-hidden">Search</label>
    <input wire:model="query" type="text" name="search" id="search" class="form-control" placeholder="Search">

    <div class="query-container shadow @if($query !== '') open @endif">
        @if($query !== '')
            <div class="search-results">
                <span class="d-block-search-query">
                    Results matching: <strong>{{ $query }}</strong>
                </span>

                <div>
                    @forelse($searchResults['hits'] as $record)
                        <div class="my-2">
                            <i class="{{ $record['icon'] }} fa-fw me-1"></i><a href="{{ $record['spannr_url'] }}">{{ $record['name'] }}</a>
                        </div>
                    @empty
                        <div class="my-2">
                            <strong>No results match your query</strong>
                        </div>
                    @endforelse
                </div>
{{--                <div class="mt-2">--}}
{{--                    <small><span class="text-quaternary">Expand Results</span></small>--}}
{{--                </div>--}}
            </div>
        @endif
    </div>
</div>
