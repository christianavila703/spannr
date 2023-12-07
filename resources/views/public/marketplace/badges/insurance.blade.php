@if($listing->insurance === 1)
    <i class="fa-sharp fa-solid fa-file-medical fa-fw text-primary me-1"></i>
    <span>Accepts Insurance</span>
@elseif($listing->insurance === 0)
    <i class="fa-sharp fa-solid fa-file-medical fa-fw opacity-50 text-decoration-line-through me-1"></i>
    <span class="opacity-50 text-decoration-line-through">Accepts Insurance</span>
@endif
