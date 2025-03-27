<?php

namespace App\Http\Controllers\Admin;

use App\Models\BasicExtended;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Statistic;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();

        $lang_id = $lang->id;

        $data['lang_id'] = $lang_id;
        $data['abe'] = $lang->basic_extended;
        $data['selLang'] = Language::where('code', $request->language)->first();
        $data['statistics'] = Statistic::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();

        if ($request->ajax()) {
            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search')['value'] ?? null;
    
            $query = Statistic::where('language_id', $data['lang_id']);
    
            // Apply search filter
            if (!empty($search)) {
                $query->where('title', 'LIKE', "%$search%");
            }
    
            // Get total count before applying pagination
            $recordsTotal = $query->count();
    
            // Apply ordering, pagination, and fetch data
            $statistics = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();
            // dd($points);
    
            $languageParam = request()->input('language');
    
            // Modify each point for DataTables response
            foreach ($statistics as $key => $statistic) {
                $statistic->sr_no = $key + 1 + $start;
    
                // Ensure icon is properly formatted
                $statistic->icon = "<div class='d-flex flex-column'>
                                    <i class='" . e($statistic->icon) . "'></i>
                                </div>";
    
                $statistic->title = "<div class='d-flex flex-column'>" . e(convertUtf8($statistic->title)) . "</div>";
                $statistic->quantity = "<div class='d-flex flex-column'>" . e($statistic->quantity) . "</div>";
                $statistic->serial_number = "<div class='d-flex flex-column'>" . e($statistic->serial_number) . "</div>";
    
                // Action buttons
                $editUrl = route('admin.statistics.edit', $statistic->id) . '?language=' . e($languageParam);
    
                $statistic->action = "<div class='d-flex align-items-center gap-2'>
                    <a href='" . $editUrl . "' class='btn btn-sm btn-secondary' title='Edit'>
                        <i class='fas fa-pencil-alt'></i>
                    </a>
                    <button type='button' class='btn btn-sm btn-danger deletebutton' data-id='" . $statistic->id . "' title='Delete'>
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </div>";
            }
    
            // Prepare DataTables response
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data' => $statistics->toArray(),
            ]);
        }

        return view('admin.home.statistics.index', $data);
    }

    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $count = Statistic::where('language_id', $request->language_id)->count();
        if ($count == 4) {
            Session::flash('warning', 'You cannot add more than 4 statistics!');
            return "success";
        }

        $rules = [
            'language_id' => 'required',
            'title' => 'required|max:20',
            'quantity' => 'required|integer',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $statistic = new Statistic;
        $statistic->language_id = $request->language_id;
        $statistic->icon = $request->icon;
        $statistic->title = $request->title;
        $statistic->quantity = $request->quantity;
        $statistic->serial_number = $request->serial_number;
        $statistic->save();

        Session::flash('success', 'New statistic added successfully!');
        return "success";
    }

    public function edit($id)
    {
        $data['statistic'] = Statistic::findOrFail($id);
        if (!empty($data['statistic']->language)) {
            $data['selLang'] = $data['statistic']->language;
        }

        return view('admin.home.statistics.edit', $data);
    }

    public function update(Request $request)
    {
        $rules = [
            'title' => 'required|max:20',
            'quantity' => 'required|integer',
            'serial_number' => 'required|integer',
        ];

        $request->validate($rules);

        $statistic = Statistic::findOrFail($request->statisticid);
        $statistic->icon = $request->icon;
        $statistic->title = $request->title;
        $statistic->quantity = $request->quantity;
        $statistic->serial_number = $request->serial_number;
        $statistic->save();

        Session::flash('success', 'Statistic updated successfully!');
        return "success";
    }

    public function upload(Request $request, $langid)
    {
        $image = $request->background_image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $rules = [];

        if ($request->filled('background_image')) {
            $rules['background_image'] = [
                function ($attribute, $value, $fail) use ($extImage, $allowedExts) {
                    if (!in_array($extImage, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                }
            ];
        }

        $request->validate($rules);

        if ($request->filled('background_image')) {

            $be = BasicExtended::where('language_id', $langid)->first();

            @unlink('assets/front/img/' . $be->statistics_bg);
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/' . $filename);

            $be->statistics_bg = $filename;
            $be->save();

        }

        Session::flash('success', 'Statistics section background image');
        return back();
    }

    public function delete(Request $request)
    {

        $statistic = Statistic::findOrFail($request->user_id);
        $statistic->delete();

        Session::flash('success', 'Statistic deleted successfully!');

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back();
    }
}
