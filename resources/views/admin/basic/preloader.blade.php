@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Preloader</h4>
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
        <a href="#">Preloader</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Update Preloader</div>
        </div>
        <div class="card-body pt-5 pb-4">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form  enctype="multipart/form-data" action="{{route('admin.preloader.update')}}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                        <label>Status</label>
                        <div class="selectgroup w-100">
                           <label class="selectgroup-item">
                            <input type="radio" name="preloader_status" value="1" class="selectgroup-input" {{$bex->preloader_status == 1 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Active</span>
                           </label>
                           <label class="selectgroup-item">
                            <input type="radio" name="preloader_status" value="0" class="selectgroup-input" {{$bex->preloader_status == 0 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Deactive</span>
                           </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Preloader ** </label>
                        <br>
                        @php
                          $imagePath = public_path('assets/front/img/' . $bex->preloader);
                        @endphp
                        <div class="thumb-preview" id="thumbPreview1">
                          @if (file_exists($imagePath) && !empty($bex->preloader))
                            <img src="{{ asset('assets/front/img/' . $bex->preloader) }}"
                            alt="Preloader" width="100%">
                          @else
                            <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="" width="100%">
                          @endif
                        </div>
                        <br>
                        <br>

                        <input id="fileInput1" type="file" name="preloader"
                          accept="image/* "style="display:none;">
                        <!-- Label acting as the button triggers the file input -->
                        <label for="fileInput1" class="choose-image btn btn-primary">Choose Image</label>

                        <p class="text-warning mb-0">JPG, PNG, JPEG, GIF, SVG images are allowed</p>
                        @if ($errors->has('preloader'))
                        <p class="text-danger mb-0">{{$errors->first('preloader')}}</p>
                        @endif
                    </div>
                  </div>
                </div>

                <div class="card-footer">
                  <div class="form">
                    <div class="form-group from-show-notify row">
                      <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success">Update</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
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
