<?php

namespace App\Http\Controllers\Admin;

use App\Helper\CommonHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\Admin;
use App\Models\RoleOld;
use Illuminate\Support\Facades\Validator;
use Session;

class UserController extends Controller
{
    public function index()
    {
        $data['users'] = Admin::all();
        $data['roles'] = RoleOld::all();
        return view('admin.user.index', $data);
    }

    public function edit($id)
    {
        $data['user'] = Admin::findOrFail($id);
        $data['roles'] = RoleOld::all();
        return view('admin.user.edit', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'image' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
            'username' => 'required|max:255|unique:admins',
            'email' => 'required|email|max:255|unique:admins',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'password' => 'required|confirmed',
            'role' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $user = new Admin;
        $user->role_id = $request->role;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->password = bcrypt($request->password); 

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/users/';
            $filename = CommonHelper::upload_file($image, $folderPath, '');
            if(!empty($filename)) $user->image = $filename; 
        }

        $user->save();

        Session::flash('success', 'User created successfully!');
        return "success";
    }


    public function update(Request $request)
    {
        $user = Admin::findOrFail($request->user_id);

        $rules = [
            'username' => [
                'required',
                'max:255',
                Rule::unique('admins')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins')->ignore($user->id),
            ],
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'role' => 'required',
        ];

        if ($request->filled('image')) {
            $rules['image'] = [
                function ($attribute, $value, $fail) use ($extImage, $allowedExts) {
                    if (!in_array($extImage, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                }
            ];
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $user->username = $request->username;
        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->status = $request->status;
        $user->role_id = $request->role;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/users/';
            $filename = CommonHelper::upload_file($image, $folderPath,  $user->image);
            if(!empty($filename)) {
                $user->image = $filename;
            } 
        }

        $user->save();

        Session::flash('success', 'User updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {
        if ($request->user_id == 1) {
            Session::flash('warning', 'You cannot delete the owner!');
            return back();
        }

        $user = Admin::findOrFail($request->user_id);
        @unlink('cms/users/' . $user->image);
        $user->delete();

        Session::flash('success', 'User deleted successfully!');
        return back();
    }

    public function managePermissions($id)
    {
        $data['user'] = Admin::find($id);
        return view('admin.user.permission.manage', $data);
    }

    public function updatePermissions(Request $request)
    {
        $permissions = json_encode($request->permissions);
        $user = Admin::find($request->user_id);
        $user->permissions = $permissions;
        $user->save();

        Session::flash('success', "Permissions updated successfully for '$user->name' user");
        return back();
    }
}
