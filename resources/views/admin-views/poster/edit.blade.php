@extends('layouts.back-end.app')

@section('title', translate('Blog'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h2 class="h1 mb-1 text-capitalize d-flex align-items-center gap-2">
                    <img width="20" src="{{asset('/public/assets/back-end/img/banner.png')}}" alt="">
                    {{translate('blog_update_form')}}
                </h2>
            </div>
            <div>
                <a class="btn btn--primary text-white" href="{{ route('admin.poster.list') }}">
                    <i class="tio-chevron-left"></i> {{ translate('back') }}</a>
            </div>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.poster.update',[$banner['id']])}}" method="post" enctype="multipart/form-data"
                              class="banner_form">
                            @csrf
                            @method('put')
                            <div class="row g-3">
                                <div class="col-md-6">
                                  
                                    <div class="form-group mb-3">
                                        <label for="name" class="title-color text-capitalize">{{ translate('blog_Category') }}</label>
                                        <select name="blog_category" id="" class="form-control" required>
                                            <option value="">----Select Category----</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->name }}" 
                                                    {{ $banner->blog_category == $category->name ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" id="id" name="id">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="name" class="title-color text-capitalize">{{ translate('blog_title')}}</label>
                                        <input type="text" name="title" class="form-control" id="url" required placeholder="{{ translate('enter_title') }}" value="{{$banner['title']}}">
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="details" class="title-color text-capitalize">{{ translate('blog_details')}}</label>
                                            <textarea name="details" class="form-control" id="url" required placeholder="{{ translate('Enter_poster_details') }}">{{$banner['details']}}</textarea>
                                        </div>
    
                                    </div> --}}
                                    

                                    {{-- For Theme Fashion - New input Field - Start --}}
                                    {{-- @if(theme_root_path() == 'theme_fashion')
                                    <div class="form-group mt-4 input_field_for_main_banner {{$banner['banner_type'] !='Main Banner'?'d-none':''}}">
                                        <label for="button_text" class="title-color text-capitalize">{{ translate('Button_Text')}}</label>
                                        <input type="text" name="button_text" class="form-control" id="button_text" placeholder="{{ translate('Enter_button_text') }}" value="{{$banner['button_text']}}">
                                    </div>
                                    <div class="form-group mt-4 mb-0 input_field_for_main_banner {{$banner['banner_type'] !='Main Banner'?'d-none':''}}">
                                        <label for="background_color" class="title-color text-capitalize">{{ translate('background_color')}}</label>
                                        <input type="color" name="background_color" class="form-control form-control_color w-100" id="background_color" value="{{$banner['background_color']}}">
                                    </div>
                                    @endif --}}
                                    {{-- For Theme Fashion - New input Field - End --}}

                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-6">
                                        <label for="details" class="title-color text-capitalize">{{ translate('blog_details') }}</label>
                                        <textarea name="details" class="textarea editor-textarea" required placeholder="{{ translate('Enter_poster_details') }}">{{$banner['details']}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex flex-column justify-content-center">
                                    <div>
                                        <center class="mx-auto">
                                            <div class="uploadDnD">
                                                <div class="form-group inputDnD input_image input_image_edit rounded-lg" style="background-image: url('{{asset('storage/app/public/poster')}}/{{$banner['image']}}')">
                                                    <input type="file" name="image" class="form-control-file text--primary font-weight-bold" onchange="readUrl(this)"
                                                      data-title="{{ file_exists('storage/app/public/poster/'.$banner['image']) ? '': 'Drag and drop file or Browse file'}}" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                </div>
                                            </div>
                                        </center>
                                        <label for="name" class="title-color text-capitalize">
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="right" title="" data-original-title="{{translate('banner_image_ratio_is_not_same_for_all_sections_in_website').' '.translate('Please_review_the_ratio_before_upload')}}">
                                                <img width="16" src={{asset('public/assets/back-end/img/info-circle.svg')}} alt="" class="m-1">
                                            </span>
                                            {{ translate('banner_image')}}
                                        </label>
                                        <span class="text-info" id="theme_ratio">( {{translate('ratio')}} 4:1 )</span>
                                        <p>{{ translate('blog_Image_ratio_is_not_same_for_all_sections_in_website') }}. {{ translate('please_review_the_ratio_before_upload') }}</p>

                                         <!-- For Theme Fashion - New input Field - Start -->
                                         @if(theme_root_path() == 'theme_fashion')
                                         <div class="form-group mt-4 input_field_for_main_banner {{$banner['banner_type'] !='Main Banner'?'d-none':''}}">
                                             <label for="title" class="title-color text-capitalize">{{ translate('Title')}}</label>
                                             <input type="text" name="title" class="form-control" id="title" placeholder="{{ translate('Enter_blog_title') }}" value="{{$banner['title']}}">
                                         </div>
                                         <div class="form-group mb-0 input_field_for_main_banner {{$banner['banner_type'] !='Main Banner'?'d-none':''}}">
                                             <label for="sub_title" class="title-color text-capitalize">{{ translate('Sub_Title')}}</label>
                                             <input type="text" name="sub_title" class="form-control" id="sub_title" placeholder="{{ translate('Enter_blog_sub_title') }}" value="{{$banner['sub_title']}}">
                                         </div>
                                         @endif
                                         <!--  For Theme Fashion - New input Field - End -->

                                    </div>
                                </div>

                                <div class="col-md-12 d-flex justify-content-end gap-3">
                                    <button type="reset" class="btn btn-secondary px-4">{{ translate('reset')}}</button>
                                    <button type="submit" class="btn btn--primary px-4">{{ translate('update')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('ready', function () {
            theme_wise_ration();
        });

        function theme_wise_ration(){
            let banner_type = $('#banner_type_select').val();
            let theme = '{{ theme_root_path() }}';
            let theme_ratio = {!! json_encode(THEME_RATIO) !!};
            let get_ratio= theme_ratio[theme][banner_type];

            $('#theme_ratio').text(get_ratio);
        }

        $('#banner_type_select').on('change',function(){
            theme_wise_ration();
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            // dir: "rtl",
            width: 'resolve'
        });

        function display_data(data) {

            $('#resource-product').hide()
            $('#resource-brand').hide()
            $('#resource-category').hide()
            $('#resource-shop').hide()

            if (data === 'product') {
                $('#resource-product').show()
            } else if (data === 'brand') {
                $('#resource-brand').show()
            } else if (data === 'category') {
                $('#resource-category').show()
            } else if (data === 'shop') {
                $('#resource-shop').show()
            }
        }
    </script>

    <script>
        function mbimagereadURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#mbImageviewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#mbimageFileUploader").change(function () {
            mbimagereadURL(this);
        });

        function readUrl(input) {
            if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = (e) => {
                let imgData = e.target.result;
                let imgName = input.files[0].name;
                input.setAttribute("data-title", "");
                let img = new Image();
                img.onload = function() {
                    let imgWidth = img.naturalWidth;
                    let imgHeight = img.naturalHeight;

                    if(imgWidth > 700){
                        imgWidth = 700;
                    }
                    $('.input_image').css({
                        "background-image": `url('${imgData}')`,
                        "width": "100%",
                        "height": "auto",
                        backgroundPosition: "center",
                        backgroundSize: "contain",
                        backgroundRepeat: "no-repeat",
                    });
                };
                img.src = imgData;
            }
            reader.readAsDataURL(input.files[0]);
        }
        }
    </script>

    <!-- New Added JS - Start -->
    <script>
        $('#banner_type_select').on('change',function(){
            let input_value = $(this).val();

            if (input_value == "Main Banner") {
                $('.input_field_for_main_banner').removeClass('d-none');
            } else {
                $('.input_field_for_main_banner').addClass('d-none');
            }
        });
    </script>
    <!-- New Added JS - End -->

    <!-- Include CKEditor 5 (latest version) -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<!-- Initialize CKEditor 5 -->
<script>
    ClassicEditor
        .create(document.querySelector('.editor-textarea'))
        .catch(error => {
            console.error(error);
        });
</script>
@endpush
