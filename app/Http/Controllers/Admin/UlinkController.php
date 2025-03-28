<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Ulink;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class UlinkController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();
        $lang_id = $lang->id;
        $data['aulinks'] = Ulink::where('language_id', $lang_id)->get();
        $data['lang_id'] = $lang_id;

        if ($request->ajax()) {
            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search')['value'] ?? null;
    
            $query = Ulink::where('language_id', $data['lang_id']);
    
            // Apply search filter
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%$search%");
            }
    
            // Get total count before applying pagination
            $recordsTotal = $query->count();
    
            // Apply ordering, pagination, and fetch data
            $ulinks = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();
            $languageParam = request()->input('language');
    
            // Modify each point for DataTables response
            foreach ($ulinks as $key => $ulink) {
                $ulink->sr_no = $key + 1 + $start;
    
                $ulink->name = "<div class='d-flex flex-column'>" . e(convertUtf8($ulink->name)) . "</div>";
                $ulink->url = "<div class='d-flex flex-column'>" . e($ulink->url) . "</div>";
    
                // Action buttons
                // $editUrl = route('admin.ulink.edit', $ulink->id) . '?language=' . e($languageParam);
    
                $ulink->action = "<div class='d-flex align-items-center gap-2'>
                <button type='button' class='btn btn-sm btn-secondary editButton' 
                    data-id='" . $ulink->id . "' 
                    data-name='" . e(strip_tags($ulink->name)) . "' 
                    data-url='" . e(strip_tags($ulink->url)) . "' 
                    title='Edit'>
                    <i class='fas fa-pencil-alt'></i>
                </button>
                <button type='button' class='btn btn-sm btn-danger deletebutton' 
                    data-id='" . $ulink->id . "' 
                    title='Delete'>
                    <i class='fas fa-trash-alt'></i>
                </button>
            </div>";
            
            }
    
            // Prepare DataTables response
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data' => $ulinks->toArray(),
            ]);
        }

        return view('admin.footer.ulink.index', $data);
    }

    public function edit($id)
    {
        $data['ulink'] = Ulink::findOrFail($id);
        return view('admin.footer.ulink.edit', $data);
    }

    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'name' => 'required|max:255',
            'url' => 'required|max:255'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $ulink = new Ulink;
        $ulink->language_id = $request->language_id;
        $ulink->name = $request->name;
        $ulink->url = $request->url;
        $ulink->save();

        Session::flash('success', 'Useful link added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'url' => 'required|max:255'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $ulink = Ulink::findOrFail($request->ulink_id);
        $ulink->name = $request->name;
        $ulink->url = $request->url;
        $ulink->save();

        Session::flash('success', 'Useful link updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {

        $ulink = Ulink::findOrFail($request->ulink_id);
        $ulink->delete();

        Session::flash('success', 'Ulink deleted successfully!');

        if($request->ajax()) {
            return response()->json(['success'=>true]);
        }
        return back();
    }
}
