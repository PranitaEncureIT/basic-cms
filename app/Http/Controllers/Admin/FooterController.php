<?php

namespace App\Http\Controllers\Admin;

use App\Helper\CommonHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BasicSetting as BS;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use Session;

class FooterController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;

        return view('admin.footer.logo-text', $data);
    }


    public function update(Request $request, $langid)
    {
        $rules = [
            'footer_text' => 'required',
            'newsletter_text' => 'required|max:255',
            'copyright_text' => 'required',
            'footer_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $bs->footer_text = $request->footer_text;
        $bs->newsletter_text = $request->newsletter_text;
        $bs->copyright_text = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->copyright_text);

        if ($request->hasFile('footer_logo') && $request->file('footer_logo')->isValid()) {
            $image = $request->file('footer_logo');
            $folderPath = 'cms/footer/';
            $filename = CommonHelper::upload_file($image, $folderPath,  $bs->footer_logo);
            if(!empty($filename)) {
                $bs->footer_logo = $filename;
            } 
        }
        $bs->save();

        Session::flash('success', 'Footer text updated successfully!');
        return "success";
    }
}
