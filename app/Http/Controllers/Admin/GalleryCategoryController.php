<?php

namespace App\Http\Controllers\Admin;

use App\Models\BasicExtra;
use App\Models\GalleryCategory;
use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GalleryCategoryController extends Controller
{
  public function settings()
  {
    $data['abex'] = BasicExtra::first();

    return view('admin.gallery.settings', $data);
  }

  public function updateSettings(Request $request)
  {
    $bexs = BasicExtra::all();

    foreach ($bexs as $bex) {
      $bex->update([
        'gallery_category_status' => $request->gallery_category_status
      ]);
    }

    Session::flash('success', 'Settings updated successfully.');

    return redirect()->back();
  }


  public function index(Request $request)
  {
    $lang_code = isset($request->language) ?  $request->language : 'en';
    $lang = Language::where('code', $lang_code)->first();

    $categories = GalleryCategory::where('language_id', $lang->id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    return view('admin.gallery.categories', compact('categories'));
  }

  public function store(Request $request)
  {
    $rules = [
      'language_id' => 'required',
      'name' => 'required',
      'status' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');

      return response()->json($validator->errors());
    }

    // Generate a slug from the name
    $slug = Str::slug($request->name);

    // Check if the slug already exists and make it unique
    $count = GalleryCategory::where('slug', $slug)->count();
    if ($count > 0) {
      $slug .= '-' . ($count + 1);
    }

    // Create the record with slug
    GalleryCategory::create(array_merge($request->all(), ['slug' => $slug]));

    Session::flash('success', 'New gallery category added successfully.');

    return 'success';
  }

  public function update(Request $request)
  {
    $rules = [
      'name' => 'required',
      'status' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');

      return response()->json($validator->errors());
    }

    GalleryCategory::findOrFail($request->categoryId)->update($request->all());

    Session::flash('success', 'Gallery category updated successfully.');

    return 'success';
  }

  public function delete(Request $request)
  {
    $category = GalleryCategory::findOrFail($request->categoryId);

    if ($category->galleryImg->count() > 0) {
      Session::flash('warning', 'First delete all the images of this category');

      return redirect()->back();
    }

    $category->delete();

    Session::flash('success', 'Gallery category deleted successfully.');

    return redirect()->back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $category = GalleryCategory::findOrFail($id);

      if ($category->galleryImg->count() > 0) {
        Session::flash('warning', 'First delete all the images of those categories');

        return 'success';
      }
    }

    foreach ($ids as $id) {
      $category = GalleryCategory::findOrFail($id);

      $category->delete();
    }

    Session::flash('success', 'Gallery categories deleted successfully.');

    return 'success';
  }
}
