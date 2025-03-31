<?php

namespace App\Http\Controllers\Admin;

use App\Helper\CommonHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Scategory;
use App\Models\Language;
use App\Models\Megamenu;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class ScategoryController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();

        $lang_id = $lang->id;
        $data['scategorys'] = Scategory::where('language_id', $lang_id)->orderBy('id', 'DESC')->paginate(10);

        $data['lang_id'] = $lang_id;

        if ($request->ajax()) {
            $draw = $request->input('draw');
            $start = $request->input('start');
            $length = $request->input('length');
            $search = $request->input('search')['value'] ?? null;

            $query = Scategory::select('users.*');

            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('email', 'LIKE', "%$search%")
                        ->orWhere('status', 'LIKE', "%$search%")
                        ->orWhere('feature', 'LIKE', "%$search%");
                });
            }

            // Get total count before applying pagination
            $recordsTotal = $query->count();

            // Apply ordering, pagination, and fetch data
            $scategorys = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();

            // Modify each user for DataTables response
            foreach ($scategorys as $key => $scategory) {
                $scategory->sr_no = $key + 1 + $start;

                // Checkbox column
                $scategory->checkbox = "<input type='checkbox' class='bulk-check' data-val='{$scategory->id}'>";

                // Name & Email formatted
                $scategory->username = "<div class='d-flex flex-column'>
                                    <p href='javascript:void(0)' class='text-gray-800 text-hover-primary mb-1'>" . convertUtf8($user->username) . "</p>
                                   </div>";

                // Status switch
                $statusClass = $user->status == 1 ? 'bg-success' : 'bg-danger';
                $user->status = "<form id='userForm{$user->id}' action='" . route('register.user.ban') . "' method='post'>
                                    " . csrf_field() . "
                                    <select class='form-control form-control-sm $statusClass' 
                                        name='status' onchange='document.getElementById(\"userForm{$user->id}\").submit();'>
                                        <option value='1' " . ($user->status == 1 ? 'selected' : '') . ">Active</option>
                                        <option value='0' " . ($user->status == 0 ? 'selected' : '') . ">Inactive</option>
                                    </select>
                                    <input type='hidden' name='user_id' value='{$user->id}'>
                                 </form>";

                // Email verification status
                $emailClass = strtolower($user->email_verified) == 'yes' ? 'bg-success' : 'bg-danger';
                $user->email_verified = "<form id='emailForm{$user->id}' action='" . route('register.user.email') . "' method='post'>
                                            " . csrf_field() . "
                                            <select class='form-control form-control-sm $emailClass' 
                                                name='email_verified' onchange='document.getElementById(\"emailForm{$user->id}\").submit();'>
                                                <option value='Yes' " . (strtolower($user->email_verified) == 'yes' ? 'selected' : '') . ">Verify</option>
                                                <option value='No' " . (strtolower($user->email_verified) == 'no' ? 'selected' : '') . ">Unverify</option>
                                            </select>
                                            <input type='hidden' name='user_id' value='{$user->id}'>
                                         </form>";

                // Action buttons
                $user->action = "<div class='d-flex align-items-center gap-2'>
                <a href='" . route('register.user.view', $user->id) . "' class='btn btn-sm btn-primary' title='View'>
                    <i class='fa fa-eye'></i>
                </a>
                <a href='" . route('register.user.changePass', $user->id) . "' class='btn btn-sm btn-warning' title='Change Password'>
                    <i class='fa fa-key'></i>
                </a>
                <a class='btn btn-sm btn-danger deletebutton' data-id='" . $user->id . "' title='Delete'>
                     <i class='fas fa-trash-alt'></i>
                </a>
                </div>";
            }

            // Prepare DataTables response
            $response = [
                'draw' => intval($draw),
                'recordsTotal' => $recordsTotal ?? 0, // Ensure this is not null
                'recordsFiltered' => $recordsTotal ?? 0, // Ensure this is not null
                'data' => $users->toArray() ?? [], // Ensure it's an array
            ];

            return response()->json($response);
        }

        return view('admin.service.scategory.index', $data);
    }

    public function edit($id)
    {
        $data['scategory'] = Scategory::findOrFail($id);
        return view('admin.service.scategory.edit', $data);
    }

    public function store(Request $request)
    {

        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'name' => 'required|max:255',
            'short_text' => 'required',
            'status' => 'required',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $scategory = new Scategory;
        $scategory->language_id = $request->language_id;
        $scategory->name = $request->name;
        $scategory->status = $request->status;
        $scategory->short_text = $request->short_text;
        $scategory->serial_number = $request->serial_number;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/scategory/';
            $filename = CommonHelper::upload_file($image, $folderPath, '');
            if(!empty($filename)) $scategory->image = $filename; 
        }

        $scategory->save();

        Session::flash('success', 'Category added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'status' => 'required',
            'short_text' => 'required',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $scategory = Scategory::findOrFail($request->scategory_id);
        $scategory->name = $request->name;
        $scategory->status = $request->status;
        $scategory->short_text = $request->short_text;
        $scategory->serial_number = $request->serial_number;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/scategory/';
            $filename = CommonHelper::upload_file($image, $folderPath,  $scategory->image);
            if(!empty($filename)) {
                $scategory->image = $filename;
            } 
        }

        $scategory->save();

        Session::flash('success', 'Category updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {
        $scategory = Scategory::findOrFail($request->scategory_id);

        if ($scategory->services()->count() > 0) {
            Session::flash('warning', 'First, delete all the services under this category!');
            return back();
        }
        @unlink('cms/scategory/' . $scategory->image);

        $this->deleteFromMegaMenu($scategory);

        $scategory->delete();

        Session::flash('success', 'Scategory deleted successfully!');
        return back();
    }

    public function deleteFromMegaMenu($scategory) {
        $megamenu = Megamenu::where('language_id', $scategory->language_id)->where('category', 1)->where('type', 'services');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            $catId = $scategory->id;
            if (is_array($menus) && array_key_exists("$catId", $menus)) {
                unset($menus["$catId"]);
                $megamenu->menus = json_encode($menus);
                $megamenu->save();
            }
        }
        $megamenu = Megamenu::where('language_id', $scategory->language_id)->where('category', 1)->where('type', 'portfolios');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            $catId = $scategory->id;
            if (is_array($menus) && array_key_exists("$catId", $menus)) {
                unset($menus["$catId"]);
                $megamenu->menus = json_encode($menus);
                $megamenu->save();
            }
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $scategory = Scategory::findOrFail($id);
            if ($scategory->services()->count() > 0) {
                Session::flash('warning', 'First, delete all the services under the selected categories!');
                return "success";
            }
        }

        foreach ($ids as $id) {
            $scategory = Scategory::findOrFail($id);
            @unlink('assets/front/img/service_category_icons/' . $scategory->image);

            $this->deleteFromMegaMenu($scategory);

            $scategory->delete();
        }

        Session::flash('success', 'Service categories deleted successfully!');
        return "success";
    }

    public function feature(Request $request)
    {
        $scategory = Scategory::find($request->scategory_id);
        $scategory->feature = $request->feature;
        $scategory->save();

        if ($request->feature == 1) {
            Session::flash('success', 'Featured successfully!');
        } else {
            Session::flash('success', 'Unfeatured successfully!');
        }

        return back();
    }
}
