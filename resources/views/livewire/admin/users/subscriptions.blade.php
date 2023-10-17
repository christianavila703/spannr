<div>
    <div class="my-2 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="ms-auto lead">
            {{ $subscriptions->total() }} results
        </div>
    </div>

     <table class="table">
         <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Status</th>
                <th>Card</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Subscription ID</th>
            </tr>
         </thead>
         <tbody>
             @foreach ($subscriptions as $subscription)
                 <tr class="no-bottom-border">
                     <td>
                         {{ $subscription->user->full_name }}</span>
                     </td>
                     <td>{{ $subscription->user->email }}</td>
                     <td>Spannr+</td>
                     <td>
                         @if($subscription->status === \App\Models\UserSubscription::STATUS_ACTIVE)
                             <span class="badge bg-success">Active</span>
                         @elseif($subscription->status === \App\Models\UserSubscription::STATUS_INACTIVE)
                             <span class="badge bg-danger">Inactive</span>
                         @elseif($subscription->status === \App\Models\UserSubscription::STATUS_CANCELLED)
                             <span class="badge bg-warning">Canceled</span>
                         @endif
                     </td>
                     <td>**** {{ $subscription->card_number }}</td>
                     <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('M j, Y') }}</td>
                     <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('M j, Y') }}</td>
                     <td>{{ $subscription->stripe_subscription_id }}</td>
                 </tr>
             @endforeach
         </tbody>
     </table>

    <div class="my-4">
        {{ $subscriptions->links() }}
    </div>
</div>
