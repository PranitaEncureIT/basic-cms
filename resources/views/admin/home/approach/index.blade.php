@extends('admin.layout')

@if(!empty($abs->language) && $abs->language->rtl == 1)
@section('styles')
<style>
    form:not(.modal-form) input,
    form:not(.modal-form)  textarea,
    form:not(.modal-form)  select {
        direction: rtl;
    }
    form:not(.modal-form)  .note-editor.note-frame .note-editing-area .note-editable {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
<div class="page-header">
    <h4 class="page-title">Approach Section</h4>
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
            <a href="#">Home</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Approach Section</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">

        @if ($bex->home_page_pagebuilder == 0)

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-10">
                        <div class="card-title">Title & Subtitle</div>
                    </div>
                    <div class="col-lg-2">
                        @if (!empty($langs))
                        <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                            <option value="" selected disabled>Select a Language</option>
                            @foreach ($langs as $lang)
                            <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>
            </div>
            <form class="" action="{{route('admin.approach.update', $lang_id)}}" method="post">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Title **</label>
                                <input class="form-control" name="approach_section_title" value="{{$abs->approach_title}}" placeholder="Enter Title">
                                @if ($errors->has('approach_section_title'))
                                <p class="mb-0 text-danger">{{$errors->first('approach_section_title')}}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Subtitle **</label>
                                <input class="form-control" name="approach_section_subtitle" value="{{$abs->approach_subtitle}}" placeholder="Enter Subtitle">
                                @if ($errors->has('approach_section_subtitle'))
                                <p class="mb-0 text-danger">{{$errors->first('approach_section_subtitle')}}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Button Text</label>
                                <input class="form-control" name="approach_section_button_text" value="{{$abs->approach_button_text}}" placeholder="Enter Button Text">
                                @if ($errors->has('approach_section_button_text'))
                                <p class="mb-0 text-danger">{{$errors->first('approach_section_button_text')}}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Button URL</label>
                                <input class="form-control ltr" name="approach_section_button_url" value="{{$abs->approach_button_url}}" placeholder="Enter Button URL">
                                @if ($errors->has('approach_section_button_url'))
                                <p class="mb-0 text-danger">{{$errors->first('approach_section_button_url')}}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form">
                        <div class="form-group from-show-notify row">
                            <div class="col-12 text-center">
                                <button type="submit" id="displayNotif" class="btn btn-success">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="card-title d-inline-block">Points</div>
                <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#createPointModal"><i class="fas fa-plus"></i> Add Point</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        @if (count($points) == 0)
                        <h2 class="text-center">NO POINT ADDED</h2>
                        @else
                        <div class="table-responsive">
                            <table id="approach_section" class="table table-striped table-row-bordered gy-5 gs-7 mt-3">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Icon</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Serial Number</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
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

{{-- Point Create Modal --}}
@includeif('admin.home.approach.create')

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        let table;
        $(document).ready(function() {
            var table = $('#approach_section').DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                lengthChange: true,
                searching: true,
                info: true,
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
                // dom: 'Bfrtip', // Include buttons in the layout
                "order": [
                    [0, "asc"]
                ],
                "ajax": {
                    url: "{{ route('admin.approach.index') }}",
                    type: "GET"
                    // data: function (d) {
                    //     d.name = $('#search_name').val();
                    // }
                },
                columns: [{
                        data: 'sr_no',
                        name: 'sr_no',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'icon',
                        name: 'icon',
                    },
                    {
                        data: 'title',
                        name: 'title',
                        searchable: true
                    },
                    {
                        data: 'serial_number',
                        name: 'serial_number'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Use the table element (or a container) as the delegation root.
            $('#approach_section').on('click', '.deletebutton', function(e) {
                e.preventDefault(); // Prevent any default behavior

                let clientId = $(this).data('id'); // Get ID from data attribute
                console.log("Client ID:", clientId); // Check if correct id is received

                Swal.fire({
                    title: "Are you sure?",
                    text: "This record will be deleted permanently!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.approach.pointdelete') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                user_id: clientId
                            },
                            success: function(response) {
                                console.log("AJAX success response:", response);
                                if (response.success) {
                                    // Reload the DataTable
                                    window.location.reload();
                                } else {
                                    console.error("Deletion did not return success");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("AJAX error:", error);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection


@section('scripts')
<script>
    $(document).ready(function() {
        $('.icp').on('iconpickerSelected', function(event){
            $("#inputIcon").val($(".iconpicker-component").find('i').attr('class'));
        });

        // make input fields RTL
        $("select[name='language_id']").on('change', function() {
            $(".request-loader").addClass("show");
            let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
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
                    $("form.modal-form input, form.modal-form select, form.modal-form textarea").removeClass('rtl');
                    $("form.modal-form .nicEdit-main").removeClass('rtl text-right');
                }
            })
        });
    });
</script>
@endsection
