<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\UserGroup as Group; 
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
//use App\Jobs\UpdateUser;
//use App\Jobs\DeleteUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

//use Illuminate\Routing\Controller as BaseController;
use App\Abstracts\Http\Controller as BaseController;

class UserController extends BaseController
{

    //permission checking for the controller is done in BaseController
    public function index(Request $request)
    {
        $defaultPerPage = config('custom.defaultPerPage', 20); // Default per page value
        $perPageOptions = config('custom.perPageOptions'); // Define per page options

         // Extract filters from request
        $filters = $request->only(['name', 'email', 'per_page', 'page']);
        $perPage = $filters['per_page'] ?? $defaultPerPage;

        $query = User::with(['userPhoto','roles:id,name'])->select('id', 'name', 'email','is_active');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        $users = $query->paginate($perPage)->withQueryString();
        
        return Inertia::render('Users/Index', 
            compact(
                'users', 
                'filters', 
                'perPageOptions'
            ));
    }

    public function create()
    {
        return $this->form(); // Call form method to render the form with roles and groups
    }

    public function edit(User $user)
    {
        return $this->form($user);
    }

    public function form(?User $user = null)
    {
        $roles = Role::get(); // Fetch all roles using Spatie, exclude deleted roles
        $groups = Group::get(); // Fetch groups
        if ($user && $user->exists) { 
            $user->load(['roles','userGroup']); // Load roles and group relationships
            $user->photo_url = $user->userPhoto?->photo_url; 
        } else {
            $user = null;
        }

        return Inertia::render('Users/Form', compact('user', 'roles', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        return $this->saveUser($request);
    }

    public function update(UserRequest $request, User $user)
    {
        return $this->saveUser($request, $user);
    }
 
    private function saveUser(UserRequest $request, ?User $user = null)
    {
        $validated = $request->validated();
        $file = $request->file('photo');
        $removePhoto = $request->boolean('remove_photo');
        $change_password = $request->boolean('change_password');

        try {
            //(new UpdateUser($validated, $user, $file, $removePhoto))->handle();
            // Handle password
            if (!empty($validated['password'])) {
                if(($user && $change_password) || !$user)                
                    $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            //update user
            if ($user) {
                $user->update($validated);
                $user->syncRoles([$validated['role']]);

                // Handle photo removal
                if ($removePhoto) {
                    try{
                        $user->clearMediaCollection('photos');
                    } catch (\Exception $e) {
                        return redirect()->route('users.index')->with('error', $e->getMessage());
                    }
                }
                $messageKey = 'data_is_updated';
            } else {
                // create new user
                $user = User::create($validated);
                $user->assignRole($validated['role']);
                $messageKey = 'data_is_created';
            }

            // Handle file upload with Spatie Media Library
            if ($file) {
                try{
                    $user->clearMediaCollection('photos'); // Clear existing photos if any
                    $user->addMedia($file)->toMediaCollection('photos');               
                } catch (\Exception $e) {
                    return redirect()->route('users.index')->with('error', $e->getMessage());
                }
            }

            $name = $user ? $user->name : $validated['name'];
            $message = __('general.' . $messageKey, ['name' => $name]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return redirect()->route('users.index')->with('error', $message);
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    public function destroy(User $user)
    {
        try {            
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->delete(); 
            $message = __('general.data_is_deleted', ['name' => $user->name]);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }      
    }

    public function enable(Request $request, User $user)
    {
        return $this->setActive($request, $user, true);
    }
    
    public function disable(Request $request, User $user)
    {
        return $this->setActive($request, $user, false);
    }

    private function setActive(Request $request, User $user, $active = true)
    {
        $user->is_active = $active;
        $user->save();

        // get filter and page from requst, then redirect it with query string
        $query = $request->only(['name', 'email', 'page']);

        if($active)
            $message = __('general.set_enabled', ['name' => $user->name]);
        else
            $message = __('general.set_disabled', ['name' => $user->name]);

        Log::info('ENABLE USER', ['id' => $user->id, 'query' => $query]);
        return back()->with('success',$message);
    }
}
