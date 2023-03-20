<div>
    <form wire:submit.prevent="save">
        <div class="mb-3">
            <span class="h6 d-block">Applicant</span>
            <span>{{ $jobApplication->person->name }}</span>
        </div>
        <div class="mb-3">
            <span class="h6 d-block">Position</span>
            <span>{{ $jobApplication->job->name }}</span>
        </div>
        <div class="mb-3">
            <label for="status" class="h6">Status</label>
            <select wire:model="jobApplication.status" name="status" id="status" class="form-select">
                @foreach($statusOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
              @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
