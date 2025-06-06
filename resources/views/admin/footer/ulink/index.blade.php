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
    <h4 class="page-title">Userful Links</h4>
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
        <a href="#">Footer</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Userful Links</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title d-inline-block">Userful Links</div>
                </div>
                <div class="col-lg-3">
                    @if (!empty($langs))
                        <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                            <option value="" selected disabled>Select a Language</option>
                            @foreach ($langs as $lang)
                                <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                    <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i> Add Userful Link</a>
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($aulinks) == 0)
                <h3 class="text-center">NO USEFUL LINK FOUND</h3>
              @else
                <div class="table-responsive">
                  <table id="ulink_section" class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">URL</th>
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


  <!-- Create Userful Link Modal -->
  <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Add Userful Link</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="ajaxForm" class="modal-form create" action="{{route('admin.ulink.store')}}" method="POST">
            @csrf
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
            <div class="form-group">
              <label for="">Name **</label>
              <input type="text" class="form-control" name="name" value="" placeholder="Enter name">
              <p id="errname" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">URL **</label>
              <input class="form-control ltr" name="url" placeholder="Enter url">
              <p id="errurl" class="mb-0 text-danger em"></p>
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

  <!-- Edit Userful Link Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Edit Userful Link</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="ajaxEditForm" action="{{route('admin.ulink.update')}}" method="POST">
            @csrf
            <input id="inulink_id" type="hidden" name="ulink_id" value="">
            <div class="form-group">
              <label for="">Name **</label>
              <input id="inname" type="text" class="form-control" name="name" value="" placeholder="Enter name">
              <p id="eerrname" class="mb-0 text-danger em"></p>
            </div>
            <div class="form-group">
              <label for="">URL **</label>
              <input id="inurl" class="form-control ltr" name="url" placeholder="Enter url">
              <p id="eerrurl" class="mb-0 text-danger em"></p>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button id="updateBtn" type="button" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // make input fields RTL
    $("select[name='language_id']").on('change', function() {
        $(".request-loader").addClass("show");
        let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
        console.log(url);
        $.get(url, function(data) {
            $(".request-loader").removeClass("show");
            if (data == 1) {
                $("form.create input").each(function() {
                    if (!$(this).hasClass('ltr')) {
                        $(this).addClass('rtl');
                    }
                });
                $("form.create select").each(function() {
                    if (!$(this).hasClass('ltr')) {
                        $(this).addClass('rtl');
                    }
                });
                $("form.create textarea").each(function() {
                    if (!$(this).hasClass('ltr')) {
                        $(this).addClass('rtl');
                    }
                });
                $("form.create .nicEdit-main").each(function() {
                    $(this).addClass('rtl text-right');
                });

            } else {
                $("form.create input, form.create select, form.create textarea").removeClass('rtl');
                $("form.create .nicEdit-main").removeClass('rtl text-right');
            }
        })
    });
});
</script>
<script>
  // let table;
  $(document).ready(function() {
      var table = $('#ulink_section').DataTable({
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
              url: "{{ route('admin.ulink.index') }}",
              type: "GET"

          },
          columns: [{
                  data: 'sr_no',
                  name: 'sr_no',
                  orderable: false,
                  searchable: true
              },
              {
                  data: 'name',
                  name: 'name',
                  searchable: true
              },
              {
                  data: 'url',
                  name: 'url'
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
      $('#ulink_section').on('click', '.deletebutton', function(e) {
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
                      url: "{{ route('admin.ulink.delete') }}",
                      type: "POST",
                      data: {
                          _token: "{{ csrf_token() }}",
                          ulink_id: clientId
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
<script>
  $(document).ready(function () {
    $(document).on("click", ".editButton", function () {
        let ulink_id = $(this).data("id");
        let name = $(this).data("name");
        let url = $(this).data("url");

        // Populate modal fields
        $("#inulink_id").val(ulink_id);
        $("#inname").val(name);
        $("#inurl").val(url);

        // Show modal
        $("#editModal").modal("show");
    });

    // Submit the form when "Save Changes" is clicked
    $("#updateBtn").on("click", function () {
        $("#ajaxEditForm").submit();
    });
});

</script>
@endsection
