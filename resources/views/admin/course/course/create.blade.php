@extends('admin.layout')

@php
$selLang = \App\Models\Language::where('code', request()->input('language'))->first();
@endphp

@if(!empty($selLang) && $selLang->rtl == 1)
  @section('styles')
  <style>
    form:not(.modal-form) input,
    form:not(.modal-form) textarea,
    form:not(.modal-form) select,
    select[name='language'] {
      direction: rtl;
    }

    form:not(.modal-form) .note-editor.note-frame .note-editing-area .note-editable {
      direction: rtl;
      text-align: right;
    }
  </style>
  @endsection
@endif

@section('content')
<div class="page-header">
  <h4 class="page-title">Create Course</h4>
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
      <a href="#">Course Page</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Create Course</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title d-inline-block">Create Course</div>
        <a
          class="btn btn-info btn-sm float-right d-inline-block"
          href="{{route('admin.course.index') . '?language=' . request()->input('language')}}"
        >
          <span class="btn-label">
            <i
              class="fas fa-backward"
              style="font-size: 12px;"
            ></i>
          </span>
          Back
        </a>
      </div>

      <div class="card-body pt-5 pb-5">
        <div class="row">
          <div class="col-lg-6 offset-lg-3">

            <form
              id="ajaxForm"
              action="{{route('admin.course.store')}}"
              method="POST"
              enctype="multipart/form-data"
            >
              @csrf

                {{-- Image Part --}}
                <div class="form-group">
                    <label for="">Image ** </label>
                    <br>
                    <div class="thumb-preview" id="thumbPreview1">
                        <img src="{{asset('assets/front/img/no_image.jpg')}}" alt="User Image">
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

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Language **</label>
                    <select
                      id="language"
                      name="language_id"
                      class="form-control"
                    >
                      <option
                        value=""
                        selected
                        disabled
                      >Select a Language</option>
                      @foreach ($langs as $lang)
                      <option value="{{$lang->id}}">{{$lang->name}}</option>
                      @endforeach
                    </select>
                    <p
                      id="errlanguage_id"
                      class="mb-0 text-danger em"
                    ></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Course Category **</label>
                    <select
                      id="course_category_id"
                      class="form-control"
                      name="course_category_id"
                      disabled
                    >
                      <option
                        value=""
                        selected
                        disabled
                      >Select a Category</option>
                    </select>
                    <p id="errcourse_category_id" class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Course Title **</label>
                    <input
                      type="text"
                      class="form-control"
                      name="title"
                      placeholder="Enter Title"
                      value=""
                    >
                    <p id="errtitle" class="mb-0 text-danger em"></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Course Duration **</label>
                    <input
                      type="text"
                      class="form-control ltr"
                      name="duration"
                      placeholder="eg: 10h 15m"
                    >
                    <p
                      id="errduration"
                      class="mb-0 text-danger em"
                    ></p>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                      <label for="">Current Price ({{$bex->base_currency_text}})</label>
                      <input
                        type="number"
                        class="form-control ltr"
                        name="current_price"
                        placeholder="Enter Current Price"
                        value=""
                      >
                      <p class="mb-0 text-danger em"></p>
                      <p class="mb-0 text-warning">Leave it blank if it is a free course</p>
                    </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Previous Price ({{$bex->base_currency_text}})</label>
                    <input
                      type="number"
                      class="form-control ltr"
                      name="previous_price"
                      placeholder="Enter Previous Price"
                      value=""
                    >
                    <p class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="">Course Summary</label>
                <textarea
                  class="form-control"
                  name="summary"
                  rows="6"
                  cols="80"
                  placeholder="Enter Course Summary"
                ></textarea>
                <p
                  class="mb-0 text-danger em"
                ></p>
              </div>

              <div class="form-group mb-1">
                <label for="">Course Video **</label>
                <input
                  type="text"
                  class="form-control ltr"
                  name="video_link"
                  placeholder="Enter YouTube Video Link"
                  value=""
                >
                <p id="errvideo_link" class="mb-0 text-danger em"></p>
              </div>

              <div class="form-group">
                <label for="">Course Overview **</label>
                <textarea
                  class="form-control summernote"
                  name="overview"
                  rows="8"
                  cols="80"
                  placeholder="Enter Overview"
                ></textarea>
                <p
                  id="erroverview"
                  class="mb-0 text-danger em"
                ></p>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Instructor Name **</label>
                    <input
                      type="text"
                      class="form-control"
                      name="instructor_name"
                      placeholder="Enter Instructor Name"
                      value=""
                    >
                    <p id="errinstructor_name" class="mb-0 text-danger em"></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Instructor Occupation **</label>
                    <input
                      type="text"
                      class="form-control"
                      name="instructor_occupation"
                      placeholder="Enter Instructor Occupation"
                      value=""
                    >
                    <p id="errinstructor_occupation" class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="">Instructor Details **</label>
                <textarea
                  class="form-control"
                  name="instructor_details"
                  rows="6"
                  cols="80"
                  placeholder="Enter Instructor Details"
                ></textarea>
                <p id="errinstructor_details" class="mb-0 text-danger em"></p>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Instructor Facebook</label>
                    <input
                      type="text"
                      class="form-control"
                      name="instructor_facebook"
                      placeholder="Enter Faecbook ID"
                      value=""
                    >
                    <p class="mb-0 text-danger em"></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Instructor Instagram</label>
                    <input
                      type="text"
                      class="form-control"
                      name="instructor_instagram"
                      placeholder="Enter Instagram ID"
                      value=""
                    >
                    <p class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Instructor Twitter</label>
                    <input
                      type="text"
                      class="form-control"
                      name="instructor_twitter"
                      placeholder="Enter Twitter ID"
                      value=""
                    >
                    <p class="mb-0 text-danger em"></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="">Instructor LinkedIn</label>
                    <input
                      type="text"
                      class="form-control"
                      name="instructor_linkedin"
                      placeholder="Enter LinkedIn ID"
                      value=""
                    >
                    <p class="mb-0 text-danger em"></p>
                  </div>
                </div>

                <div class="col-12">
                    {{-- Instructor Image Part --}}
                    <div class="form-group">
                        <label for="">Instructor Image ** </label>
                        <br>
                        <div class="thumb-preview" id="thumbPreview2">
                            <img src="{{asset('assets/front/img/no_image.jpg')}}" alt="Instructor Image">
                        </div>
                        <br>
                        <br>


                        <input id="fileInput2" type="file" name="instructor_image" accept="image/*"
                        style="display: none;">
                       <!-- Label acting as the button triggers the file input -->
                       <label for="fileInput2" class="choose-image btn btn-primary">Choose Image</label>


                        <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                        <p class="em text-danger mb-0" id="errinstructor_image"></p>

                    </div>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>

      <div class="card-footer">
        <div class="form">
          <div class="form-group from-show-notify row">
            <div class="col-12 text-center">
              <button
                id="submitBtn"
                type="button"
                class="btn btn-success"
              >Submit</button>
            </div>
          </div>
        </div>
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

  // Initialize preview for both inputs
  previewImage('fileInput1', 'thumbPreview1');
  previewImage('fileInput2', 'thumbPreview2');
