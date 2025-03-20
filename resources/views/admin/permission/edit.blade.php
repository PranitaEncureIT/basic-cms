<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editPermissionModalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Permission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ajaxEditPermissionForm" action="{{ route('admin.permission.update') }}" method="POST">
                    @csrf
                    <input type="hidden" id="editPermissionId" name="permission_id">

                    <div class="form-group">
                        <label for="editPermissionName">Name **</label>
                        <input id="editPermissionName" class="form-control" name="name" placeholder="Enter name">
                        <p class="text-danger em" id="error-name"></p>
                    </div>

                    <div class="form-group">
                        <label for="editPermissionGroup">Group Name **</label>
                        <input id="editPermissionGroup" class="form-control" name="group_name"
                            placeholder="Enter Group Name">
                        <p class="text-danger em" id="error-group_name"></p>
                    </div>

                    <!-- Status Dropdown -->
                    <div class="form-group">
                        <label for="editPermissionStatus">Status **</label>
                        <select id="editPermissionStatus" class="form-control ltr" name="status">
                            <option value="" selected disabled>Select a status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <p class="text-danger em" id="error-status"></p>
                    </div>

                    <!-- Is Basic Dropdown -->
                    <div class="form-group">
                        <label for="editPermissionIsBasic">Is Basic **</label>
                        <select id="editPermissionIsBasic" class="form-control ltr" name="basic">
                            <option value="" selected disabled>Select type</option>
                            <option value="1">Basic</option>
                            <option value="0">Not Basic</option>
                        </select>
                        <p class="text-danger em" id="error-basic"></p>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="updatePermissionBtn" type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>


@section('scripts')
    <script>
        $(document).ready(function() {

            // Prefill the edit modal when clicking the edit button
            $(document).on('click', '.edit-permission-btn', function() {
                let data = $(this).data();
                console.log("Edit Data:", data);

                $("#editPermissionId").val(data.permission_id);
                $("#editPermissionName").val(data.name);
                $("#editPermissionGroup").val(data.group_name);

                // Set the status dropdown
                $("#editPermissionStatus").val(data.status);

                // Set the basic dropdown
                $("#editPermissionIsBasic").val(data.basic);
            });

            // Handle update form submission via AJAX

            $("#updatePermissionBtn").on('click', function(e) {
                $(".request-loader").addClass("show"); // Show loader

                let form = document.getElementById('ajaxEditPermissionForm');
                let formData = new FormData(form);
                let url = $("#ajaxEditPermissionForm").attr('action');
                let method = $("#ajaxEditPermissionForm").attr('method');

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log("Server Response:", response);

                        $(".request-loader").removeClass("show"); // Hide loader

                        $(".em").html(""); // Clear any previous errors

                        if (response === "success") {
                            location.reload();
                        } else if (response.error) {
                            for (let field in response) {
                                if (field !== 'error') {
                                    $("#error-" + field).html(response[field][0]);
                                }
                            }
                        }
                    },
                    error: function(error) {
                        console.log("AJAX Error:", error);
                        $(".request-loader").removeClass("show"); // Hide loader

                        $(".em").html(""); // Clear previous errors
                        for (let field in error.responseJSON.errors) {
                            $("#error-" + field).html(error.responseJSON.errors[field][0]);
                        }
                        $(e.target).attr('disabled', false); // Re-enable button
                    }
                });
            });
        });
    </script>
@endsection
