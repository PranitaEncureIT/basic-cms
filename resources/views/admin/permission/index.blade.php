@extends('admin.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">Permissions</h4>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">Permissions</div>
                    <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#createModal">
                        <i class="fas fa-plus"></i> Add Permission
                    </a>
                </div>
                <div class="card-body">
                    @if ($permissions->isEmpty())
                        <h3 class="text-center">NO PERMISSION FOUND</h3>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped mt-3" id="basic-datatables">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Permission</th>
                                        <th>Group Name</th>
                                        <th>Status</th>
                                        <th>Is Basic</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $key => $permission)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $permission->name }}</td>
                                            <td>{{ $permission->group_name }}</td>
                                            <td>
                                                @if ($permission->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($permission->basic == 1)
                                                    <span class="badge badge-primary">Basic</span>
                                                @else
                                                    <span class="badge badge-secondary">Not Basic</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-secondary btn-sm edit-permission-btn"
                                                    data-toggle="modal" data-target="#editModal"
                                                    data-permission_id="{{ $permission->id }}"
                                                    data-name="{{ $permission->name }}"
                                                    data-group_name="{{ $permission->group_name }}"
                                                    data-status="{{ $permission->status }}"
                                                    data-basic="{{ $permission->basic }}">
                                                    Edit
                                                </button>

                                                <form class="delete-permission-form d-inline-block"
                                                    action="{{ route('admin.permission.delete') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="permission_id"
                                                        value="{{ $permission->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm confirm-delete-btn">
                                                        <i class="fas fa-trash"></i> Delete
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

    @includeif('admin.permission.create')
    @includeif('admin.permission.edit')

    <!-- Include jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '.confirm-delete-btn', function(e) {
                e.preventDefault();

                let form = $(this).closest(".delete-permission-form");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

@endsection
