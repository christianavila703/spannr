<div class="modal fade" id="addPeopleForm" tabindex="-1" aria-labelledby="addPeopleFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('organizations.add-people') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPeopleFormLabel">Add a Person to {{ $organization->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="min-height: 420px">
                    <div class="mb-3">
                        <label for="founders" class="visually-hidden">Founders</label>
                        <select multiple name="founders[]" id="founders" class="js-choice-founders flex-grow-1">
                            <option value="">Select Founders</option>
                            @foreach ($people as $person)
                                <option value="{{ $person->id }}">{{ $person->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="team" class="visually-hidden">Team</label>
                        <select multiple name="team[]" id="team" class="js-choice-team flex-grow-1">
                            <option value="">Select Team Members</option>
                            @foreach ($people as $person)
                                <option value="{{ $person->id }}">{{ $person->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const foundersChoice = new Choices('.js-choice-founders');
    const teamChoice = new Choices('.js-choice-team');
</script>