</script>

@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $("select[name='language_id']").on('change', function() {
      $("#course_category_id").removeAttr('disabled');

      let langId = $(this).val();
      let url = "{{url('/')}}/admin/course/" + langId + "/get_categories";
      // console.log(url);

      $.get(url, function(data) {
        // console.log(data);
        let options = `<option value="" disabled selected>Select a Category</option>`;

        if (data.length == 0) {
          options += `<option value="" disabled>${'No Category Exists'}</option>`;
        } else {
          for (let i = 0; i < data.length; i++) {
            options += `<option value="${data[i].id}">${data[i].name}</option>`;
          }
        }

        $("#course_category_id").html(options);
      });
    });

    // make input fields RTL
    $("select[name='language_id']").on('change', function() {
      $(".request-loader").addClass("show");

      let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
      console.log(url);

      $.get(url, function(data) {
        $(".request-loader").removeClass("show");
        if (data == 1) {
          $("form input").each(function() {
            if (!$(this).hasClass('ltr')) {
              $(this).addClass('rtl');
            }
          });
          $("form select").each(function() {
            if (!$(this).hasClass('ltr')) {
              $(this).addClass('rtl');
            }
          });
          $("form textarea").each(function() {
            if (!$(this).hasClass('ltr')) {
              $(this).addClass('rtl');
            }
          });
          $("form .summernote").each(function() {
            $(this).siblings('.note-editor').find('.note-editable').addClass('rtl text-right');
          });
        } else {
          $("form input, form select, form textarea").removeClass('rtl');
          $("form.modal-form .summernote").siblings('.note-editor').find('.note-editable').removeClass('rtl text-right');
        }
      })
    });

    // translatable portfolios will be available if the selected language is not 'Default'
    $("#language").on('change', function() {
      let language = $(this).val();
      // console.log(language);
      if (language == 0) {
        $("#translatable").attr('disabled', true);
      } else {
        $("#translatable").removeAttr('disabled');
      }
    });
  });
</script>
@endsection
