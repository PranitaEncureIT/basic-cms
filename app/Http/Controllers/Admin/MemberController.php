<?php

namespace App\Http\Controllers\Admin;

use App\Helper\CommonHelper;
use App\Models\BasicExtended;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Language;
use App\Models\BasicSetting as BS;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['members'] = Member::where('language_id', $data['lang_id'])->get();

        if ($request->ajax()) {
            $draw = intval($request->input('draw'));
            $start = intval($request->input('start'));
            $length = intval($request->input('length'));
            $search = $request->input('search')['value'] ?? null;
    
            $query = Member::where('language_id', $data['lang_id']);
    
            // Apply search filter
            if (!empty($search)) {
                $query->orwhere('name', 'LIKE', "%$search%")
                ->orwhere('rank', 'LIKE', "%$search%");

            }
    
            // Get total count before applying pagination
            $recordsTotal = $query->count();
    
            // Apply ordering, pagination, and fetch data
            $members = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();
            $languageParam = request()->input('language');
    
            // Modify each point for DataTables response
            foreach ($members as $key => $member) {
                $member->sr_no = $key + 1 + $start;

                $imagePath = public_path('cms/memebers/' . $member->image);
                if (!empty($member->image) && file_exists($imagePath)) {
                    $imageUrl = asset('cms/memebers/' . $member->image);
                } else {
                    $imageUrl = asset('assets/front/img/no_image.jpg');
                }
    
                // Ensure image is properly formatted
                $member->image = "<div class='d-flex flex-column'>
                <img src='" . e($imageUrl) . "' alt='member Image' style='width:100px;'>
           </div>";
    
                $member->name = "<div class='d-flex flex-column'>" . e(convertUtf8($member->name)) . "</div>";
                $member->rank = "<div class='d-flex flex-column'>" . e($member->rank) . "</div>";
                $memberFeature = $member->feature == 1 ? 'bg-success' : 'bg-danger';
                $member->feature = "<form id='memberForm{$member->id}' action='" . route('admin.member.feature') . "' method='post'>
                                    " . csrf_field() . "
                                    <select class='form-control form-control-sm $memberFeature' 
                                        name='feature' onchange='document.getElementById(\"memberForm{$member->id}\").submit();'>
                                        <option value='1' " . ($member->feature == 1 ? 'selected' : '') . ">Yes</option>
                                        <option value='0' " . ($member->feature == 0 ? 'selected' : '') . ">No</option>
                                    </select>
                                    <input type='hidden' name='user_id' value='{$member->id}'>
                                 </form>";
    
                // Action buttons
                $editUrl = route('admin.member.edit', $member->id) . '?language=' . e($languageParam);
    
                $member->action = "<div class='d-flex align-items-center gap-2'>
                    <a href='" . $editUrl . "' class='btn btn-sm btn-secondary' title='Edit'>
                        <i class='fas fa-pencil-alt'></i>
                    </a>
                    <button type='button' class='btn btn-sm btn-danger deletebutton' data-id='" . $member->id . "' title='Delete'>
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </div>";
            }
    
            // Prepare DataTables response
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data' => $members->toArray(),
            ]);
        }


        return view('admin.home.member.index', $data);
    }

    public function create()
    {
        return view('admin.home.member.create');
    }

    public function edit($id)
    {
        $data['member'] = Member::findOrFail($id);
        return view('admin.home.member.edit', $data);
    }

    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
            'name' => 'required|max:50',
            'rank' => 'required|max:50',
            'facebook' => 'nullable|max:50',
            'twitter' => 'nullable|max:50',
            'linkedin' => 'nullable|max:50',
            'instagram' => 'nullable|max:50',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $member = new Member;
        $member->language_id = $request->language_id;
        $member->image = $request->member_image;
        $member->name = $request->name;
        $member->rank = $request->rank;
        $member->facebook = $request->facebook;
        $member->twitter = $request->twitter;
        $member->linkedin = $request->linkedin;
        $member->instagram = $request->instagram;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/memebers/';
            $filename = CommonHelper::upload_file($image, $folderPath, '');
            if(!empty($filename)) $member->image = $filename; 
        }

        $member->save();

        Session::flash('success', 'Member added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|max:50',
            'rank' => 'required|max:50',
            'facebook' => 'nullable|max:50',
            'twitter' => 'nullable|max:50',
            'linkedin' => 'nullable|max:50',
            'instagram' => 'nullable|max:50',
            
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $member = Member::findOrFail($request->member_id);
        $member->name = $request->name;
        $member->rank = $request->rank;
        $member->facebook = $request->facebook;
        $member->twitter = $request->twitter;
        $member->linkedin = $request->linkedin;
        $member->instagram = $request->instagram;
        
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/memebers/';
            $filename = CommonHelper::upload_file($image, $folderPath,  $member->image);
            if(!empty($filename)) {
                $member->image = $filename;
            } 
        }

        $member->save();

        Session::flash('success', 'Member updated successfully!');
        return "success";
    }

    public function textupdate(Request $request, $langid)
    {
        $be = BasicExtended::firstOrFail();
        $version = $be->theme_version;

        if ($version == 'default' || $version == 'dark') {
            $background = $request->background;
            $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
            $extBackground = pathinfo($background, PATHINFO_EXTENSION);
        }

        $rules = [
            'team_section_title' => 'required|max:25',
            'team_section_subtitle' => 'required|max:80',
        ];

        if (($version == 'default' || $version == 'dark') && $request->filled('background')) {
            $rules['background'] = [
                function ($attribute, $value, $fail) use ($extBackground, $allowedExts) {
                    if (!in_array($extBackground, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                }
            ];
        }

        $request->validate($rules);

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $bs->team_section_title = $request->team_section_title;
        $bs->team_section_subtitle = $request->team_section_subtitle;

        if (($version == 'default' || $version == 'dark') && $request->filled('background')) {
            @unlink('assets/front/img/'.$bs->team_bg);
            $filename = uniqid() .'.'. $extBackground;
            @copy($background, 'assets/front/img/' . $filename);
            $bs->team_bg = $filename;
        }

        $bs->save();

        Session::flash('success', 'Text & Background updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $member = Member::findOrFail($request->member_id);
        @unlink('cms/memebers/' . $member->image);
        $member->delete();

        Session::flash('success', 'Member deleted successfully!');

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back();
    }

    public function feature(Request $request)
    {
        $member = Member::find($request->member_id);
        $member->feature = $request->feature;
        $member->save();

        if ($request->feature == 1) {
            Session::flash('success', 'Featured successfully!');
        } else {
            Session::flash('success', 'Unfeatured successfully!');
        }

        return back();
    }
}
