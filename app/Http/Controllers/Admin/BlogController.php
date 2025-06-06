<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Bcategory;
use App\Models\Language;
use App\Models\Blog;
use App\Models\Megamenu;
use App\Helper\CommonHelper;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
		$lang = Language::where('code', $lang_code)->first();
        $lang_id = $lang->id;
        $data['lang_id'] = $lang_id;
        $data['blogs'] = Blog::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();
        $data['bcats'] = Bcategory::where('language_id', $lang_id)->where('status', 1)->get();

        return view('admin.blog.blog.index', $data);
    }

    public function edit($id)
    {
        $data['blog'] = Blog::findOrFail($id);
        $data['bcats'] = Bcategory::where('language_id', $data['blog']->language_id)->where('status', 1)->get();
        return view('admin.blog.blog.edit', $data);
    }

    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $slug = make_slug($request->title);

        $rules = [
            'language_id' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug) {
                    $blogs = Blog::all();
                    foreach ($blogs as $key => $blog) {
                        if (strtolower($slug) == strtolower($blog->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'category' => 'required',
            'content' => 'required',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $blog = new Blog;
        $blog->language_id = $request->language_id;
        $blog->title = $request->title;
        $blog->slug = $slug;
        $blog->bcategory_id = $request->category;
        $blog->content = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);
        $blog->meta_keywords = $request->meta_keywords;
        $blog->meta_description = $request->meta_description;
        $blog->serial_number = $request->serial_number;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/blogs/';
            $filename = CommonHelper::upload_file($image, $folderPath, '');
            if(!empty($filename)) $blog->main_image = $filename; 
        }

        $blog->save();

        Session::flash('success', 'Blog added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $slug = make_slug($request->title);
        $blog = Blog::findOrFail($request->blog_id);
        $blogId = $request->blog_id;


        $rules = [
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug, $blogId) {
                    $blogs = Blog::all();
                    foreach ($blogs as $key => $blog) {
                        if ($blog->id != $blogId && strtolower($slug) == strtolower($blog->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'category' => 'required',
            'content' => 'required',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $blog = Blog::findOrFail($request->blog_id);
        $blog->title = $request->title;
        $blog->slug = $slug;
        $blog->bcategory_id = $request->category;
        $blog->content = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);
        $blog->meta_keywords = $request->meta_keywords;
        $blog->meta_description = $request->meta_description;
        $blog->serial_number = $request->serial_number;


        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folderPath = 'cms/blogs/';
            $filename = CommonHelper::upload_file($image, $folderPath,  $blog->main_image);
            if(!empty($filename)) {
                $blog->main_image = $filename;
            } 
        }

        $blog->save();

        Session::flash('success', 'Blog updated successfully!');
        return "success";
    }

    public function deleteFromMegaMenu($blog) {
        // unset service from megamenu for service_category = 1
        $megamenu = Megamenu::where('language_id', $blog->language_id)->where('category', 1)->where('type', 'blogs');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            $catId = $blog->bcategory->id;
            if (is_array($menus) && array_key_exists("$catId", $menus)) {
                if (in_array($blog->id, $menus["$catId"])) {
                    $index = array_search($blog->id, $menus["$catId"]);
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

    public function delete(Request $request)
    {

        $blog = Blog::findOrFail($request->blog_id);
        @unlink('cms/blogs/' . $blog->main_image);

        $this->deleteFromMegaMenu($blog);

        $blog->delete();

        Session::flash('success', 'Blog deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $blog = Blog::findOrFail($id);
            @unlink('assets/front/img/blogs/' . $blog->main_image);

            $this->deleteFromMegaMenu($blog);

            $blog->delete();
        }

        Session::flash('success', 'Blogs deleted successfully!');
        return "success";
    }

    public function getcats($langid)
    {
        $bcategories = Bcategory::where('language_id', $langid)->get();

        return $bcategories;
    }

    public function sidebar(Request $request)
    {
        $blog = Blog::find($request->blog_id);
        $blog->sidebar = $request->sidebar;
        $blog->save();

        if ($request->sidebar == 1) {
            Session::flash('success', 'Enabled successfully!');
        } else {
            Session::flash('success', 'Disabled successfully!');
        }

        return back();
    }
}
