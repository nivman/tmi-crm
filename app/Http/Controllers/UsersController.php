<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function changePassword(Request $request)
    {
        $this->authorize('update');
        $v = $request->validate([
            'current'  => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!(Hash::check($v['current'], $request->user()->password))) {
            return response(['message' => 'Current password does not match.'], 422);
        }

        if (strcmp($v['current'], $v['password']) == 0) {
            return response(['message' => 'Password cannot be same as your current password'], 422);
        }

        if (demo()) {
            return response(['message' => 'This feature is disabled on demo.'], 422);
        }

        $request->user()->fill([
            'password' => Hash::make($v['password']),
        ])->save();

        return response(['success' => true], 204);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete');
        if ($user->id == auth()->id()) {
            return response(['message' => 'You can not delete your own account.'], 422);
        } elseif ($user->expenses()->exists()) {
            return response(['message' => 'User has been attached to some expenses and can not be deleted.'], 422);
        } elseif ($user->incomes()->exists()) {
            return response(['message' => 'User has been attached to some incomes and can not be deleted.'], 422);
        } elseif ($user->invoices()->exists()) {
            return response(['message' => 'User has been attached to some invoices and can not be deleted.'], 422);
        } elseif ($user->purchases()->exists()) {
            return response(['message' => 'User has been attached to some purchases and can not be deleted.'], 422);
        }

        if (demo()) {
            return response(['message' => 'This feature is disabled on demo.'], 422);
        }

        $user->events()->delete();
        $user->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {
        $this->authorize('delete');
        if ($request->all) {
            return User::select(['id', 'name', 'id as value'])->orderBy('name', 'asc')->get();
        }
        return response()->json(User::vueTable(User::$columns));
    }

    public function roles()
    {
        return \App\Role::all();
    }

    public function show(User $user)
    {
        $this->authorize('update', $user);
        return $user->load('roles');
    }

    public function store(Request $request)
    {
        $this->authorize('delete');
        $v = $request->validate([
            'phone'    => 'nullable|string',
            'name'     => 'required|string|max:255',
            'username' => ['required', 'max:25', 'unique:users', 'regex:/^[\w\-\_\.]*$/'],
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::create($v);
        try {
            \Mail::to($user->email)->send(new \App\Mail\UserCreated($v));
        } catch (\Exception $e) {
            \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
        }
        return $user;
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $super = auth()->user()->hasRole('super');
        if ($super) {
            $v = $request->validate([
                'roles'       => 'nullable|array',
                'vendor_id'   => 'nullable|exists:vendors,id',
                'customer_id' => 'nullable|exists:customers,id',
                'phone'       => 'nullable|string',
                'name'        => 'required|string|max:255',
                'username'    => [
                    'required', 'max:25', 'regex:/^[\w\-\_\.]*$/',
                    Rule::unique('users')->ignore($user->id),
                ],
                'email' => [
                    'required', 'string', 'email', 'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'nullable|string|min:6|confirmed',
            ]);
        } else {
            $v = $request->validate([
                'phone' => 'nullable|string',
                'name'  => 'required|string|max:255',
            ]);
        }

        $roles = false;
        if ($super) {
            if (isset($v['password']) && !empty($v['password'])) {
                try {
                    \Mail::to($user->email)->send(new \App\Mail\UserPasswoedReset($v));
                } catch (\Exception $e) {
                    \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
                }
                $v['password'] = Hash::make($v['password']);
            }
            if (isset($v['roles']) && !empty($v['roles'])) {
                foreach ($v['roles'] as $role) {
                    if (!empty($role['id'])) {
                        $roles[] = $role['id'];
                    }
                }
            }
            unset($v['roles']);
        }

        if ($user->id <= 5 && demo()) {
            return response(['message' => 'This feature is disabled on demo.'], 422);
        }

        $user->update($v);
        if (!empty($roles)) {
            $user->roles()->sync($roles);
        }
        return $user->load('roles');
    }
}
