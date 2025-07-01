<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Abstracts\Http\Controller as BaseController;
use App\Models\UserGroup as Group; 
use Inertia\Inertia;
use App\Http\Requests\UserGroupRequest;

class UserGroupController extends BaseController
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $defaultPerPage = config('custom.defaultPerPage', 20); // Default per page value
        $perPageOptions = config('custom.perPageOptions'); // Define per page options

         // Extract filters from request
        $filters = $request->only(['per_page', 'page']);
        $perPage = $filters['per_page'] ?? $defaultPerPage;

        $groups = Group::paginate($perPage)->withQueryString();        
        
        return Inertia::render('UserGroups/Index', compact(
            'groups', 
            'filters', 
            'perPageOptions'
        ));
    }

     public function create()
    {
        return $this->form(); // Call form method to render the form with roles and groups
    }

    public function edit(Group $usergroup)
    {
        return $this->form($usergroup);
    }

    public function form(?Group $usergroup = null)
    {
        return Inertia::render('UserGroups/Form', compact('usergroup'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(UserGroupRequest $request)
    {
        return $this->saveUserGroup($request);
    }

    public function update(UserGroupRequest $request, Group $usergroup)
    {
        return $this->saveUserGroup($request, $usergroup);
    }
 
    private function saveUserGroup($request, ?Group $usergroup = null)
    {
        $validated = $request->validated();
    
        try {
             if ($usergroup) {
                $usergroup->update($validated);
                $messageKey = 'data_is_updated';
            } else {
                $usergroup = Group::create($validated);
                $messageKey = 'data_is_created';
            }

            $name = $usergroup->name;
            $message = __('general.' . $messageKey, ['name' => $name]);
            
            return redirect()->route('usergroups.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('usergroups.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $usergroup)
    {
        // Check if the user group is still used by any users
        if ($usergroup->users()->exists()) {
            $message =  __('general.data_is_still_used', ['name' => $usergroup->name]);
            return back()->with('error', $message);
        }

        try {
            $usergroup->delete(); // soft delete                
            $message =  __('general.data_is_deleted', ['name' => $usergroup->name]);

            return back()->with('success',$message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function enable(Request $request, Group $usergroup)
    {
        return $this->setActive($request, $usergroup, true);
    }
    
    public function disable(Request $request, Group $usergroup)
    {
        return $this->setActive($request, $usergroup, false);
    }

    private function setActive($request, $usergroup, $active)
    {
        try {
            $usergroup->is_active = $active;
            $usergroup->save();

            // get page from requst, then redirect it with query string
            $query = $request->only(['page']);

            if($active)
                $message = __('general.set_enabled', ['name' => $usergroup->name]);
            else
                $message = __('general.set_disabled', ['name' => $usergroup->name]);

            return redirect()->route('usergroups.index', $query)->with('success',$message);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
