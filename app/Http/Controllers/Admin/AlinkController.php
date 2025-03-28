<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Alink;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AlinkController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();
        $lang_id = $lang->id;
        $data['alink'] = Alink::where('language_id', $lang_id)->get();
        $data['lang_id'] = $lang_id;

        if ($request->ajax()) {
            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search')['value'] ?? null;
    
            $query = Alink::where('language_id', $data['lang_id']);
    
            // Apply search filter
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%$search%");
            }
    
            // Get total count before applying pagination
            $recordsTotal = $query->count();
    
            // Apply ordering, pagination, and fetch data
            $alinks = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();
            $languageParam = request()->input('language');
    
            // Modify each point for DataTables response
            foreach ($alinks as $key => $alink) {
                $alink->sr_no = $key + 1 + $start;
    
                $alink->name = "<div class='d-flex flex-column'>" . e(convertUtf8($alink->name)) . "</div>";
                $alink->url = "<div class='d-flex flex-column'>" . e($alink->url) . "</div>";
    
                // Action buttons
                // $editUrl = route('admin.ulink.edit', $ulink->id) . '?language=' . e($languageParam);
    
                $alink->action = "<div class='d-flex align-items-center gap-2'>
                <button type='button' class='btn btn-sm btn-secondary editButton' 
                    data-id='" . $alink->id . "' 
                    data-name='" . e(strip_tags($alink->name)) . "' 
                    data-url='" . e(strip_tags($alink->url)) . "' 
                    title='Edit'>
                    <i class='fas fa-pencil-alt'></i>
                </button>
                <button type='button' class='btn btn-sm btn-danger deletebutton' 
                    data-id='" . $alink->id . "' 
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
                'data' => $alinks->toArray(),
            ]);
        }

        return view('admin.footer.alink.index',$data);
    }

    public function edit($id)
    {
        $data['alink'] = Alink::find($id);
        return view('admin.footer.alink.edit', $data);
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

        $alink = new Alink;
        $alink->language_id = $request->language_id;
        $alink->name = $request->name;
        $alink->url = $request->url;
        $alink->save();

        Session::flash('success', 'About link added successfully!');
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

        $alink = Alink::find($request->alink_id);
        $alink->name = $request->name;
        $alink->url = $request->url;
        $alink->save();

        Session::flash('success', 'About link updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {
        $alink = Alink::find($request->alink_id);
        $alink->delete();

        Session::flash('success', 'About deleted successfully!');

        if($request->ajax()) {
        return response()->json(['success'=>true]);
        }
        return back();
    }
}
