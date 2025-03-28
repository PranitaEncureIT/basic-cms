<?php

namespace App\Http\Controllers\Admin;

use App\Helper\CommonHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();

        $lang_id = $lang->id;
        $data['partners'] = Partner::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();
        $data['lang_id'] = $lang_id;

        if ($request->ajax()) {
            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search')['value'] ?? null;
    
            $query = Partner::where('language_id', $data['lang_id']);
    
            // Apply search filter
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%$search%");
            }
    
            // Get total count before applying pagination
            $recordsTotal = $query->count();
    
            // Apply ordering, pagination, and fetch data
            $partners = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();
            $languageParam = request()->input('language');
    
            // Modify each point for DataTables response
            foreach ($partners as $key => $partner) {
                $partner->sr_no = $key + 1 + $start;

                $imagePath = public_path('cms/partners/' . $partner->image);
                if (!empty($partner->image) && file_exists($imagePath)) {
                    $imageUrl = asset('cms/partners/' . $partner->image);
                } else {
                    $imageUrl = asset('assets/front/img/no_image.jpg');
                }
    
                // Ensure image is properly formatted
                $partner->image = "<div class='d-flex flex-column'>
                <img src='" . e($imageUrl) . "' alt='Partner Image' style='width:100px;'>
           </div>";
    
                // Action buttons
                $editUrl = route('admin.partner.edit', $partner->id) . '?language=' . e($languageParam);
    
                $partner->action = "<div class='d-flex align-items-center gap-2'>
                    <a href='" . $editUrl . "' class='btn btn-sm btn-secondary' title='Edit'>
                        <i class='fas fa-pencil-alt'></i>
                    </a>
                    <button type='button' class='btn btn-sm btn-danger deletebutton' data-id='" . $partner->id . "' title='Delete'>
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </div>";
            }
    
            // Prepare DataTables response
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data' => $partners->toArray(),
            ]);
        }
        return view('admin.home.partner.index', $data);
    }

    public function edit($id)
    {
        $data['partner'] = Partner::findOrFail($id);
        return view('admin.home.partner.edit', $data);
    }


    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'url' => 'required|max:255',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $partner = new Partner;
        $partner->language_id = $request->language_id;
        $partner->url = $request->url;
        $partner->serial_number = $request->serial_number;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/partners/';
            $filename = CommonHelper::upload_file($image, $folderPath, '');
            if(!empty($filename)) $partner->image = $filename; 
        }

        $partner->save();

        Session::flash('success', 'Partner added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'url' => 'required|max:255',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $partner = Partner::findOrFail($request->partner_id);
        $partner->url = $request->url;
        $partner->serial_number = $request->serial_number;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/partners/';
            $filename = CommonHelper::upload_file($image, $folderPath,  $partner->image);
            if(!empty($filename)) {
                $partner->image = $filename;
            } 
        }

        $partner->save();

        Session::flash('success', 'Partner updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {

        $partner = Partner::findOrFail($request->partner_id);
        @unlink('cms/partners/' . $partner->image);
        $partner->delete();

        Session::flash('success', 'Partner deleted successfully!');

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back();
    }
}
