<div>
    <div class="mt-3 d-flex">
        <div class="mb-4 me-4 d-flex align-items-center">
            <input wire:model.lazy="filters.search" type="text" class="form-control" placeholder="Search...">
            @if($filters['search']) <button wire:click="clearSearch" class="btn btn-sm text-danger ms-1"><i class="fa-sharp fa-solid fa-xmark"></i></button> @endif
        </div>

        <div class="me-md-4 mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-md @if($filters['role']) btn-tertiary @else btn-outline-tertiary @endif dropdown-toggle rounded-0" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter by Role
                </button>
                <ul class="dropdown-menu" style="min-width: 220px">
                    @foreach ($roleOptions as $optionId => $option)
                        <li class="px-3">
                            <div class="form-check form-check-small form-check-inline">
                                <input wire:model="filters.role" class="form-check-input" type="checkbox" value="{{ $option }}"
                                       id="filter-role-{{ $optionId }}" @if(in_array($option, $filters['role'])) checked @endif>
                                <label class="form-check-label @if(in_array($option, $filters['role'])) fw-bold @endif" for="filter-role-{{ $optionId }}">
                                    {{ $option }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="ms-auto">
            <button wire:click="resetFilters" class="btn btn-sm btn-dark">Clear Filters</button>
        </div>

    </div>
    <div class="mb-3">
        <div class="fs-5">{{ $users->count() }} users</div>
    </div>
    <table class="table align-middle">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Last Login</th>
                <th>Person</th>
            </tr>
         </thead>
         <tbody>
             @forelse ($users as $user)
                 <tr wire:key="user-{{ $user->id }}">
                     <td>
                        <a href="{{ route('admin.users.show', $user->id) }}">{{ $user->full_name }}</a>
                     </td>
                     <td>{{ $user->email }}</td>
                     <td>
                         @foreach ($user->roles as $role)
                             <span class="badge {{ $role->color }}">{{ $role->name }}</span>
                         @endforeach
                     </td>
                     <td>{{ $user->created_date }}</td>
                     <td>
                         <span class="small">{{ $user->lastLoginAt() }}</span>
                     </td>
                     <td>
                         @if ($user->person)
                             <a href="{{ route('admin.people.show', $user->person->slug) }}" class="btn btn-outline-success btn-sm">{{ $user->person->name }}</a>
                         @else
                             <button class="btn btn-tertiary btn-sm" type="button" data-bs-toggle="modal"
                                     data-bs-target="#assignPersonForm" data-user-id="{{ $user->id }}">Assign Person
                             </button>
                         @endif
                     </td>
                 </tr>
             @empty
                 <tr wire:key="empty-no-records">
                     <td colspan="99">
                         <div class="fs-5">No users match your query</div>
                     </td>
                 </tr>
             @endforelse
         </tbody>
     </table>

    <div class="my-4">
        {{ $users->links() }}
    </div>
</div>

<div class="modal fade" id="assignPersonForm" tabindex="-1" aria-labelledby="assignPersonFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.assign-person') }}">
                @csrf
                <input type="hidden" id="assignUserId" name="user_id" value="" />

                <div class="modal-header">
                    <h5 class="modal-title">Assign Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="min-height: 400px;">
                    <label for="assign_person_people_select">Select Person</label>
                    <select name="person_id" id="assignPersonId" required>
                        <option value=""></option>
                        @foreach($unassignedPeople as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    new Choices('#assignPersonId');

    let assignPersonFormModal = document.getElementById('assignPersonForm');
    let assignUserIdInput = document.getElementById('assignUserId');

    assignPersonFormModal.addEventListener('shown.bs.modal', function (event) {
        assignUserIdInput.value = event.relatedTarget.dataset.userId;
    });
</script>
