@extends('admin.layout')

@if (!empty($member->language) && $member->language->rtl == 1)
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
        <h4 class="page-title">Edit Member</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="#">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Home Page</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Edit Member</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">Edit Member</div>
                    <a class="btn btn-info btn-sm float-right d-inline-block"
                        href="{{ route('admin.member.index') . '?language=' . request()->input('language') }}">
                        <span class="btn-label">
                            <i class="fas fa-backward" style="font-size: 12px;"></i>
                        </span>
                        Back
                    </a>
                </div>
                <div class="card-body pt-5 pb-5">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">

                            <form id="ajaxEditForm" class="" action="{{ route('admin.member.update') }}" method="post">
                                @csrf
                                <input type="hidden" name="member_id" value="{{ $member->id }}">

                                {{-- Image Part --}}
                                <div class="form-group">
                                    <label for="">Image ** </label>
                                    <br>
                                    @php
                                        $imagePath = public_path('cms/memebers/' . $member->image);
                                    @endphp
                                    <div class="thumb-preview" id="thumbPreview1">

                                        @if (!empty($member->image) && file_exists(public_path('cms/memebers/' . $member->image)))
                                            <img src="{{ asset('cms/memebers/' . $member->image) }}"
                                                alt="Image">
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

                                <div class="form-group">
                                    <label for="">Name **</label>
                                    <input type="text" class="form-control" name="name" value="{{ $member->name }}"
                                        placeholder="Enter name">
                                    <p id="errname" class="mb-0 text-danger em"></p>
                                </div>
                                <div class="form-group">
                                    <label for="">Rank **</label>
                                    <input type="text" class="form-control" name="rank" value="{{ $member->rank }}"
                                        placeholder="Enter rank">
                                    <p id="errrank" class="mb-0 text-danger em"></p>
                                </div>
                                <div class="form-group">
                                    <label for="">Facebook</label>
                                    <input type="text" class="form-control ltr" name="facebook"
                                        value="{{ $member->facebook }}" placeholder="Enter facebook url">
                                    <p id="errfacebook" class="mb-0 text-danger em"></p>
                                </div>
                                <div class="form-group">
                                    <label for="">Twitter</label>
                                    <input type="text" class="form-control ltr" name="twitter"
                                        value="{{ $member->twitter }}" placeholder="Enter twitter url">
                                    <p id="errtwitter" class="mb-0 text-danger em"></p>
                                </div>
                                <div class="form-group">
                                    <label for="">Instagram</label>
                                    <input type="text" class="form-control ltr" name="instagram"
                                        value="{{ $member->instagram }}" placeholder="Enter instagram url">
                                    <p id="errinstagram" class="mb-0 text-danger em"></p>
                                </div>
                                <div class="form-group">
                                    <label for="">Linkedin</label>
                                    <input type="text" class="form-control ltr" name="linkedin"
                                        value="{{ $member->linkedin }}" placeholder="Enter linkedin url">
                                    <p id="errlinkedin" class="mb-0 text-danger em"></p>
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
    {{-- Image preview JS --}}
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
