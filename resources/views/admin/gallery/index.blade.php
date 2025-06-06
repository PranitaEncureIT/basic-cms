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
  <h4 class="page-title">Gallery Images</h4>
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
      <a href="#">Gallery Image Management</a>
    </li>
  </ul>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-lg-4">
            <div class="card-title d-inline-block">Gallery Images</div>
          </div>
          <div class="col-lg-3">
            @if (!empty($langs))
            <select name="language" class="form-control"
              onchange="window.location='{{url()->current() . '?language='}}'+this.value">
              <option value="" selected disabled>Select a Language</option>
              @foreach ($langs as $lang)
              <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>
                {{$lang->name}}</option>
              @endforeach
            </select>
            @endif
          </div>
          <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
            <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModal"><i
                class="fas fa-plus"></i> Add Image</a>
            <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
              data-href="{{route('admin.gallery.bulk.delete')}}"><i class="flaticon-interface-5"></i> Delete</button>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            @if (count($galleries) == 0)
            <h3 class="text-center">NO IMAGE FOUND</h3>
            @else
            <div class="table-responsive">
              <table class="table table-striped mt-3" id="basic-datatables">
                <thead>
                  <tr>
                    <th scope="col">
                      <input type="checkbox" class="bulk-check" data-val="all">
                    </th>
                    <th scope="col">Image</th>
                    <th scope="col">Title</th>
                    <th scope="col">Serial Number</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($galleries as $key => $gallery)
                  <tr>
                    <td>
                      <input type="checkbox" class="bulk-check" data-val="{{$gallery->id}}">
                    </td>

                    <td>
                      @php
                        $imagePath = public_path('cms/gallery/' . $gallery->image);
                      @endphp

                  @if (!empty($gallery->image) && file_exists(public_path('cms/gallery/' . $gallery->image)))
                  
                      <img src="{{ asset('cms/gallery/' . $gallery->image) }}"
                          alt="Image" style="width: 100px;">
                  @else
                      <img src="{{ asset('assets/front/img/no_image.jpg') }}"
                          alt="Image" style="width: 100px;">
                  @endif

                    </td>

                    <td>
                      {{strlen($gallery->title) > 70 ? mb_substr($gallery->title, 0, 70, 'UTF-8') . '...' : $gallery->title}}
                    </td>
                    <td>{{$gallery->serial_number}}</td>
                    <td>
                      <a class="btn btn-secondary btn-sm"
                        href="{{route('admin.gallery.edit', $gallery->id) . '?language=' . request()->input('language')}}">
                        <span class="btn-label">
                          <i class="fas fa-edit"></i>
                        </span>
                        Edit
                      </a>
                      <form class="deleteform d-inline-block" action="{{route('admin.gallery.delete')}}" method="post">
                        @csrf
                        <input type="hidden" name="gallery_id" value="{{$gallery->id}}">
                        <button type="submit" class="btn btn-danger btn-sm deletebtn">
                          <span class="btn-label">
                            <i class="fas fa-trash"></i>
                          </span>
                          Delete
                        </button>
                      </form>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Create Gallery Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="ajaxForm" class="modal-form" action="{{route('admin.gallery.store')}}" method="POST">
          @csrf
          {{-- Image Part --}}
          <div class="form-group">
            <label for="">Image ** </label>
            <br>
            <div class="thumb-preview" id="thumbPreview1">
              <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="User Image">
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
            <label for="">Language **</label>
            <select name="language_id" class="form-control">
              <option value="" selected disabled>Select a language</option>
              @foreach ($langs as $lang)
              <option value="{{$lang->id}}">{{$lang->name}}</option>
              @endforeach
            </select>
            <p id="errlanguage_id" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group {{ $categoryInfo->gallery_category_status == 0 ? 'd-none' : '' }}">
            <label for="">Category **</label>
            <select name="category_id" id="gallery_category_id" class="form-control" disabled>
              <option selected disabled>Select a category</option>
            </select>
            <p id="errcategory_id" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group">
            <label for="">Title **</label>
            <input type="text" class="form-control" name="title" placeholder="Enter title" value="">
            <p id="errtitle" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group">
            <label for="">Serial Number **</label>
            <input type="number" class="form-control ltr" name="serial_number" value=""
              placeholder="Enter Serial Number">
            <p id="errserial_number" class="mb-0 text-danger em"></p>
            <p class="text-warning"><small>The higher the serial number is, the later the image will be shown.</small>
            </p>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="submitBtn" type="button" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript for image preview -->
<script>
  document.getElementById('fileInput1').addEventListener('change', function (event) {
      const input = event.target;
      if (input.files && input.files[0]) {
          const reader = new FileReader();
          reader.onload = function (e) {
              document.getElementById('thumbPreview1').innerHTML =
                  '<img src="' + e.target.result + '" alt="Image">';
          }
          reader.readAsDataURL(input.files[0]);
      }
  });
</script>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $("select[name='language_id']").on('change', function() {
      $("#gallery_category_id").removeAttr('disabled');

      let langId = $(this).val();
      let url = "{{url('/')}}/admin/gallery/" + langId + "/get_categories";

      $.get(url, function(data) {
        let options = `<option value="" disabled selected>Select a category</option>`;

        if (data.length == 0) {
          options += `<option value="" disabled>${'No Category Exists'}</option>`;
        } else {
          for (let i = 0; i < data.length; i++) {
            options +=`<option value="${data[i].id}">${data[i].name}</option>`;
          }
        }

        $("#gallery_category_id").html(options);
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
          $("form .nicEdit-main").each(function() {
            $(this).addClass('rtl text-right');
          });
        } else {
          $("form input, form select, form textarea").removeClass('rtl');
          $("form .nicEdit-main").removeClass('rtl text-right');
        }
      });
    });
  });
</script>
@endsection
