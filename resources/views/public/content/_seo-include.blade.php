@section('title', (string) $content->name)
@push('seo')
<meta name="description" content="{{ $content->meta_description }}" />
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
<link rel="canonical" href="{{ $content->url }}" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $content->name }}" />
<meta property="og:description" content="{{ $content->meta_description }}" />
<meta property="og:url" content="{{ $content->url }}" />
<meta property="og:site_name" content="Spannr" />
<meta property="article:published_time" content="{{ $content->meta_published_time }}" />
<meta property="og:image" content="{{ $content->shareable_image_url }}" />
<meta property="og:image:width" content="1616" />
<meta property="og:image:height" content="824" />
<meta property="og:image:type" content="image/jpeg" />
<meta name="author" content="Spannr" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:creator" content="@spannr_" />
<meta name="twitter:site" content="@spannr_" />
<meta name="twitter:label1" content="Written by" />
<meta name="twitter:data1" content="Spannr" />
<meta name="twitter:label2" content="Est. reading time" />
<meta name="twitter:data2" content="{{ $content->reading_time_minutes }} minutes" />
{!! $graph !!}
@endpush
