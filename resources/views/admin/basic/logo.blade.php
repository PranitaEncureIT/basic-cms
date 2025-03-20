@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">Logo</h4>
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
            <a href="#">Basic Settings</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Logo</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-title">Update Logo</div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-5 pb-4">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <form id="imageForm" action="{{route('admin.logo.update')}}" method="POST">
                            @csrf
                            <div class="form-group">
                  <label>Website Heading **</label>
                  <input class="form-control" name="website_heading" value="{{$abs->website_heading}}" placeholder="Enter Titlte">
                  @if ($errors->has('website_heading'))
                    <p class="mb-0 text-danger">{{$errors->first('website_heading')}}</p>
                  @endif
                </div>
                            {{-- Logo Part --}}
                            
                            <div class="form-group">
                                <label for="">Logo ** </label>
                                <br>
                                @php
                                    $imagePath = public_path('assets/front/img/' . $abs->logo);
                                @endphp
                                <div class="thumb-preview" id="thumbPreview1">
                                    @if (file_exists($imagePath) && !empty($abs->logo))
                                        <img src="{{ asset('assets/front/img/' . $abs->logo) }}"
                                                    alt="Feature Image" width="100">
                                    @else
                                        <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="Image" width="100">
                                    @endif
                                </div>
                                <br>
                                <br>

                                <input id="fileInput1" type="file" name="logo" accept="image/* "style="display:none;">
                                <label for="fileInput1" class="choose-image btn btn-primary">Choose Image</label>

                                <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                                @if ($errors->has('logo'))
                                <p class="text-danger mb-0">{{$errors->first('logo')}}</p>
                                @endif
                            </div>

                            {{-- Favicon Part --}}
                            <div class="form-group">
                                <label for="">Favicon ** </label>
                                <br>
                                @php
                                    $imagePath = public_path('assets/front/img/' . $abs->favicon);
                                @endphp
                                <div class="thumb-preview" id="thumbPreview2">
                                    @if (file_exists($imagePath) && !empty($abs->favicon))
                                        <img src="{{ asset('assets/front/img/' . $abs->favicon) }}"
                                                    alt="Feature Image" width="100">
                                    @else
                                        <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="Image" width="100">
                                    @endif

                                </div>
                                <br>
                                <br>

                                <input id="fileInput2" type="file" name="favicon" accept="image/* "style="display:none;">
                                <label for="fileInput2" class="choose-image btn btn-primary">Choose Image</label>


                                <p class="text-warning mb-0">JPG, PNG, JPEG, SVG, SVG, SVG images are allowed</p>
                                @if ($errors->has('favicon'))
                                <p class="text-danger mb-0">{{$errors->first('favicon')}}</p>
                                @endif
                            </div>

                            {{-- Breadcrumb Part --}}
                            <div class="form-group">
                                <label for="">Breadcrumb ** </label>
                                <br>
                                @php
                                    $imagePath = public_path('assets/front/img/' . $abs->breadcrumb);
                                @endphp
                                <div class="thumb-preview" id="thumbPreview3">

                                    @if (file_exists($imagePath) && !empty($abs->breadcrumb))
                                        <img src="{{ asset('assets/front/img/' . $abs->breadcrumb) }}"
                                                    alt="Feature Image" width="100">
                                    @else
                                        <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="Image" width="100">
                                    @endif
                                </div>
                                <br>
                                <br>

                                <input id="fileInput3" type="file" name="breadcrumb" accept="image/* "style="display:none;">
                                <label for="fileInput3" class="choose-image btn btn-primary">Choose Image</label>

                                <p class="text-warning mb-0">JPG, PNG, JPEG, SVG, SVG images are allowed</p>
                                @if ($errors->has('breadcrumb'))
                                <p class="text-danger mb-0">{{$errors->first('breadcrumb')}}</p>
                                @endif
                            </div>
                        </form>

                    </div>

                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-success" form="imageForm">Update</button>
            </div>
        </div>
    </div>
</div>
<script>
    function previewImage(inputId, previewId) {
        document.getElementById(inputId).addEventListener('change', function (event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewContainer = document.getElementById(previewId);
                    const imgTag = previewContainer.querySelector("img");
                    if (imgTag) {
                        imgTag.src = e.target.result;
                    } else {
                        const newImg = document.createElement("img");
                        newImg.src = e.target.result;
                        newImg.alt = "Image";
                        previewContainer.appendChild(newImg);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    }
  
    // Initialize preview for three inputs
    previewImage('fileInput1', 'thumbPreview1');
    previewImage('fileInput2', 'thumbPreview2');
    previewImage('fileInput3', 'thumbPreview3');
  </script>
  


@endsection

