@extends('admin.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">
            Registered Users
        </h4>
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
                <a href="#">Registered Users</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-title">
                                Registered Users
                            </div>
                        </div>
                        {{-- <div class="col-6 mt-2 mt-lg-0">
                            <button class="btn btn-danger float-right btn-sm ml-2 mt-1 d-none bulk-delete"
                                data-href="{{ route('register.user.bulk.delete') }}"><i class="flaticon-interface-5"></i>
                                Delete</button>
                            <form action="{{ url()->full() }}" class="float-right">
                                <input type="text" name="term" class="form-control"
                                    value="{{ request()->input('term') }}" placeholder="Search by Username / Email"
                                    style="min-width: 250px;">
                            </form>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            {{-- @if (count($users) == 0)
                                <h3 class="text-center">NO USER FOUND</h3>
                            @else 
                            @endif--}}
                                <div class="table-responsive">
                                    <table id="user_list" class="table table-striped table-row-bordered gy-5 gs-7 mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col"> Name</th>
                                                <th scope="col"> Email</th>
                                                <th scope="col"> Number</th>
                                                <th scope="col"> Email Status</th>
                                                <th scope="col"> Account</th>
                                                <th scope="col"> Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="d-inline-block mx-auto">
                            {{-- {{ $users->appends(['term' => request()->input('term')])->links() }} --}}
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

    <script>
        var table = $('#user_list').DataTable({
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
                url: "{{ route('admin.register.user') }}",
                type: "GET"
                // data: function (d) {
                //     d.name = $('#search_name').val();
                // }
            },
            columns: [
        {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
        {data: 'username', name: 'username', searchable: true},
        {data: 'email', name: 'email'},
        {data: 'number', name: 'number'},
        {data: 'email_verified', name: 'email_verified', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
        });
    </script>
@endsection
