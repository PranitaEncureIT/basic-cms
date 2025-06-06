@extends('admin.layout')

@if(!empty($gallery->language) && $gallery->language->rtl == 1)
@section('styles')
<style>
  form input,
  form textarea,
  form select {
    direction: rtl;
  }

  .nicEdit-main {
    direction: rtl;
    text-align: right;
  }
</style>
@endsection
@endif

@section('content')
<div class="page-header">
  <h4 class="page-title">Edit Gallery</h4>
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
      <a href="#">Gallery Page</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Edit Gallery</a>
    </li>
  </ul>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title d-inline-block">Edit Gallery</div>
        <a class="btn btn-info btn-sm float-right d-inline-block"
          href="{{route('admin.gallery.index') . '?language=' . request()->input('language')}}">
          <span class="btn-label">
            <i class="fas fa-backward" style="font-size: 12px;"></i>
          </span>
          Back
        </a>
      </div>
      <div class="card-body pt-5 pb-5">
        <div class="row">
          <div class="col-lg-6 offset-lg-3">
            <form id="ajaxForm" class="" action="{{route('admin.gallery.update')}}" method="post">
              @csrf
              <input type="hidden" name="gallery_id" value="{{$gallery->id}}">

              {{-- Image Part --}}
              <div class="form-group">
                <label for="">Image ** </label>
                <br>
                @php
                  $imagePath = public_path('cms/gallery/'.$gallery->image);
                @endphp
                <div class="thumb-preview" id="thumbPreview1">
                  @if (!empty($gallery->image) && file_exists(public_path('cms/gallery/' . $gallery->image)))
                    <img src="{{ asset('cms/gallery/'.$gallery->image) }}" alt="User Image">
                  @else
                    <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="Image">
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

              <div class="form-group {{ $categoryInfo->gallery_category_status == 0 ? 'd-none' : '' }}">
                <label for="">Category **</label>
                <select name="category_id" class="form-control">
                  <option disabled selected>Select a category</option>
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id == $gallery->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                  @endforeach
                </select>
                <p id="eerrcategory_id" class="mb-0 text-danger em"></p>
              </div>
              <div class="form-group">
                <label for="">Title **</label>
                <input type="text" class="form-control" name="title" value="{{$gallery->title}}" placeholder="Enter title">
                <p id="errtitle" class="mb-0 text-danger em"></p>
              </div>
              <div class="form-group">
                <label for="">Serial Number **</label>
                <input type="number" class="form-control ltr" name="serial_number" value="{{$gallery->serial_number}}"
                  placeholder="Enter Serial Number">
                <p id="errserial_number" class="mb-0 text-danger em"></p>
                <p class="text-warning"><small>The higher the serial number is, the later the image will be
                    shown.</small></p>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <div class="form">
          <div class="form-group from-show-notify row">
            <div class="col-12 text-center">
              <button type="submit" id="submitBtn" class="btn btn-success">Update</button>
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
