<div>
    <form wire:submit.prevent="save">
        <table class="table width-auto align-middle">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($children as $index => $childContent)
                    <tr wire:key="children-{{ $childContent->id }}">
                        <td>
                            <input wire:model="children.{{ $index }}.order" type="number" step="1" class="form-control max-width-80">
                        </td>
                        <td>{{ $childContent->name }}</td>
                        <td>
                            <a class="btn-link text-primary" href="{{ route('admin.content.edit', $childContent->id) }}">
                                <i class="fa-duotone fa-pen-to-square me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex flex-wrap align-items-center">
            <button type="submit" class="btn btn-primary">Save</button>
            @if($success)
                <div class="text-success ms-3 fw-bold">{{ $success }}</div>
            @endif
        </div>
    </form>
</div>
