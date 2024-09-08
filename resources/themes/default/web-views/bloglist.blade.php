@extends('layouts.front-end.app')

@section('title', $web_config['name']->value . ' ' . translate('online_Shopping') . ' | ' . $web_config['name']->value .
    ' ' . translate('ecommerce'))

    @push('css_or_js')
        <meta property="og:image" content="{{ asset('storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
        <meta property="og:title" content="Welcome To {{ $web_config['name']->value }} Home" />
        <meta property="og:url" content="{{ env('APP_URL') }}">
        <meta property="og:description"
            content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">

        <meta property="twitter:card" content="{{ asset('storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
        <meta property="twitter:title" content="Welcome To {{ $web_config['name']->value }} Home" />
        <meta property="twitter:url" content="{{ env('APP_URL') }}">
        <meta property="twitter:description"
            content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">

        <link rel="stylesheet" href="{{ asset('public/assets/front-end/css/home.css') }}" />
        <link rel="stylesheet" href="{{ asset('public/assets/front-end/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/front-end/css/owl.theme.default.min.css') }}">
    @endpush

@section('content')
    <style>
        .custom-card {
            width: 100%;
            /* height: 300px; */
            border: none;
            position: relative;
            background-color: #D9D9D9;

            margin-bottom: 0;
        }

        .category-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 15px;
            background-color: #FFFFFF;
            color: #000;
        }

        .card-title {
            font-size: 26px;
            font-weight: 600;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0;
        }

        .card-subtitle {
            font-size: 14px;
            font-weight: 400;
            color: #FFFFFF;
        }
    </style>

    <section class="container">
        <!-- Blogs start -->

        <nav class="navbar">
            <p style="font-size: 34px; font-weight: 600;">All Blogs</p>

        </nav>
        <hr>

        <div class="container" style="margin-top: 40px;">
            <div class="row">
                <!-- First Row -->
                @if ($blogs->isEmpty())
                    <p>No blogs available.</p>
                @else
                    @foreach ($blogs as $blog)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card custom-card" style="border-radius: 0;">
                                <!-- Image -->
                                <img src="{{ asset('storage/app/public/poster/' . $blog['image']) }}"
                                    class="card-img-top img-fluid" alt="{{ $blog->title }}" style="border-radius: 0;">

                                <div class="card-body">
                                    <span class="badge badge-light category-badge">{{ $blog->title }}</span>
                                    <div class="card-text mt-auto">
                                        <a href="{{ route('blogDetailsView', ['id' => $blog]) }}">
                                            <h5 class="blog-title text-white">
                                                {{ strip_tags($blog->details) }}
                                            </h5>

                                        </a>

                                        <p class="blog-created-by text-white">by {{ $blog->added_by }}
                                            {{ $blog->created_at->format('Y-m-d') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- Pagination Links -->
                    <div class="col-12 d-flex justify-content-center">
                        {{ $blogs->links() }}
                    </div>
                @endif
            </div>
        </div>


    </section>


@endsection
