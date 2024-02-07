<div>
    <div class="d-lg-flex">
        <table class="table w-auto align-middle mb-4 mb-lg-0">
            <thead class="table-dark">
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="py-3">{{ $user->order }}</td>
                        <td class="py-3">{{ $user->full_name }}</td>
                        <td class="py-3">
                            <span class="badge fs-6 {{ $user->roles->first()->color ?? 'bg-secondary' }}">{{ $user->roles->first()->name ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3">
                            <span class="small text-body-secondary">{{ $user->created_at }}</span>
                        </td>
                        <td>
                            <button wire:click="selectUser('{{ $user->id }}')" type="button" class="btn btn-sm fs-5">
                                <i class="fa-sharp fa-light fa-arrows-to-line fa-xl"></i>
                            </button>
                            <div>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="ms-lg-5">
            <div>
                @if($selectedUser)
                    <div class="mb-5">
                        <div class="border border-quaternary border-2 min-width-400">
                            <label for="user-order" class="h5 bg-quaternary m-0 p-3 text-white d-block">Change {{ $selectedUser->name }}'s Order</label>
                            <div class="d-flex p-3">
                                <input wire:model="userOrder" type="text" class="form-control me-2" id="user-order">
                                <button wire:click="saveOrder" type="button" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button wire:click="clearSelectedUser" type="button" class="btn btn-sm btn-dark">Clear Selected User</button>
                        </div>
                    </div>
                @endif
            </div>
            <div>
                <div class="border border-tertiary border-2 min-width-400">
                    <label for="user-search" class="h5 bg-tertiary m-0 p-3 text-white d-block">Search Users / Roles</label>
                    <div class="d-flex p-3">
                        <input wire:model.lazy="userSearch" type="text" class="form-control me-2" id="user-search">
                        <button wire:click="search" type="button" class="btn btn-primary"><i class="fa-sharp fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>
                <div>
                    @if($this->userSearch)
                        <div>
                            <div class="mt-2">
                                <button wire:click="clearUserSearch" type="button" class="btn btn-sm btn-dark">Clear Search</button>
                            </div>
                            <div class="mt-3">
                                @empty($this->userResults)
                                    <div class="border px-3 py-2 text-danger">No users [without order assigned] <i class="fa-sharp fa-light fa-face-thinking"></i> try again?</div>
                                @else
                                    <div>
                                        <div class="h5">Results</div>
                                        <div class="list-group">
                                            @foreach($userResults as $userResult)
                                                <div wire:key="user-result-{{ $userResult['id'] }}" class="list-group-item list-group-item-action p-0">
                                                    <button wire:click="selectUser('{{ $userResult['id'] }}')" class="btn checkbox-choice-button d-flex align-items-center">
                                                        <span class="me-2 d-block h6 mb-0 text-body-secondary" style="min-width: 2rem">{{ $userResult['id'] }}</span>
                                                        <span class="me-2">{{ $userResult['name'] }} {{ $userResult['last_name'] }}</span>
                                                        <span class="d-block ms-auto text-body-secondary">{{ $userResult['email'] }}</span>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endempty
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
