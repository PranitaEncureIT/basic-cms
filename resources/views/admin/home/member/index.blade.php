@extends('admin.layout')

@if (!empty($abs->language) && $abs->language->rtl == 1)
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
        <h4 class="page-title">Team Section</h4>
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
                <a href="#">Home Page</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Team Section</a>
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
                        </div>
                    </div>

                    <form class="" action="{{ route('admin.teamtext.update', $lang_id) }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                @if ($be->theme_version == 'default' || $be->theme_version == 'dark')
                                    <div class="col-lg-6 offset-lg-3">
                                        {{-- Background Part --}}
                                        <div class="form-group">
                                            <label for="">Background ** </label>
                                            <br>
                                            @php
                                                $imagePath = public_path('cms/memebers/' . $member->image);
                                            @endphp
                                            <div class="thumb-preview" id="thumbPreview1">
                                                @if (!empty($member->image) && file_exists(public_path('cms/memebers/' . $member->image)))
                                                    @else
                                                        <img src="{{ asset('assets/front/img/no_image.jpg') }}"
                                                            alt="Image">
                                                    @endif

                                            </div>
                                            <br>
                                            <br>
                                            <input id="fileInput1" type="file" name="background" accept="image/*"
                                                style="display: none;">
                                            <!-- Label acting as the button triggers the file input -->
                                            <label for="fileInput1" class="choose-image btn btn-primary">Choose
                                                Image</label>


                                            <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                                            @if ($errors->has('background'))
                                                <p class="text-danger mb-0">{{ $errors->first('background') }}</p>
                                            @endif


                                        </div>
                                    </div>
                                @endif

                                <div class="col-lg-6 offset-lg-3">
                                    <div class="form-group">
                                        <label>Title **</label>
                                        <input class="form-control" name="team_section_title"
                                            value="{{ $abs->team_section_title }}" placeholder="Enter Title">
                                        @if ($errors->has('team_section_title'))
                                            <p class="mb-0 text-danger">{{ $errors->first('team_section_title') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6 offset-lg-3">
                                    <div class="form-group">
                                        <label>Subtitle **</label>
                                        <input class="form-control" name="team_section_subtitle"
                                            value="{{ $abs->team_section_subtitle }}" placeholder="Enter Subtitle">
                                        @if ($errors->has('team_section_subtitle'))
                                            <p class="mb-0 text-danger">{{ $errors->first('team_section_subtitle') }}</p>
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
                    <div class="card-title d-inline-block">Members</div>
                    <a href="{{ route('admin.member.create') . '?language=' . request()->input('language') }}"
                        class="btn btn-primary float-right"><i class="fas fa-plus"></i> Add Member</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($members) == 0)
                                <h3 class="text-center">NO MEMBER FOUND</h3>
                            @else
                                <div class="table-responsive">
                                    <table id="members_section" class="table table-striped mt-3">   
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Image</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Rank</th>
                                                <th scope="col">Featured</th>
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
    
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // let table;
        $(document).ready(function() {
            var table = $('#members_section').DataTable({
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
                    url: "{{ route('admin.member.index') }}",
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
                        data: 'image',
                        name: 'image',
                    },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: true
                    },
                    {
                        data: 'rank',
                        name: 'rank'
                    },
                    {
                        data: 'feature',
                        name: 'feature'
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
            $('#members_section').on('click', '.deletebutton', function(e) {
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
                            url: "{{ route('admin.member.delete') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                member_id: clientId
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
