<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center me-4">
        <label for="sort" class="d-block fw-bold me-2 flex-shrink-0">Sort</label>
        <select wire:change="sortBy($event.target.value)" id="sort" class="form-select">
            @foreach($sortOptions as $value => $label)
                <option value="{{$value}}" @if(isset($currentSortOption) && $currentSortOption === $value) selected @endif>{{$label}}</option>
            @endforeach
        </select>
    </div>
    <div class="lead">{{ $total }} results</div>
</div>
