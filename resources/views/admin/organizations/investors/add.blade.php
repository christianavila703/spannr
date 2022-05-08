<div class="modal fade" id="addInvestorForm" tabindex="-1" aria-labelledby="addInvestorFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('organizations.add-investors') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addInvestorFormLabel">Add Investors to {{ $organization->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="min-height: 400px">
                    <label for="investors" class="visually-hidden">Select Investors</label>
                    <select multiple name="investors[]" id="investors" class="js-choice-investor">
                        @foreach ($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                        @endforeach
                    </select>
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
    const investorChoice = new Choices('.js-choice-investor');
</script>
