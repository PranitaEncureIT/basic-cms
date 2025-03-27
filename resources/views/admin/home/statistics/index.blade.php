@extends('admin.layout')

@if(!empty($selLang) && $selLang->rtl == 1)
@section('styles')
<style>
    select[name='language'] {
        direction: rtl;
    }
</style>
@endsection
@endif

@section('content')
  <div class="page-header">
    <h4 class="page-title">Statistics Section</h4>
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
        <a href="#">Statistics Section</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      @if ($be->theme_version != 'car' && $bex->home_page_pagebuilder == 0)
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-10">
                        <div class="card-title">Background Image</div>
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
            <div class="card-body">
                <div class="row">
                    <div class="offset-lg-3 col-lg-6 text-center">
                        <form id="backgroundForm" action="{{route('admin.statistics.upload', $lang_id)}}" method="POST">
                            @csrf
                            {{-- Image Part --}}
                            <div class="form-group">
                                <div class="thumb-preview" id="thumbPreview1">
                                    <img src="{{asset('assets/front/img/' . $abe->statistics_bg)}}" alt="Image">
                                </div>
                                <br>
                                <br>


                                <input id="fileInput1" type="hidden" name="background_image">
                                <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                                <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                                @if ($errors->has('background_image'))
                                <p class="text-danger mb-0">{{$errors->first('background_image')}}</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button class="btn btn-success" type="submit" form="backgroundForm">Update</button>
            </div>
        </div>
      @endif

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card-title d-inline-block">Statistics</div>
                </div>
                <div class="col-lg-6 mt-2 mt-lg-0">
                    <a href="#" class="btn btn-primary float-lg-right float-left" data-toggle="modal" data-target="#createStatisticModal"><i class="fas fa-plus"></i> Add Statistic</a>
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($statistics) == 0)
                <h2 class="text-center">NO STATISTIC ADDED</h2>
              @else
                <div class="table-responsive">
                  <table id="statistics_section" class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Icon</th>
                        <th scope="col">Title</th>
                        <th scope="col">Quantity</th>
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

  {{-- Statistic Create Modal --}}
  @includeif('admin.home.statistics.create')

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
          let table;
          $(document).ready(function() {
              var table = $('#statistics_section').DataTable({
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
                      url: "{{ route('admin.statistics.index') }}",
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
                          data: 'quantity',
                          name: 'quantity'
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
              $('#statistics_section').on('click', '.deletebutton', function(e) {
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
                              url: "{{ route('admin.statistics.delete') }}",
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
                    $("form .input-group").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form .nicEdit-main").each(function() {
                        $(this).addClass('rtl text-right');
                    });

                } else {
                    $("form input, form select, form textarea, form .input-group").removeClass('rtl');
                    $("form .nicEdit-main").removeClass('rtl text-right');
                }
            })
        });
    });
  </script>
@endsection
