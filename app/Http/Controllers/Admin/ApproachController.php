<?php

namespace App\Http\Controllers\Admin;

use App\Models\BasicExtended;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BasicSetting as BS;
use App\Models\Point;
use App\Models\Language;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ApproachController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();
    
        if (!$lang) {
            return response()->json(['error' => 'Language not found'], 404);
        }
    
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['points'] = Point::where('language_id', $data['lang_id'])->orderBy('id', 'DESC')->get();
    
        if ($request->ajax()) {
            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search')['value'] ?? null;
    
            $query = Point::where('language_id', $data['lang_id']);
    
            // Apply search filter
            if (!empty($search)) {
                $query->where('title', 'LIKE', "%$search%");
            }
    
            // Get total count before applying pagination
            $recordsTotal = $query->count();
    
            // Apply ordering, pagination, and fetch data
            $points = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();
            // dd($points);
    
            $languageParam = request()->input('language');
    
            // Modify each point for DataTables response
            foreach ($points as $key => $point) {
                $point->sr_no = $key + 1 + $start;
    
                // Ensure icon is properly formatted
                $point->icon = "<div class='d-flex flex-column'>
                                    <i class='" . e($point->icon) . "'></i>
                                </div>";
    
                $point->title = "<div class='d-flex flex-column'>" . e(convertUtf8($point->title)) . "</div>";
                $point->serial_number = "<div class='d-flex flex-column'>" . e($point->serial_number) . "</div>";
    
                // Action buttons
                $editUrl = route('admin.approach.point.edit', $point->id) . '?language=' . e($languageParam);
    
                $point->action = "<div class='d-flex align-items-center gap-2'>
                    <a href='" . $editUrl . "' class='btn btn-sm btn-secondary' title='Edit'>
                        <i class='fas fa-pencil-alt'></i>
                    </a>
                    <button type='button' class='btn btn-sm btn-danger deletebutton' data-id='" . $point->id . "' title='Delete'>
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </div>";
            }
    
            // Prepare DataTables response
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data' => $points->toArray(),
            ]);
        }
    
        return view('admin.home.approach.index', $data);
    }
    
    

    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'title' => 'required',
            'short_text' => 'required',
            'serial_number' => 'required|integer',
        ];

        $be = BasicExtended::first();
        $version = $be->theme_version;

        if ($version == 'cleaning') {
            $rules['color'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $point = new Point;
        $point->language_id = $request->language_id;
        $point->icon = $request->icon;
        if ($version == 'cleaning') {
            $point->color = $request->color;
        }
        $point->title = $request->title;
        $point->short_text = $request->short_text;
        $point->serial_number = $request->serial_number;
        $point->save();

        Session::flash('success', 'New point added successfully!');
        return "success";
    }

    public function pointedit($id)
    {
        $data['point'] = Point::findOrFail($id);
        return view('admin.home.approach.edit', $data);
    }

    public function update(Request $request, $langid)
    {
        $request->validate([
            'approach_section_title' => 'required|max:25',
            'approach_section_subtitle' => 'required|max:80',
            'approach_section_button_text' => 'nullable|max:15',
            'approach_section_button_url' => 'nullable|max:255',
        ]);

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $bs->approach_title = $request->approach_section_title;
        $bs->approach_subtitle = $request->approach_section_subtitle;
        $bs->approach_button_text = $request->approach_section_button_text;
        $bs->approach_button_url = $request->approach_section_button_url;
        $bs->save();

        Session::flash('success', 'Text updated successfully!');
        return back();
    }

    public function pointupdate(Request $request)
    {
        $rules = [
            'title' => 'required',
            'short_text' => 'required',
            'serial_number' => 'required|integer',
        ];

        $be = BasicExtended::first();
        $version = $be->theme_version;

        if ($version == 'cleaning') {
            $rules['color'] = 'required';
        }

        $request->validate($rules);

        $point = Point::findOrFail($request->pointid);
        $point->icon = $request->icon;
        if ($version == 'cleaning') {
            $point->color = $request->color;
        }
        $point->title = $request->title;
        $point->short_text = $request->short_text;
        $point->serial_number = $request->serial_number;
        $point->save();

        Session::flash('success', 'Point updated successfully!');
        
        return "success";
    }

    public function pointdelete(Request $request)
    {

        $point = Point::findOrFail($request->user_id);
        
        $point->delete();

        Session::flash('success', 'Point deleted successfully!');

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }
}
