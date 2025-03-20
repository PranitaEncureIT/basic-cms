@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Maintenance Mode</h4>
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
        <a href="#">Maintenance Mode</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-lg-12">
                      <div class="card-title">Update Maintenance Page & Mode</div>
                  </div>
              </div>
          </div>
          <div class="card-body pt-5 pb-5">
            <div class="row">
              <div class="col-lg-6 offset-lg-3">

                <form id="maintenanceForm" action="{{route('admin.maintainance.update')}}" method="post">
                  @csrf

                  <div class="form-group">
                    <label for="">Maintenance ** </label>
                    <br>
                    <div class="thumb-preview" id="thumbPreview1">
                        <img src="{{asset('assets/front/img/maintainance.png?'.time())}}" alt="Maintenance">
                    </div>
                    <br>
                    <br>


                    <input id="fileInput1" type="file" name="maintenance" accept="image/* "style="display:none;">
                    <!-- Label acting as the button triggers the file input -->
                    <label for="fileInput1" class="choose-image btn btn-primary">Choose Image</label>


                    <p class="text-warning mb-0">JPG, PNG, JPEG images are allowed</p>
                    @if ($errors->has('maintenance'))
                    <p class="text-danger mb-0">{{$errors->first('maintenance')}}</p>
                    @endif

                </div>

                  <div class="form-group">
                    <label>Maintenance Mode **</label>
                    <div class="selectgroup w-100">
                      <label class="selectgroup-item">
                        <input type="radio" name="maintainance_mode" value="1" class="selectgroup-input" {{$bs->maintainance_mode == 1 ? 'checked' : ''}}>
                        <span class="selectgroup-button">Active</span>
                      </label>
                      <label class="selectgroup-item">
                        <input type="radio" name="maintainance_mode" value="0" class="selectgroup-input" {{$bs->maintainance_mode == 0 ? 'checked' : ''}}>
                        <span class="selectgroup-button">Deactive</span>
                      </label>
                    </div>
                    @if ($errors->has('maintainance_mode'))
                      <p class="mb-0 text-danger">{{$errors->first('maintainance_mode')}}</p>
                    @endif
                  </div>
                  <div class="form-group">
                    <label>Maintenance Text</label>
                    <textarea class="form-control" name="maintainance_text" rows="3" cols="80">{!! ($bs->maintainance_text) !!}</textarea>
                    @if ($errors->has('maintainance_text'))
                      <p class="mb-0 text-danger">{{$errors->first('maintainance_text')}}</p>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>Secret Path</label>
                    <input name="secret_path" type="text" class="form-control" value="{{$bs->secret_path}}">
                    <p class="text-warning">After activating maintenance mode, You can access the website via <strong class="text-danger">{{url('{secret_path}')}}</strong></p>
                    <p class="text-warning">Try to avoid using special characters in {secret path}</p>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" form="maintenanceForm" class="btn btn-success">Update</button>
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
