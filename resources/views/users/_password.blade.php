<div>
    <h3>Change Password</h3>
    <form action="{{ route('users.store-password') }}" method="post">
        @csrf
        <div class="row mb-3">
            <div class="col-12 col-md-6">
                <label for="currentPassword">Current Password</label>
                <input type="password" name="currentPassword" id="currentPassword" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="col-12 col-md-6">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>

</div>
