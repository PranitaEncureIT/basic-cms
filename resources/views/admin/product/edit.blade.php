@extends('admin.layout')

@if(!empty($data->language) && $data->language->rtl == 1)
@section('styles')
<style>
    form input,
    form textarea,
    form select {
        direction: rtl;
    }
    .nicEdit-main {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Product</h4>
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
            <a href="#">Shop Management</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Manage Products</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Products</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Edit Product</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title d-inline-block">Edit Product</div>
                <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.product.index') . '?language=' . request()->input('language')}}">
                    <span class="btn-label">
                        <i class="fas fa-backward" style="font-size: 12px;"></i>
                    </span>
                    Back
                </a>
            </div>
            <div class="card-body pt-5 pb-5">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">

                    {{-- Featured image upload end --}}
                    <form id="ajaxForm" class="" action="{{route('admin.product.update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden"  name="product_id" value="{{$data->id}}">

                        {{-- START: Featured Image --}}
                        <div class="form-group">
                            <label for="">Featured Image ** </label>
                            <br>
                            @php
                                $imagePath = public_path('assets/front/img/product/featured/' . $data->feature_image);
                            @endphp
                            <div class="thumb-preview" id="thumbPreview1">

                                @if (file_exists($imagePath) && !empty($data->feature_image))
                                    <img src="{{ asset('assets/front/img/product/featured/' . $data->feature_image) }}" alt="Feature Image">
                                @else
                                    <img src="{{ asset('assets/front/img/no_image.jpg') }}" alt="Image">
                                @endif
                            </div>
                            <br>
                            <br>

                            <input id="fileInput1" type="file" name="featured_image" accept="image/*"
                            style="display: none;">
                            <!-- Label acting as the button triggers the file input -->
                            <label for="fileInput1" class="choose-image btn btn-primary">Choose Image</label>


                            <p class="text-warning mb-0">JPG, PNG, JPEG images are allowed</p>
                            <p id="errfeatured_image" class="mb-0 text-danger em"></p>
                        </div>
                        {{-- END: Featured Image --}}

                        {{-- START: slider Part --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Slider Images ** </label>
                                    <br>

                               
                                    <div class="slider-thumbs" id="sliderThumbs2">

                                    </div>

                                    <input id="fileInput2" type="file" name="slider" value="" accept="image/*"
                                    style="display: none;" />
                                    <!-- Label acting as the button triggers the file input -->
                                    <label for="fileInput1" class="choose-image btn btn-primary">Choose Image</label>
                                    
                                    <p class="text-warning mb-0">JPG, PNG, JPEG images are allowed</p>
                                    <p id="errslider" class="mb-0 text-danger em"></p>

                                </div>
                            </div>
                        </div>
                        {{-- END: slider Part --}}

                        <div class="row">

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Status **</label>
                                    <select class="form-control ltr" name="status">
                                        <option value="" selected disabled>Select a status</option>
                                        <option value="1" {{$data->status == 1 ? 'selected' : ''}}>Show</option>
                                        <option value="0" {{$data->status == 0 ? 'selected' : ''}}>Hide</option>
                                    </select>
                                    <p id="errstatus" class="mb-0 text-danger em"></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Title **</label>
                                    <input type="text" class="form-control" name="title"  placeholder="Enter title" value="{{$data->title}}">
                                    <p id="errtitle" class="mb-0 text-danger em"></p>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="category">Category **</label>
                                    <select  class="form-control categoryData" name="category_id" id="category">
                                        <option value="" selected disabled>Select a category</option>
                                        @foreach ($categories as $categroy)
                                        <option value="{{$categroy->id}}" {{$data->category_id == $categroy->id ? 'selected' : ''}}>{{$categroy->name}}</option>
                                        @endforeach
                                    </select>
                                    <p id="errcategory_id" class="mb-0 text-danger em"></p>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                @if ($data->type == 'physical')
                                <div class="form-group">
                                    <label for="">Stock Product **</label>
                                    <input type="number" class="form-control ltr" name="stock"  placeholder="Enter Product Stock" value="{{$data->stock}}">
                                    <p id="errstock" class="mb-0 text-danger em"></p>
                                </div>
                                @endif

                                @if ($data->type == 'digital')
                                <div class="form-group">
                                    <label for="">Type **</label>
                                    <select name="file_type" class="form-control" id="fileType" onchange="toggleFileUpload();">
                                        <option value="upload" {{!empty($data->download_file) ? 'selected' : ''}}>File Upload</option>
                                        <option value="link" {{!empty($data->download_link) ? 'selected' : ''}}>File Download Link</option>
                                    </select>
                                    <p id="errfile_type" class="mb-0 text-danger em"></p>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if ($data->type == 'digital')
                        <div class="row">
                            <div class="col-12">
                                <div id="downloadFile" class="form-group">
                                    <label for="">Downloadable File **</label>
                                    <br>
                                    <input name="download_file" type="file">
                                    <p class="mb-0 text-warning">Only zip file is allowed.</p>
                                    <p id="errdownload_file" class="mb-0 text-danger em"></p>
                                </div>
                                <div id="downloadLink" class="form-group" style="display: none">
                                    <label for="">Downloadable Link **</label>
                                    <input name="download_link" type="text" class="form-control" value="{{$data->download_link}}">
                                    <p id="errdownload_link" class="mb-0 text-danger em"></p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            @if ($data->type == 'physical')
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for=""> Product Sku **</label>
                                    <input type="text" class="form-control ltr" name="sku"  placeholder="Enter Product sku" value="{{$data->sku}}">
                                    <p id="errsku" class="mb-0 text-danger em"></p>
                                </div>
                            </div>
                            @endif
                            <div class="{{$data->type == 'physical' ? 'col-lg-6' : 'col-lg-12'}}">
                                <div class="form-group">
                                    <label for="">Tags </label>
                                    <input type="text" class="form-control" name="tags" value="{{$data->tags}}" data-role="tagsinput" placeholder="Enter tags">
                                    <p id="errtags" class="mb-0 text-danger em"></p>
                                </div>
                            </div>
                        </div>

                        @if ($bex->catalog_mode == 0)

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for=""> Current Price (in {{$abx->base_currency_text}}) **</label>
                                        <input type="number" class="form-control ltr" name="current_price" value="{{$data->current_price}}"  placeholder="Enter Current Price">
                                        <p id="errcurrent_price" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Previous Price (in {{$abx->base_currency_text}})</label>
                                        <input type="number" class="form-control ltr" name="previous_price" value="{{$data->previous_price}}" placeholder="Enter Previous Price">
                                        <p id="errprevious_price" class="mb-0 text-danger em"></p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="summary">Summary</label>
                                    <textarea name="summary" id="summary" class="form-control" rows="4" placeholder="Enter Product Summary">{{$data->summary}}</textarea>
                                    <p id="errsubmission_date" class="mb-0 text-danger em"></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">Description</label>
                                    <textarea class="form-control summernote" name="description" placeholder="Enter description" data-height="300">{{replaceBaseUrl($data->description)}}</textarea>
                                    <p id="errdescription" class="mb-0 text-danger em"></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Meta Keywords</label>
                                    <input class="form-control" name="meta_keywords" value="{{$data->meta_keywords}}" placeholder="Enter meta keywords" data-role="tagsinput">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Meta Description</label>
                                    <textarea class="form-control" name="meta_description" rows="5" placeholder="Enter meta description">{{$data->meta_description}}</textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="form">
                <div class="form-group from-show-notify row">
                    <div class="col-12 text-center">
                        <button type="submit" id="submitBtn" class="btn btn-success">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
    <!-- JavaScript for image preview -->
    <script>
        document.getElementById('fileInput1').addEventListener('change', function(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('thumbPreview1').innerHTML =
                        '<img src="' + e.target.result + '" alt="Image">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
@endsection


@section('scripts')

@if($data->type == 'digital')
<script>
    function toggleFileUpload() {
        let type = $("select[name='file_type']").val();
        if (type == 'link') {
            $("#downloadFile input").attr('disabled', true);
            $("#downloadFile").hide();
            $("#downloadLink").show();
            $("#downloadLink input").removeAttr('disabled');
        } else {
            $("#downloadLink input").attr('disabled', true);
            $("#downloadLink").hide();
            $("#downloadFile").show();
            $("#downloadFile input").removeAttr('disabled');
        }
    }

    $(document).ready(function() {
        toggleFileUpload();
    });
</script>
@endif

{{-- dropzone --}}
<script>
    // myDropzone is the configuration for the element that has an id attribute
    // with the value my-dropzone (or myDropzone)
    Dropzone.options.myDropzone = {
        acceptedFiles: '.png, .jpg, .jpeg',
        url: "",
        success : function(file, response){
            console.log(response.file_id);

            // Create the remove button
            var removeButton = Dropzone.createElement("<button class='rmv-btn'><i class='fa fa-times'></i></button>");


            // Capture the Dropzone instance as closure.
            var _this = this;

            // Listen to the click event
            removeButton.addEventListener("click", function(e) {
                // Make sure the button click doesn't submit the form:
                e.preventDefault();
                e.stopPropagation();

                _this.removeFile(file);

                rmvimg(response.file_id);
            });

            // Add the button to the file preview element.
            file.previewElement.appendChild(removeButton);

            var content = {};

            content.message = 'Slider images added successfully!';
            content.title = 'Success';
            content.icon = 'fa fa-bell';

            $.notify(content,{
                type: 'success',
                placement: {
                    from: 'top',
                    align: 'right'
                },
                time: 1000,
                delay: 0,
            });
        }
    };

    function rmvimg(fileid) {
        // If you want to the delete the file on the server as well,
        // you can do the AJAX request here.

        $.ajax({
            url: "",
            type: 'POST',
            data: {
                _token: "{{csrf_token()}}",
                fileid: fileid
            },
            success: function(data) {
                var content = {};

                content.message = 'Slider image deleted successfully!';
                content.title = 'Success';
                content.icon = 'fa fa-bell';

                $.notify(content,{
                    type: 'success',
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                    time: 1000,
                    delay: 0,
                });
            }
        });

    }
</script>


<script>
    var el = 0;

    $(document).ready(function(){
        $.get("{{route('admin.product.images', $data->id)}}", function(data){
            for (var i = 0; i < data.length; i++) {
                $("#imgtable").append('<tr class="trdb" id="trdb'+data[i].id+'"><td><div class="thumbnail"><img style="width:150px;" src="{{asset('assets/front/img/product/sliders/')}}/'+data[i].image+'" alt="Ad Image"></div></td><td><button type="button" class="btn btn-danger pull-right rmvbtndb" onclick="rmvdbimg('+data[i].id+')"><i class="fa fa-times"></i></button></td></tr>');
            }
        });
    });

    function rmvdbimg(indb) {
        $(".request-loader").addClass("show");
        $.ajax({
            url: "",
            type: 'POST',
            data: {
                _token: "{{csrf_token()}}",
                fileid: indb
            },
            success: function(data) {
                $(".request-loader").removeClass("show");
                $("#trdb"+indb).remove();
                var content = {};

                content.message = 'Slider image deleted successfully!';
                content.title = 'Success';
                content.icon = 'fa fa-bell';

                $.notify(content,{
                    type: 'success',
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                    time: 1000,
                    delay: 0,
                });
            }
        });

    }


</script>

@endsection
