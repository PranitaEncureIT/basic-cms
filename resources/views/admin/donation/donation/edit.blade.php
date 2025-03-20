@extends('admin.layout')

@if(!empty($event->language) && $event->language->rtl == 1)
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
    <h4 class="page-title">Edit Donation</h4>
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
        <a href="#">Donation Page</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Edit Donation</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">Edit Donation</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.donation.index') . '?language=' . request()->input('language')}}">
            <span class="btn-label">
              <i class="fas fa-backward" style="font-size: 12px;"></i>
            </span>
            Back
          </a>
        </div>
        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">

              <form id="ajaxForm" class="" action="{{route('admin.donation.update')}}" method="post">
                @csrf
                <input type="hidden" name="donation_id" value="{{$donation->id}}">
                <input type="hidden" name="lang_id" value="{{$donation->lang_id}}">

                {{-- Image Part --}}
                <div class="form-group">
                    <label for="">Image ** </label>
                    <br>
                    @php
                      $imagePath = public_path('cms/donations/' . $donation->image);                            
                    @endphp
                    <div class="thumb-preview" id="thumbPreview1">
                      @if (!empty($donation->image) && file_exists(public_path('cms/donations/' . $donation->image)))
                      <img src="{{ asset('cms/donations/' . $donation->image) }}"
                          alt="Image" style="width: 100px;">
                  @else
                      <img src="{{ asset('assets/front/img/no_image.jpg') }}"
                          alt="Image" style="width: 100px;">
                  @endif
                    </div>
                    <br>
                    <br>

                    <input id="fileInput1" type="file" name="image" accept="image/* "style="display:none;">
                    <!-- Label acting as the button triggers the file input -->
                    <label for="fileInput1" class="choose-image btn btn-primary">Choose Image</label>


                    <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                    <p class="em text-danger mb-0" id="errimage"></p>
                </div>
                <div class="form-group">
                  <label for="">Title **</label>
                  <input type="text" class="form-control" name="title" value="{{$donation->title}}" placeholder="Enter title">
                  <p id="errtitle" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Content **</label>
                  <textarea class="form-control summernote" name="content" data-height="300" placeholder="Enter content">{{convertHtml($donation->content)}}</textarea>
                  <p id="errcontent" class="mb-0 text-danger em"></p>
                </div>
                  <div class="form-group">
                      <label for="">Goal Amount (in {{$abx->base_currency_text}}) **</label>
                      <input type="number" class="form-control ltr" name="goal_amount" value="{{$donation->goal_amount}}" placeholder="Enter Ticket Cost">
                      <p id="errgoal_amount" class="mb-0 text-danger em"></p>
                  </div>
                  <div class="form-group">
                      <label for="">Minimum Amount (in {{$abx->base_currency_text}}) **</label>
                      <input type="number" class="form-control ltr" name="min_amount" value="{{$donation->min_amount}}" placeholder="Enter Ticket Cost">
                      <small class="text-warning">Minimum amount for this cause</small>
                      <p id="errmin_amount" class="mb-0 text-danger em"></p>
                  </div>
                  <div class="form-group">
                      <label for="">Custom Amount (in {{$abx->base_currency_text}}) </label>
                      <input type="text" class="form-control" name="custom_amount" value="{{$donation->custom_amount}}" data-role="tagsinput">
                      <small class="text-warning">Use comma (,) to seperate the amounts.</small><br>
                      <small class="text-warning">Custom amount must be equal to or greater than minimum amount</small>
                      <p id="errcustom_amount" class="mb-0 text-danger em"></p>
                  </div>
                <div class="form-group">
                  <label for="">Meta Keywords</label>
                  <input type="text" class="form-control" name="meta_tags" value="{{$donation->meta_tags}}" data-role="tagsinput">
                  <p id="errmeta_keywords" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Meta Description</label>
                  <textarea type="text" class="form-control" name="meta_description" rows="5">{{$donation->meta_description}}</textarea>
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
