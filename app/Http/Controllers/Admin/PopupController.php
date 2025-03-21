<?php

namespace App\Http\Controllers\Admin;

use App\Helper\CommonHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Popup;
use Illuminate\Support\Facades\Validator;
use Session;

class PopupController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();
        $lang_id = $lang->id;
        $data['popups'] = Popup::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();
        return view('admin.popups.index', $data);
    }

    public function types()
    {
        return view('admin.popups.types');
    }

    public function create()
    {
        $data['langs'] = Language::all();
        return view('admin.popups.create', $data);
    }

    public function edit($id)
    {
        $data['popup'] = Popup::findOrFail($id);
        return view('admin.popups.edit', $data);
    }

    public function store(Request $request)
    {
        $type = $request->type;

        $messages = [
            'language_id.required' => 'The language field is required',
            // 'image.required' => 'An image is required for this popup type.',
            // 'background_image.required' => 'A background image is required for this popup type.',
            // 'image.mimes' => 'Only JPG, JPEG, PNG, SVG, and WEBP files are allowed.',
            // 'background_image.mimes' => 'Only JPG, JPEG, PNG, SVG, and WEBP files are allowed.',
        ];
    
        // Default Rules
        $rules = [
            'name' => 'required',
            'language_id' => 'required',
            'serial_number' => 'required|integer',
            'delay' => 'required|integer',
        ];
    
        // Conditionally apply image validation based on type
       
        if (in_array($type, [1, 4, 5, 7])) {
            $rules['image'] = 'required|image|mimes:jpg,jpeg,png,svg,webp|max:2048';
        }
    
        if (in_array($type, [2, 3, 6])) {
            $rules['background_image'] = 'required|image|mimes:jpg,jpeg,png,svg,webp|max:2048';
        }
    
        if (in_array($type, [2, 3, 4, 5, 6, 7])) {
            $rules['title'] = 'nullable';
            $rules['text'] = 'nullable';
        }
    
        if (in_array($type, [2, 3])) {
            $rules['background_color'] = 'required';
            $rules['background_opacity'] = 'required|numeric|max:1|min:0';
        }
    
        if ($type == 7) {
            $rules['background_color'] = 'required';
        }
    
        if (in_array($type, [6, 7])) {
            $rules['end_date'] = 'required';
            $rules['end_time'] = 'required';
        }
    
        if (in_array($type, [2, 3, 4, 5, 6, 7])) {
            $rules['button_text'] = 'nullable';
            $rules['button_color'] = 'nullable';
        }
    
        if (in_array($type, [2, 4, 6, 7])) {
            $rules['button_url'] = 'nullable';
        }
    
        // Perform Validation
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
    
        // Save Data
        $popup = new Popup;
        $popup->name = $request->name;
        $popup->language_id = $request->language_id;
        $popup->serial_number = $request->serial_number;
        $popup->delay = $request->delay;
        $popup->type = $type;
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = CommonHelper::upload_file($image, 'cms/popups/');
            $popup->image = $filename ?: null;
        }
    
        if ($request->hasFile('background_image')) {
            $bgImage = $request->file('background_image');
            $bgFilename = CommonHelper::upload_file($bgImage, 'cms/popups/');
            $popup->background_image = $bgFilename ?: null;
        }
    
        if (in_array($type, [2, 3])) {
            $popup->background_color = $request->background_color;
            $popup->background_opacity = $request->background_opacity;
        }
    
        if ($type == 7) {
            $popup->background_color = $request->background_color;
        }
    
        if (in_array($type, [2, 3, 4, 5, 6, 7])) {
            $popup->button_text = $request->button_text;
            $popup->button_color = $request->button_color;
        }
    
        if (in_array($type, [2, 4, 6, 7])) {
            $popup->button_url = $request->button_url;
        }
    
        if (in_array($type, [2, 3, 4, 5, 6, 7])) {
            $popup->title = $request->title;
            $popup->text = $request->text;
        }
    
        if (in_array($type, [6, 7])) {
            $popup->end_date = $request->end_date;
            $popup->end_time = $request->end_time;
        }
    
        $popup->save();

        Session::flash('success', 'Popup added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $type = $request->type;

        $rules = [
            'name' => 'required',
            'serial_number' => 'required|integer',
            'delay' => 'required|integer',
            // 'image' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
            // 'background_image' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048'
        ];

        // if ($type == 1 || $type == 4 || $type == 5 || $type == 7) {
        //     if ($request->filled('image')) {
        //         $image = $request->image;
        //         $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        //         $extImage = pathinfo($image, PATHINFO_EXTENSION);

        //         $rules['image'] = [
        //             function ($attribute, $value, $fail) use ($extImage, $allowedExts) {
        //                 if (!in_array($extImage, $allowedExts)) {
        //                     return $fail("Only png, jpg, jpeg, svg image is allowed");
        //                 }
        //             }
        //         ];
        //     }
        // }

        // if ($type == 2 || $type == 3 || $type == 6) {
        //     if ($request->filled('background_image')) {
        //         $background = $request->background_image;
        //         $allowedBgExts = array('jpg', 'png', 'jpeg', 'svg');
        //         $extBackground = pathinfo($background, PATHINFO_EXTENSION);

        //         $rules['background_image'] = [
        //             function ($attribute, $value, $fail) use ($extBackground, $allowedBgExts) {
        //                 if (!in_array($extBackground, $allowedBgExts)) {
        //                     return $fail("Only png, jpg, jpeg, svg image is allowed");
        //                 }
        //             }
        //         ];
        //     }
        // }

        if ($type == 2 || $type == 3 || $type == 4 || $type == 5 || $type == 6 || $type == 7) {
            $rules['title'] = 'nullable';
            $rules['text'] = 'nullable';
        }

        if ($type == 2 || $type == 3) {
            $rules['background_color'] = 'required';
            $rules['background_opacity'] = 'required|numeric|max:1|min:0';
        }

        if ($type == 7) {
            $rules['background_color'] = 'required';
        }

        if ($type == 6 || $type == 7) {
            $rules['end_date'] = 'required';
            $rules['end_time'] = 'required';
        }

        if ($type == 2 || $type == 3 || $type == 4 || $type == 5 || $type == 6 || $type == 7) {
            $rules['button_text'] = 'nullable';
            $rules['button_color'] = 'nullable';
        }

        if ($type == 2 || $type == 4 || $type == 6 || $type == 7) {
            $rules['button_url'] = 'nullable';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $popup = Popup::findOrFail($request->popup_id);
        $popup->name = $request->name;
        $popup->serial_number = $request->serial_number;
        $popup->delay = $request->delay;

        if ($type == 1 || $type == 4 || $type == 5 || $type == 7) {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                $folderPath = 'cms/popups/';
                $filename = CommonHelper::upload_file($image, $folderPath,  $popup->image);
                if (!empty($filename)) {
                    $popup->image = $filename;
                }
            }
        }

        if ($type == 2 || $type == 3 || $type == 6) {
            if ($request->hasFile('background_image') && $request->file('background_image')->isValid()) {
                $image = $request->file('background_image');
                $folderPath = 'cms/popups/';
                $filename = CommonHelper::upload_file($image, $folderPath,  $popup->background_image);
                if (!empty($filename)) {
                    $popup->background_image = $filename;
                }
            }
        }

        if ($type == 2 || $type == 3) {
            $popup->background_color = $request->background_color;
            $popup->background_opacity = $request->background_opacity;
        }

        if ($type == 7) {
            $popup->background_color = $request->background_color;
        }

        if ($type == 2 || $type == 3 || $type == 4 || $type == 5 || $type == 6 || $type == 7) {
            $popup->button_text = $request->button_text;
            $popup->button_color = $request->button_color;
        }

        if ($type == 2 || $type == 4 || $type == 6 || $type == 7) {
            $popup->button_url = $request->button_url;
        }

        if ($type == 2 || $type == 3 || $type == 4 || $type == 5 || $type == 6 || $type == 7) {
            $popup->title = $request->title;
            $popup->text = $request->text;
        }

        if ($type == 6 || $type == 7) {
            $popup->end_date = $request->end_date;
            $popup->end_time = $request->end_time;
        }

        $popup->save();

        Session::flash('success', 'Popup updated successfully!');
        return "success";
    }


    public function delete(Request $request)
    {

        $popup = Popup::findOrFail($request->popup_id);
        @unlink('cms/popups/' . $popup->image);
        @unlink('cms/popups/' . $popup->background_image);
        $popup->delete();

        Session::flash('success', 'Popup deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $popup = Popup::findOrFail($id);
            @unlink('cms/popups/' . $popup->image);
            @unlink('cms/popups/' . $popup->background_image);
            $popup->delete();
        }

        Session::flash('success', 'Popups deleted successfully!');
        return "success";
    }

    public function status(Request $request)
    {

        $po = Popup::find($request->popup_id);
        $po->status = $request->status;
        $po->save();

        Session::flash('success', 'Status changed!');
        return back();
    }
}
