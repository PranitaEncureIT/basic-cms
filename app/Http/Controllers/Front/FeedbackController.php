<?php

namespace App\Http\Controllers\Front;

use App\Models\Feedback;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class FeedbackController extends Controller
{
  public function feedback()
  {
    if (session()->has('lang')) {
      $currentLang = Language::where('code', session()->get('lang'))->first();
    } else {
      $currentLang = Language::where('is_default', 1)->first();
    }

    $data['bse'] = $currentLang->basic_extra;
    $data['currentLang'] = $currentLang;

    $be = $currentLang->basic_extended;
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }

    $data['version'] = $version;

    return view('front.client_feedback', $data);
  }

  public function storeFeedback(StoreFeedbackRequest $request)
  {
    Feedback::create($request->all());

    Session::flash('success', 'Your feedback has submitted.');

    return redirect()->back();
  }
}
