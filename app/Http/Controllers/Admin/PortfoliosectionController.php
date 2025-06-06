<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BasicSetting as BS;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class PortfoliosectionController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = $request->language ?? 'en';
        $lang = Language::where('code', $lang_code)->first();

        if ($lang) {
            $data['lang_id'] = $lang->id;
            $data['abs'] = $lang->basic_setting;
        } else {
            $data['lang_id'] = 0;
            $data['abs'] = BS::firstOrFail();
        }
        return view('admin.home.portfolio-section', $data);
    }

    public function update(Request $request, $langid)
    {
        $rules = [
            'portfolio_section_text' => 'required|max:80',
            'portfolio_section_title' => 'required|max:25'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $bs->portfolio_section_text = $request->portfolio_section_text;
        $bs->portfolio_section_title = $request->portfolio_section_title;
        $bs->save();

        Session::flash('success', 'Texts updated successfully!');
        return "success";
    }
}
