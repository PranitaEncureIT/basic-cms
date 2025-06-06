<?php

namespace App\Http\Controllers\Admin;

use App\Helper\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Megamenu;
use App\Models\Portfolio;
use App\Models\PortfolioImage;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
  public function index(Request $request)
  {
    $lang_code = isset($request->language) ?  $request->language : 'en';
    $lang = Language::where('code', $lang_code)->first();

    $lang_id = $lang->id;
    $data['portfolios'] = Portfolio::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();

    $data['lang_id'] = $lang_id;

    return view('admin.portfolio.index', $data);
  }

  public function create()
  {
    $data['services'] = Service::all();
    $data['tportfolios'] = Portfolio::where('language_id', 0)->get();
    return view('admin.portfolio.create', $data);
  }

  public function edit($id)
  {
    $data['portfolio'] = Portfolio::findOrFail($id);
    $data['services'] = Service::where('language_id', $data['portfolio']->language_id)->get();
    return view('admin.portfolio.edit', $data);
  }

  public function sliderrmv(Request $request)
  {
    $pi = PortfolioImage::findOrFail($request->fileid);
    @unlink('assets/front/img/portfolios/sliders/' . $pi->image);
    $pi->delete();
    return $pi->id;
  }


  public function store(Request $request)
  {
    $slug = make_slug($request->title);

    $sliders = !empty($request->slider) ? explode(',', $request->slider) : [];
    $image = $request->image;
    $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
    $extImage = pathinfo($image, PATHINFO_EXTENSION);

    $rules = [
      'slider' => 'required',
      'language_id' => 'required',
      'title' => [
        'required',
        'max:255',
        function ($attribute, $value, $fail) use ($slug) {
          $portfolios = Portfolio::all();
          foreach ($portfolios as $key => $portfolio) {
            if (strtolower($slug) == strtolower($portfolio->slug)) {
              $fail('The title field must be unique.');
            }
          }
        }
      ],
      'client_name' => 'required|max:255',
      'service_id' => 'required',
      'tags' => 'required',
      'content' => 'required',
      'image' => 'required',
      'status' => 'required',
      'serial_number' => 'required|integer',
    ];

    // Optionally, add custom validation for each slider file:
    if ($request->hasFile('slider')) {
      foreach ($request->file('slider') as $sliderFile) {
        $extSlider = strtolower($sliderFile->getClientOriginalExtension());
        if (!in_array($extSlider, $allowedExts)) {
          return response()->json(['slider' => 'Only png, jpg, jpeg images are allowed for sliders']);
        }
      }
    }

    if ($request->hasFile('image')) {
      $extImage = strtolower($request->file('image')->getClientOriginalExtension());
      if (!in_array($extImage, $allowedExts)) {
        return response()->json(['image' => 'Only png, jpg, jpeg, svg images are allowed for featured image']);
      }
    }

    $messages = [
      'language_id.required' => 'The language field is required',
      'service_id.required' => 'service is required'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $errmsgs = $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $in = $request->all();
    $in['language_id'] = $request->language_id;
    $in['slug'] = $slug;
    $in['content'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);

    if ($request->hasFile('image') && $request->file('image')->isValid()) {
      $image = $request->file('image');
      $folderPath = 'assets/front/img/portfolios/featured/';
      $filename = CommonHelper::upload_file($image, $folderPath, '');

      if (!empty($filename)) {
        $in['featured_image'] = $filename;
      }
    }


    $portfolio = Portfolio::create($in);

    // Upload slider images using CommonHelper
    if ($request->hasFile('slider')) {
      foreach ($request->file('slider') as $sliderFile) {
        if ($sliderFile->isValid()) {
          $folderPath = 'assets/front/img/portfolios/sliders/';
          $filename = CommonHelper::upload_file($sliderFile, $folderPath, '');
          if (!empty($filename)) {
            $pi = new PortfolioImage;
            $pi->portfolio_id = $portfolio->id;
            $pi->image = $filename;
            $pi->save();
          }
        }
      }
    }


    Session::flash('success', 'Portfolio added successfully!');
    return "success";
  }

  public function images($portid)
  {
    $images = PortfolioImage::select('image')->where('portfolio_id', $portid)->get();
    $convImages = [];

    foreach ($images as $key => $image) {
      $convImages[] = url("assets/front/img/portfolios/sliders/$image->image");
    }

    return $convImages;
  }

  public function update(Request $request)
  {
    $slug = make_slug($request->title);
    $portfolio = Portfolio::findOrFail($request->portfolio_id);
    $portfolioId = $request->portfolio_id;

    $sliders = !empty($request->slider) ? explode(',', $request->slider) : [];
    $image = $request->image;
    $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
    $extImage = pathinfo($image, PATHINFO_EXTENSION);

    $rules = [
      
      'title' => [
        'required',
        'max:255',
        function ($attribute, $value, $fail) use ($slug, $portfolioId) {
          $portfolios = Portfolio::all();
          foreach ($portfolios as $key => $portfolio) {
            if ($portfolio->id != $portfolioId && strtolower($slug) == strtolower($portfolio->slug)) {
              $fail('The title field must be unique.');
            }
          }
        }
      ],
      'client_name' => 'required|max:255',
      'service_id' => 'required',
      'tags' => 'required',
      'content' => 'required',
      'status' => 'required',
      'serial_number' => 'required|integer',
    ];

    if ($request->hasFile('image')) {
      $extImage = strtolower($request->file('image')->getClientOriginalExtension());
      if (!in_array($extImage, $allowedExts)) {
        return response()->json(['image' => 'Only png, jpg, jpeg, svg images are allowed']);
      }
      $rules['image'] = 'file';
    }

    if ($request->hasFile('slider')) {
      foreach ($request->file('slider') as $sliderFile) {
        $extSlider = strtolower($sliderFile->getClientOriginalExtension());
        if (!in_array($extSlider, $allowedExts)) {
          return response()->json(['slider' => 'Only png, jpg, jpeg images are allowed for sliders']);
        }
      }
    }

    $messages = [
      'service_id.required' => 'service is required'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $errmsgs = $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $in = $request->all();
    $portfolio = Portfolio::findOrFail($request->portfolio_id);
    $in['content'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);
    $in['slug'] = $slug;

    // If a new featured image is uploaded, delete the old file and upload the new one using CommonHelper
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
      $oldImagePath = public_path('assets/front/img/portfolios/featured/' . $portfolio->featured_image);
      if (!empty($portfolio->featured_image) && file_exists($oldImagePath)) {
        @unlink($oldImagePath);
      }
      $imageFile = $request->file('image');
      $folderPath = 'assets/front/img/portfolios/featured/';
      $filename = CommonHelper::upload_file($imageFile, $folderPath, '');
      if (!empty($filename)) {
        $in['featured_image'] = $filename;
      }
    }

    $portfolio->fill($in)->save();


    // Upload new slider images using CommonHelper
    $fileNames = [];
    if ($request->hasFile('slider')) {
        foreach ($request->file('slider') as $sliderFile) {
            if ($sliderFile->isValid()) {
                $folderPath = 'assets/front/img/portfolios/sliders/';
                $filename = CommonHelper::upload_file($sliderFile, $folderPath, '');
                if (!empty($filename)) {
                    $fileNames[] = $filename;
                }
            }
        }
    }

    // delete & unlink previous slider images
    $pis = PortfolioImage::where('portfolio_id', $portfolio->id)->get();
    foreach ($pis as $key => $pi) {
      @unlink('assets/front/img/portfolios/sliders/' . $pi->image);
      $pi->delete();
    }

    // store new slider images
    foreach ($fileNames as $key => $fileName) {
      $pi = new PortfolioImage;
      $pi->portfolio_id = $portfolio->id;
      $pi->image = $fileName;
      $pi->save();
    }

    Session::flash('success', 'Portfolio updated successfully!');
    return "success";
  }

  public function delete(Request $request)
  {
    $portfolio = Portfolio::findOrFail($request->portfolio_id);
    foreach ($portfolio->portfolio_images as $key => $pi) {
      @unlink('assets/front/img/portfolios/sliders/' . $pi->image);
      $pi->delete();
    }
    @unlink('assets/front/img/portfolios/featured/' . $portfolio->featured_image);

    $this->deleteFromMegaMenu($portfolio);

    $portfolio->delete();

    Session::flash('success', 'Portfolio deleted successfully!');
    return back();
  }

  public function deleteFromMegaMenu($portfolio)
  {
    // unset portfolio from megamenu for service_category = 1
    $megamenu = Megamenu::where('language_id', $portfolio->language_id)->where('category', 1)->where('type', 'portfolios');
    if ($megamenu->count() > 0) {
      $megamenu = $megamenu->first();
      $menus = json_decode($megamenu->menus, true);
      if (!empty($portfolio->service) && !empty($portfolio->service->scategory)) {
        $catId = $portfolio->service->scategory->id;
        if (is_array($menus) && array_key_exists("$catId", $menus)) {
          if (in_array($portfolio->id, $menus["$catId"])) {
            $index = array_search($portfolio->id, $menus["$catId"]);
            unset($menus["$catId"]["$index"]);
            $menus["$catId"] = array_values($menus["$catId"]);
            if (count($menus["$catId"]) == 0) {
              unset($menus["$catId"]);
            }
            $megamenu->menus = json_encode($menus);
            $megamenu->save();
          }
        }
      }
    }

    // unset portfolio from megamenu for service_category = 0
    $megamenu = Megamenu::where('language_id', $portfolio->language_id)->where('category', 0)->where('type', 'portfolios');
    if ($megamenu->count() > 0) {
      $megamenu = $megamenu->first();
      $menus = json_decode($megamenu->menus, true);
      if (is_array($menus)) {
        if (in_array($portfolio->id, $menus)) {
          $index = array_search($portfolio->id, $menus);
          unset($menus["$index"]);
          $menus = array_values($menus);
          $megamenu->menus = json_encode($menus);
          $megamenu->save();
        }
      }
    }
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $portfolio = Portfolio::findOrFail($id);
      foreach ($portfolio->portfolio_images as $key => $pi) {
        @unlink('assets/front/img/portfolios/sliders/' . $pi->image);
        $pi->delete();
      }
    }

    foreach ($ids as $id) {
      $portfolio = Portfolio::findOrFail($id);
      @unlink('assets/front/img/portfolios/featured/' . $portfolio->featured_image);

      $this->deleteFromMegaMenu($portfolio);

      $portfolio->delete();
    }

    Session::flash('success', 'Portfolios deleted successfully!');
    return "success";
  }

  public function getservices($langid)
  {
    $services = Service::where('language_id', $langid)->get();

    return $services;
  }

  public function feature(Request $request)
  {
    $portfolio = Portfolio::find($request->portfolio_id);
    $portfolio->feature = $request->feature;
    $portfolio->save();

    if ($request->feature == 1) {
      Session::flash('success', 'Featured successfully!');
    } else {
      Session::flash('success', 'Unfeatured successfully!');
    }

    return back();
  }
}
