<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Dlink;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DlinkController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();
        $lang_id = $lang->id;
        $data['dlink'] = Dlink::where('language_id', $lang_id)->get();
        $data['lang_id'] = $lang_id;

        if ($request->ajax()) {
            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search')['value'] ?? null;
    
            $query = Dlink::where('language_id', $data['lang_id']);
    
            // Apply search filter
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%$search%");
            }
    
            // Get total count before applying pagination
            $recordsTotal = $query->count();
    
            // Apply ordering, pagination, and fetch data
            $dlinks = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();
            // $languageParam = request()->input('language');
    
            // Modify each point for DataTables response
            foreach ($dlinks as $key => $dlink) {
                $dlink->sr_no = $key + 1 + $start;
    
                $dlink->name = "<div class='d-flex flex-column'>" . e(convertUtf8($dlink->name)) . "</div>";
                $dlink->url = "<div class='d-flex flex-column'>" . e($dlink->url) . "</div>";
    
                // Action buttons
                // $editUrl = route('admin.ulink.edit', $ulink->id) . '?language=' . e($languageParam);
    
                $dlink->action = "<div class='d-flex align-items-center gap-2'>
                <button type='button' class='btn btn-sm btn-secondary editButton' 
                    data-id='" . $dlink->id . "' 
                    data-name='" . e(strip_tags($dlink->name)) . "' 
                    data-url='" . e(strip_tags($dlink->url)) . "' 
                    title='Edit'>
                    <i class='fas fa-pencil-alt'></i>
                </button>
                <button type='button' class='btn btn-sm btn-danger deletebutton' 
                    data-id='" . $dlink->id . "' 
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
                'data' => $dlinks->toArray(),
            ]);
        }

        return view('admin.footer.dlink.index', $data);
    }

    public function edit($id)
    {
        $data['dlink'] = Dlink::find($id);
        return view('admin.footer.dlink.edit', $data);
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

        $dlink = new Dlink;
        $dlink->language_id = $request->language_id;
        $dlink->name = $request->name;
        $dlink->url = $request->url;
        $dlink->save();

        Session::flash('success', 'Department link added successfully!');
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
        $dlink = Dlink::find($request->dlink_id);
        $dlink->name = $request->name;
        $dlink->url = $request->url;
        $dlink->save();

        Session::flash('success', 'Department link updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {

        $dlink = Dlink::find($request->dlink_id);
        $dlink->delete();

        Session::flash('success', 'Department deleted successfully!');

        if($request->ajax()){
            return response(['success'=>true]);
        }
        return back();
    }
}
