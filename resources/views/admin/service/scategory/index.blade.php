@extends('admin.layout')

@php
    $selLang = \App\Models\Language::where('code', request()->input('language'))->first();
@endphp
@if (!empty($selLang) && $selLang->rtl == 1)
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
        <h4 class="page-title">Service Categories</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Service Page</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Category</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title d-inline-block">Categories</div>
                        </div>
                        <div class="col-lg-3">
                            @if (!empty($langs))
                                <select name="language" class="form-control"
                                    onchange="window.location='{{ url()->current() . '?language=' }}'+this.value">
                                    <option value="" selected disabled>Select a Language</option>
                                    @foreach ($langs as $lang)
                                        <option value="{{ $lang->code }}"
                                            {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                                            {{ $lang->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                            <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal"
                                data-target="#createModal"><i class="fas fa-plus"></i> Add Category</a>
                            <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
                                data-href="{{ route('admin.scategory.bulk.delete') }}"><i class="flaticon-interface-5"></i>
                                Delete</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($scategorys) == 0)
                                <h3 class="text-center">NO SERVICE CATEGORY FOUND</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">Icon Image</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Featured</th>
                                                <th scope="col">Serial Number</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($scategorys as $key => $scategory)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $scategory->id }}">
                                                    </td>
                                                    <td>
                                                        @php
                                                            $imagePath = public_path('cms/scategory/'. $scategory->image);       
                                                        @endphp

                                                        @if (!empty($scategory->image) && file_exists(public_path('cms/scategory/' . $scategory->image)))
                                                            <img src="{{ asset('cms/scategory/' . $scategory->image) }}"
                                                                alt="Image" style="width: 100px;">
                                                        @else
                                                            <img src="{{ asset('assets/front/img/no_image.jpg') }}"
                                                                alt="Image" style="width: 100px;">
                                                        @endif

                                                    </td>
                                                    <td>{{ convertUtf8($scategory->name) }}</td>
                                                    <td>
                                                        <form id="featureForm{{ $scategory->id }}" class="d-inline-block"
                                                            action="{{ route('admin.scategory.feature') }}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="scategory_id"
                                                                value="{{ $scategory->id }}">
                                                            <select
                                                                class="form-control {{ $scategory->feature == 1 ? 'bg-success' : 'bg-danger' }}"
                                                                name="feature"
                                                                onchange="document.getElementById('featureForm{{ $scategory->id }}').submit();">
                                                                <option value="1"
                                                                    {{ $scategory->feature == 1 ? 'selected' : '' }}>Yes
                                                                </option>
                                                                <option value="0"
                                                                    {{ $scategory->feature == 0 ? 'selected' : '' }}>No
                                                                </option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td>{{ $scategory->serial_number }}</td>
                                                    <td>
                                                        @if ($scategory->status == 1)
                                                            <h2 class="d-inline-block"><span
                                                                    class="badge badge-success">Active</span></h2>
                                                        @else
                                                            <h2 class="d-inline-block"><span
                                                                    class="badge badge-danger">Inactive</span></h2>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-secondary btn-sm editbtn"
                                                            href="{{ route('admin.scategory.edit', $scategory->id) . '?language=' . request()->input('language') }}">
                                                            <span class="btn-label">
                                                                <i class="fas fa-edit"></i>
                                                            </span>
                                                            Edit
                                                        </a>
                                                        <form class="deleteform d-inline-block"
                                                            action="{{ route('admin.scategory.delete') }}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="scategory_id"
                                                                value="{{ $scategory->id }}">
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
                <div class="card-footer">
                    <div class="row">
                        <div class="d-inline-block mx-auto">
                            {{ $scategorys->appends(['language' => request()->input('language')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Create Service Category Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Service Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="ajaxForm" class="modal-form" action="{{ route('admin.scategory.store') }}"
                        method="POST">
                        @csrf
                        {{-- Image Part --}}
                        <div class="form-group">
                            <label for="">Image ** </label>
                            <br>
                            <div class="thumb-preview" id="thumbPreview1">
                                <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="Image">
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
                                    <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                                @endforeach
                            </select>
                            <p id="errlanguage_id" class="mb-0 text-danger em"></p>
                        </div>
                        <div class="form-group">
                            <label for="">Name **</label>
                            <input type="text" class="form-control" name="name" value=""
                                placeholder="Enter name">
                            <p id="errname" class="mb-0 text-danger em"></p>
                        </div>
                        <div class="form-group">
                            <label for="">Sort Text **</label>
                            <input type="text" class="form-control" name="short_text" value=""
                                placeholder="Enter short text">
                            <p id="errshort_text" class="mb-0 text-danger em"></p>
                        </div>
                        <div class="form-group">
                            <label for="">Status **</label>
                            <select class="form-control ltr" name="status">
                                <option value="" selected disabled>Select a status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <p id="errstatus" class="mb-0 text-danger em"></p>
                        </div>
                        <div class="form-group">
                            <label for="">Serial Number **</label>
                            <input type="number" class="form-control ltr" name="serial_number" value=""
                                placeholder="Enter Serial Number">
                            <p id="errserial_number" class="mb-0 text-danger em"></p>
                            <p class="text-warning"><small>The higher the serial number is, the later the service category
                                    will be shown everywhere.</small></p>
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

            // make input fields RTL
            $("select[name='language_id']").on('change', function() {
                $(".request-loader").addClass("show");
                let url = "{{ url('/') }}/admin/rtlcheck/" + $(this).val();
                console.log(url);
                $.get(url, function(data) {
                    $(".request-loader").removeClass("show");
                    if (data == 1) {
                        $("form.modal-form input").each(function() {
                            if (!$(this).hasClass('ltr')) {
                                $(this).addClass('rtl');
                            }
                        });
                        $("form.modal-form select").each(function() {
                            if (!$(this).hasClass('ltr')) {
                                $(this).addClass('rtl');
                            }
                        });
                        $("form.modal-form textarea").each(function() {
                            if (!$(this).hasClass('ltr')) {
                                $(this).addClass('rtl');
                            }
                        });
                        $("form.modal-form .nicEdit-main").each(function() {
                            $(this).addClass('rtl text-right');
                        });

                    } else {
                        $("form.modal-form input, form.modal-form select, form.modal-form textarea")
                            .removeClass('rtl');
                        $("form.modal-form .nicEdit-main").removeClass('rtl text-right');
                    }
                })
            });
        });
    </script>
@endsection
