<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
//use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
//use Illuminate\Routing\Controller as BaseController;
use App\Abstracts\Http\Controller as BaseController;
use App\Models\Role;

class RoleController extends BaseController
{

    //permission checking for the controller is done in BaseController
    public function index(Request $request)
    {
        $defaultPerPage = config('custom.defaultPerPage', 20); // Default per page value
        $perPageOptions = config('custom.perPageOptions'); // Define per page options
        
        $filters = $request->only(['per_page', 'page']);
        $perPage = $filters['per_page'] ?? $defaultPerPage;
        $query = Role::with(['permissions']);
        
        $roles = $query->paginate($perPage)->withQueryString();
        
        return Inertia::render('Roles/Index', compact(
            'roles',
            'filters', 
            'perPageOptions'
        ));
    }

    public function create()
    {
        return $this->form();
    }
    
    // Show the matrix for a specific role
    public function edit(Role $role)
    {
        return $this->form($role);
    }
    
    public function form(?Role $role = null)
    {
        if($role && $role->exists){
            $role->load('permissions');
        }
 
        $all_permissions = Permission::all();

        return Inertia::render('Roles/Form', compact('role', 'all_permissions'));
    }

     /**
     * Store a newly created resource in storage.
     */
   public function store(RoleRequest $request)
    {
        return $this->saveRole($request);
    }

    public function update(RoleRequest $request, Role $role)
    {
        return $this->saveRole($request, $role);
    }
 
    private function saveRole($request, ?Role $role = null)
    {
        $validated = $request->validated();
    
        try {
             if ($role) {
                $role->update($validated);
                $messageKey = 'data_is_updated';
            } else {
                $role = Role::create($validated);
                $messageKey = 'data_is_created';
            }

            $role->syncPermissions($request->permissions ?? []);

            $name = $role->name;
            $message = __('general.' . $messageKey, ['name' => $name]);
            
            return redirect()->route('roles.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
         // Check if the user group is still used by any users
        if ($role->users()->exists()) {
            $message =  __('general.data_is_still_used', ['name' => $role->name]);
            return back()->with('error', $message);
        }
        
        try {            
            $role->delete(); // soft delete 
            $message =  __('general.data_is_deleted', ['name' => $role->name]);
            return back()->with('success',$message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}