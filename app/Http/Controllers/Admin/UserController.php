<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\StoreUserRequest;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.users.index');
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', [
            'roles' => $roles
        ]);
    }

    public function store(StoreUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        try {
            $user = User::create($validated);
            $user->roles()->sync($validated['roles']);
        } catch(\Throwable $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.users.index')->with('success', 'Created ' . $user->name);
    }

    public function show($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = User::with([
            'articles',
            'jobs',
            'jobSubscriptions',
            'listings',
            'marketplaceLogs',
            'person',
            'roles',
            'subscription',
        ])->withCount([
            'articles',
            'jobs',
            'jobSubscriptions',
            'listings',
            'marketplaceLogs',
        ])->findOrFail($id);

        return view('admin.users.show', [
            'user' => $user
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function assignPerson(Request $request)
    {
        $user = User::findOrFail($request->input('user_id'));
        $person = Person::findOrFail($request->input('person_id'));

        $person->user_id = $user->id;
        $person->save();

        return redirect()->route('admin.users.index')->with('success', 'Person "' . $person->name . '" assigned to ' . $user->name);
    }

    public function subscriptions(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.users.subscriptions');
    }
}
