<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permission.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'group_name' => 'required|max:255',
            'status' => 'required|in:0,1', // Ensure status is either 0 or 1
            'basic' => 'required|in:0,1', // Ensure is_basic is either 0 or 1
        ];
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
    
        $permissions = new Permission;
        $permissions->name = $request->name;
        $permissions->group_name = $request->group_name;
        $permissions->status = $request->status; // Store status
        $permissions->basic = $request->basic; // Store is_basic
        $permissions->save();
    
        Session::flash('success', 'Permission added successfully!');
        return "success";
    }
    

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'group_name' => 'required|max:255',
            'status' => 'required|in:0,1', // Ensuring status is either 0 or 1
            'basic' => 'required|in:0,1', // Ensuring is_basic is either 0 or 1
        ];
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
    
        $permission = Permission::findOrFail($request->permission_id);
        $permission->name = $request->name;
        $permission->group_name = $request->group_name;
        $permission->status = $request->status; // Update status
        $permission->basic = $request->basic; // Update is_basic
        $permission->save();
    
        Session::flash('success', 'Permission updated successfully!');
        return "success";
    }
    

    public function delete(Request $request)
    {

        $permissions = Permission::findOrFail($request->permission_id);
        // if ($permissions->admins()->count() > 0) {
        //     Session::flash('warning', 'Please delete permission');
        //     return back();
        // }
        $permissions->delete();

        Session::flash('success', 'Permission deleted successfully!');
        return back();
    }
}
