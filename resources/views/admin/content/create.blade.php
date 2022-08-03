@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.content.index') }}" backLabel="Content">
        <x-slot:title>Add Content from WordPress</x-slot:title>
        <form method="POST" action="{{ route('admin.content.store') }}" enctype="multipart/form-data" id="wordpress-form" data-wordpress="{{ $wordpressPages }}">
            @csrf
            <div class="row">

                <div class="col-12 col-md-6 col-lg-5">
                    <label for="wordpress-select" class="form-label fw-bold">Select WordPress Page</label>
                    <select name="wordpress" id="wordpress-select"></select>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>

    </x-entities.create>

@endsection

@push('end')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        $( document ).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const peopleData = JSON.parse(document.getElementById('wordpress-form').dataset.wordpress);
            const peopleSelect = new Choices(document.getElementById('wordpress-select'));
            setChoices();

            function setChoices() {
                peopleSelect.setChoices(peopleData);
            }

            function clearChoices() {
                $('#role').val('');
                peopleSelect.setChoiceByValue(['']);
            }

            $('#add-person').on('click', function(event) {
                event.preventDefault();

                let url = $(this).data('url');
                let person = $('#people-select').val();
                let investor = $('#investor').val();
                let role = $('#role').val();

                $.post(url, {
                    person: person,
                    investor: investor,
                    role: role,
                }, function (response) {
                    $('.js-ajax-response .message').text(response.message);
                    if (response.status === 'success') {
                        $('.js-ajax-response').removeClass('alert-danger').addClass('alert-success').show();
                        clearChoices();
                    } else {
                        $('.js-ajax-response').removeClass('alert-success').addClass('alert-danger').show();
                    }
                });

                $('.close-ajax-response').on('click', function() {
                    $(this).parent().hide();
                });
            });
        });
    </script>
@endpush
