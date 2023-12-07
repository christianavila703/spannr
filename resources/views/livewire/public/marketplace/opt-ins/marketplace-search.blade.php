<div class="marketplace-listings-container">
    <div class="d-flex flex-column flex-md-row flex-wrap align-items-center mt-5">
        <div class="marketplace-location-search-container d-flex justify-content-center align-items-center me-md-4 mb-3 flex-grow-1 max-width-300">
            <input wire:model.lazy="search" type="text" class="form-control me-2" placeholder="Search by keyword" aria-label="Search listings" id="marketplace-search-box">
            <span>
                <i class="fa-sharp fa-solid fa-circle-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Search by name, location, treatment, etc."></i>
            </span>
            <div id="marketplace-search-preset-options" class="marketplace-location-results d-none short shadow-sm">
                <ul class="list-group">
                    @foreach($presetOptions as $option)
                        <li class="list-group-item list-group-item-action p-0">
                            <button wire:click="setSearchValue('{{ $option }}')" class="btn checkbox-choice-button small text-start">{{ $option }}</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-center me-md-4 mb-3 marketplace-location-search-container flex-grow-1">
            <input wire:model.lazy="locationSearch" type="text" class="form-control me-2 flex-grow-1" placeholder="Search location" aria-label="Search location">
            <div class="input-group me-2" style="max-width: 125px">
                <input wire:model.lazy="locationDistance" type="text" class="form-control" placeholder="100" aria-label="Distance in miles">
                <span class="input-group-text">miles</span>
            </div>
            <span>
                <i class="fa-sharp fa-solid fa-circle-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Search by street, city, zip code, state, etc."></i>
            </span>
            @if($locationResults)
                <div class="marketplace-location-results long shadow">
                    <ul class="list-group">
                        @forelse($locationResults as $resultId => $result)
                            <li class="list-group-item list-group-item-action p-0">
                                <button wire:click="selectLocation('{{ $resultId }}')" class="btn checkbox-choice-button small text-start">{{ $result['properties']['formatted'] }}</button>
                            </li>
                        @empty
                            <li class="list-group-item">
                                We weren't able to find a match. Try your search again or contact help@spannr.com
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <div class="mt-3">
        <button wire:click="submit" class="btn btn-lg btn-quintary">Search Marketplace</button>
    </div>
</div>
