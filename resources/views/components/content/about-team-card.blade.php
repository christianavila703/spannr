<div class="col-12 col-md-6 col-lg-4 mb-5 about-team-card">
    @if ($person->show_user_page) <a href="{{ route('public.content.about.user', $person->slug) }}"> @endif
        <img src="@if ($person->getFirstMedia('teamPhoto')) {{$person->getFirstMediaUrl('teamPhoto')}} @else {{Vite::asset('resources/images/team/missing.jpg')}} @endif" alt="{{ $person->name }}, {{ $person->title }}" class="mb-4" width="640" height="360">
        <h2 class="h3 mb-2 user-name">
            <span>{{ $person->full_name }}</span>
        </h2>
        <div class="d-flex">
            <p class="title">{{ $person->title ?? 'Title' }}</p>
            @if ($person->person)
                <div class="ms-2 lead text-primary">
                    @if ($person->person->linkedin)
                        <a href="{{ $person->person->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-tertiary">
                            <i class="fa-brands fa-linkedin"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
        <p class="bio">
            @if ($person->bio)
                {{ $person->bio }}
            @else
                Sed ut perspiciatis unde omnis iste natus error sit voluptatem. Totam rem aperiam. Animi, id est laborum et dolorum fuga. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et recusandae.
            @endif
        </p>
    @if ($person->show_user_page) </a> @endif
</div>
