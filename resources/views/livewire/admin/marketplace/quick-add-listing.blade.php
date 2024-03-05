<div>
    <div>
        @if($success)
            <div class="my-4 text-success">{{ $success }}</div>
        @endif
    </div>
    <div>
        @if($error)
            <div class="my-4 text-danger">{{ $error }}</div>
        @endif
    </div>
     @include('public.marketplace.forms.create-listing-form')
</div>
