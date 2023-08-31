@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.organizations.show', $organization->slug) }}" backLabel="{{ $organization->name }}">
        <x-slot:title>Manage People</x-slot:title>

        <form action="" method="post" id="people-form" data-people="{{ $people }}">
            @csrf

            <div class="alert alert-dismissible js-ajax-response" role="alert" style="display: none;">
                <span class="message"></span>
                <button type="button" class="btn-close-manual close-ajax-response" aria-label="Close"></button>
            </div>

            <div class="d-flex flex-wrap align-items-center">
                <div class="me-3" style="min-width: 300px">
                    <select class="form-select" id="people-select" name="person_id" aria-label="Select Person">
                        <option value="" selected>Select Person</option>
                    </select>
                </div>
                <div class="me-3">
                    <x-forms.input type="text" name="role" label="Role" class="" />
                    <input type="hidden" id="organization" name="organization" value="{{ $organization->id }}">
                </div>
                <div>
                    <button id="add-person" type="submit" class="btn btn-lg btn-tertiary" data-url="{{ route('admin.organizations.save-person') }}">Save</button>
                </div>
            </div>

        </form>
    </x-entities.create>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <livewire:admin.organizations.people :organization="$organization" />
            </div>
        </div>
    </div>

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

            const peopleData = JSON.parse(document.getElementById('people-form').dataset.people);
            const peopleSelect = new Choices(document.getElementById('people-select'));
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
                let organization = $('#organization').val();
                let role = $('#role').val();

                $.post(url, {
                    person: person,
                    organization: organization,
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
