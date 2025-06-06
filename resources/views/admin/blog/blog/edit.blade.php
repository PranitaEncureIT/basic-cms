@extends('admin.layout')

@if(!empty($blog->language) && $blog->language->rtl == 1)
@section('styles')
<style>
    form input,
    form textarea,
    form select {
        direction: rtl;
    }
    form .note-editor.note-frame .note-editing-area .note-editable {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
  <div class="page-header">
    <h4 class="page-title">Edit Blog</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Blog Page</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Edit Blog</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">Edit Blog</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.blog.index') . '?language=' . request()->input('language')}}">
            <span class="btn-label">
              <i class="fas fa-backward" style="font-size: 12px;"></i>
            </span>
            Back
          </a>
        </div>
        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">

              <form id="ajaxEditForm" class="" action="{{route('admin.blog.update')}}" method="post">
                @csrf
                <input type="hidden" name="blog_id" value="{{$blog->id}}">

                {{-- Image Part --}}
                <div class="form-group">
                    <label for="">Image ** </label>
                    <br>
                    @php
                      $imagePath = public_path('cms/blogs/' . $blog->main_image); 
                    @endphp
                    <div class="thumb-preview" id="thumbPreview1">
                      @if (!empty($blog->main_image) && file_exists(public_path('cms/blogs/' . $blog->main_image)))
                      <img src="{{ asset('cms/blogs/' . $blog->main_image) }}" alt="Image" style="width:100px;">
                  @else
                      <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="No Image" style="width:100px;">
                  @endif
                    </div>
                    <br>
                    <br>

                    <input id="fileInput1" type="file" name="image" accept="image/*"
                    style="display: none;">
                    <!-- Label acting as the button triggers the file input -->
                    <label for="fileInput1" class="choose-image btn btn-primary">Choose Image</label>

                    <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                    <p class="em text-danger mb-0" id="errimage"></p>

                </div>
                <div class="form-group">
                  <label for="">Title **</label>
                  <input type="text" class="form-control" name="title" value="{{$blog->title}}" placeholder="Enter title">
                  <p id="errtitle" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Category **</label>
                  <select class="form-control" name="category">
                    <option value="" selected disabled>Select a category</option>
                    @foreach ($bcats as $key => $bcat)
                      <option value="{{$bcat->id}}" {{$bcat->id == $blog->bcategory->id ? 'selected' : ''}}>{{$bcat->name}}</option>
                    @endforeach
                  </select>
                  <p id="errcategory" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Content **</label>
                  <textarea id="blogContent" class="form-control summernote" name="content" data-height="300" placeholder="Enter content">{{replaceBaseUrl($blog->content)}}</textarea>
                  <p id="errcontent" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Serial Number **</label>
                  <input type="number" class="form-control ltr" name="serial_number" value="{{$blog->serial_number}}" placeholder="Enter Serial Number">
                  <p id="errserial_number" class="mb-0 text-danger em"></p>
                  <p class="text-warning"><small>The higher the serial number is, the later the blog will be shown.</small></p>
                </div>
                <div class="form-group">
                  <label for="">Meta Keywords</label>
                  <input type="text" class="form-control" name="meta_keywords" value="{{$blog->meta_keywords}}" data-role="tagsinput">
                  <p id="errmeta_keywords" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Meta Description</label>
                  <textarea type="text" class="form-control" name="meta_description" rows="5">{{$blog->meta_description}}</textarea>
                  <p id="errmeta_description" class="mb-0 text-danger em"></p>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="form">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" id="updateBtn" class="btn btn-success">Update</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
    <!-- JavaScript for image preview -->
    <script>
      document.getElementById('fileInput1').addEventListener('change', function(event) {
          const input = event.target;
          if (input.files && input.files[0]) {
              const reader = new FileReader();
              reader.onload = function(e) {
                  document.getElementById('thumbPreview1').innerHTML =
                      '<img src="' + e.target.result + '" alt="Image">';
              }
              reader.readAsDataURL(input.files[0]);
          }
      });
  </script>
@endsection
