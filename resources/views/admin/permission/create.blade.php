<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Permission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ajaxForm" action="{{ route('admin.permission.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Permission Name **</label>
                        <input class="form-control" name="name" placeholder="Enter name">
                        <p id="errname" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label>Group Name **</label>
                        <input class="form-control" name="group_name" placeholder="Enter group name">
                        <p id="errgroupname" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label>Status **</label>
                        <select class="form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <p id="errstatus" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label>Is Basic **</label>
                        <select class="form-control" name="basic">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        <p id="errbasic" class="mb-0 text-danger em"></p>
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
