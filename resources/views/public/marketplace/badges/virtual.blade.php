@if($listing->virtual === 1)
    <i class="fa-sharp fa-solid fa-laptop-medical fa-fw text-primary me-1"></i>
    <span>Telehealth</span>
@elseif($listing->virtual === 0)
    <i class="fa-sharp fa-solid fa-laptop-medical fa-fw opacity-50 text-decoration-line-through me-1"></i>
    <span class="opacity-50 text-decoration-line-through">Telehealth</span>
@endif
