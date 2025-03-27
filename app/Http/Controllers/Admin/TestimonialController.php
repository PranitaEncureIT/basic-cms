<?php

namespace App\Http\Controllers\Admin;

use App\Helper\CommonHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Testimonial;
use App\Models\BasicSetting as BS;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['testimonials'] = Testimonial::where('language_id', $data['lang_id'])->orderBy('id', 'DESC')->get();
        
        if ($request->ajax()) {
            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search')['value'] ?? null;
    
            $query = Testimonial::where('language_id', $data['lang_id']);
    
            // Apply search filter
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%$search%");
            }
    
            // Get total count before applying pagination
            $recordsTotal = $query->count();
    
            // Apply ordering, pagination, and fetch data
            $testimonials = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();
            // dd($points);
            // dd($testimonials);
            $languageParam = request()->input('language');
    
            // Modify each point for DataTables response
            foreach ($testimonials as $key => $testimonial) {
                $testimonial->sr_no = $key + 1 + $start;

                $imagePath = public_path('cms/testimonial/' . $testimonial->image);
                if (!empty($testimonial->image) && file_exists($imagePath)) {
                    $imageUrl = asset('cms/testimonial/' . $testimonial->image);
                } else {
                    $imageUrl = asset('assets/front/img/no_image.jpg');
                }
    
                // Ensure image is properly formatted
                $testimonial->image = "<div class='d-flex flex-column'>
                <img src='" . e($imageUrl) . "' alt='Testimonial Image' style='width:100px;'>
           </div>";
    
                $testimonial->name = "<div class='d-flex flex-column'>" . e(convertUtf8($testimonial->name)) . "</div>";
                $testimonial->rank = "<div class='d-flex flex-column'>" . e($testimonial->rank) . "</div>";
                $testimonial->serial_number = "<div class='d-flex flex-column'>" . e($testimonial->serial_number) . "</div>";
    
                // Action buttons
                $editUrl = route('admin.testimonial.edit', $testimonial->id) . '?language=' . e($languageParam);
    
                $testimonial->action = "<div class='d-flex align-items-center gap-2'>
                    <a href='" . $editUrl . "' class='btn btn-sm btn-secondary' title='Edit'>
                        <i class='fas fa-pencil-alt'></i>
                    </a>
                    <button type='button' class='btn btn-sm btn-danger deletebutton' data-id='" . $testimonial->id . "' title='Delete'>
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </div>";
            }
    
            // Prepare DataTables response
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data' => $testimonials->toArray(),
            ]);
        }


        return view('admin.home.testimonial.index', $data);
    }

    public function edit($id)
    {
        $data['testimonial'] = Testimonial::findOrFail($id);
        return view('admin.home.testimonial.edit', $data);
    }

    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
            'comment' => 'required',
            'name' => 'required|max:50',
            'rank' => 'required|max:50',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $testimonial = new Testimonial;
        $testimonial->language_id = $request->language_id;
        $testimonial->comment = $request->comment;
        $testimonial->name = $request->name;
        $testimonial->rank = $request->rank;
        $testimonial->image = $request->testimonial_image;
        $testimonial->serial_number = $request->serial_number;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/testimonial/';
            $filename = CommonHelper::upload_file($image, $folderPath, '');
            if(!empty($filename)) $testimonial->image = $filename; 
        }

        $testimonial->save();

        Session::flash('success', 'Testimonial added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'comment' => 'required',
            'name' => 'required|max:50',
            'rank' => 'required|max:50',
            'serial_number' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $testimonial = Testimonial::findOrFail($request->testimonial_id);
        $testimonial->comment = $request->comment;
        $testimonial->name = $request->name;
        $testimonial->rank = $request->rank;
        $testimonial->serial_number = $request->serial_number;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/testimonial/';
            $filename = CommonHelper::upload_file($image, $folderPath,  $testimonial->image);
            if(!empty($filename)) {
                $testimonial->image = $filename;
            } 
        }
        $testimonial->save();

        Session::flash('success', 'Testimonial updated successfully!');
        return "success";
    }

    public function textupdate(Request $request, $langid)
    {
        $request->validate([
            'testimonial_section_title' => 'required|max:25',
            'testimonial_section_subtitle' => 'required|max:80',
        ]);

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $bs->testimonial_title = $request->testimonial_section_title;
        $bs->testimonial_subtitle = $request->testimonial_section_subtitle;
        $bs->save();

        Session::flash('success', 'Text updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $testimonial = Testimonial::findOrFail($request->testimonial_id);
        @unlink('cms/testimonial/' . $testimonial->image);
        $testimonial->delete();

        Session::flash('success', 'Testimonial deleted successfully!');

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back();
    }
}
