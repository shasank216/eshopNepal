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
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scrollbar-width: thin;
            scrollbar-color: #000000;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: white;
            border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #f39c12;
            border-radius: 8px;
            border: 1px solid #000000;
        }

        /* Body */
        body {
            background-color: #fff;
            /* Change to white */
            color: #000;
            /* Change to black */
        }

        /* Blog Title */
        .blog_title {
            font-size: 26px;
            font-weight: 600;
            line-height: 36px;
            text-transform: capitalize;
            color: #593b7b;
        }

        /* Blog Paragraph */
        .blog_detail_paragraph p {
            font-size: 15px;
            font-weight: 400;
            line-height: 24px;
            letter-spacing: 0.005em;
            color: #727272;
        }

        /* Author and Date */
        .author_and_admin {
            color: #727272;
            /* Change to black */
            font-size: 15px;
            font-weight: 400;

        }

        /* Sidebar */
        .sideBar {
            border: 1px solid #1f3c74;
            border-radius: 10px;
            align-self: flex-start;
            max-height: 67.1vh;
            overflow-y: auto;
            position: relative;
            margin-top: 4%;
            background-color: #fff;
            color: #000;
        }

        .sidebar-header {
            background-color: #fff;
            /* Change to white */
            color: #000;
            /* Change to black */
            padding: 5px;
            position: sticky;
            top: 0;
            z-index: 9;
        }

        .sidebar-header h1 {
            font-size: 26px;
            font-weight: 600;
            line-height: 36px;
            text-transform: capitalize;
            color: #593b7b;
        }

        .list-group-container {
            background-color: #fff;
            /* Change to white */
            color: #000;
            /* Change to black */
        }

        /* Sidebar Item */
        .list-group-item.card-item {
            background-color: #fff;
            /* Change to white */
            color: #000;
            /* Change to black */
        }

        .card-text strong {
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            text-transform: capitalize;
            color: #593b7b;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-text .small {
            font-size: 13px;
            font-weight: 400;
            line-height: 20px;
            letter-spacing: 0.005em;
            color: #727272;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Latest Blog Category Button */
        .latest_blog_category {
            color: #000;
            /* Change to black */
            border-color: #fcbe11;
            border-radius: 50px;
        }

        .latest_blog_category:hover {
            color: #fff;
            background-color: #000;
            /* Change to black on hover */
        }

        .padding-rl {
            padding: 0 3%;
        }

        .blog_detail_image {
            width: 100%;
            height: 400px;
        }

        .text-sm {
            font-size: 14px;
            font-weight: 500;
            line-height: 24px;
            letter-spacing: 0.005em;
            color: #454545;
        }

        img.latest_blog_image {
            height: 183px;
            width: 100%;
        }

        .blog-category {
            font-size: 16px;
            font-weight: 500;
            line-height: 24px;
            letter-spacing: 0.005em;
            color: #454545;
        }


        /* Responsive Styles */
        @media (max-width: 992px) {
            .blog_title {
                font-size: 1.75rem;
            }

            .blog_detail_paragraph {
                font-size: 0.9rem;
            }

            .sideBar {
                margin: 1.5rem !important;
            }

            .latest_blog_image {
                width: 100%;
                height: 100%;


            }

            .latest-blogs-responsive {
                display: flex;
                gap: 5px;
            }
        }

        @media (max-width: 450px) {
            .latest_blog_image {
                height: 70%;
            }


        }
    </style>

    <div class="bg-white text-dark">
        <div class="container mt-2 padding-rl">

            <div class="breadcrumb mb-4">
                <a href="{{ url('/') }}">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    Home
                </a>
                <i class="fa fa-angle-right" aria-hidden="true"></i>
                <span>
                    {{ ucwords($blogs_details->title) }}
                </span>
            </div>

            <div class="row">
                <div class="col-lg-8 col-md-12 padding-rl">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="font-weight-bold blog_title">{{ ucwords($blogs_details->title) }}</h2>
                        @if ($blogs_details->blog_category)
                            <p class="m-0 blog-category">
                                {{ $blogs_details->blog_category ?? '' }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <img class="img-fluid blog_detail_image"
                            src="{{ asset('storage/app/public/poster') }}/{{ $blogs_details['image'] }}" alt="Blog Image">
                    </div>
                    <div class="d-flex justify-content-between author_and_admin">
                        <div class="text-sm">
                            Author:
                            <i class="fa fa-user" aria-hidden="true"></i>
                            {{ $blogs_details->added_by }}
                        </div>
                        <div class="text-right text-sm">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            Date:
                            {{ $blogs_details->created_at->format('Y-m-d') }}
                        </div>
                    </div>

                    <div class="blog_detail_paragraph">
                        <p class="mt-3">
                            {{ strip_tags($blogs_details->details) }}
                        </p>
                    </div>
                </div>

                <!-- sideBar -->
                <div class="col-lg-4 col-md-12 sideBar">
                    <div class="sidebar-header">
                        <h1 class="text-center">Latest Blogs</h1>
                    </div>
                    <div class="list-group-container">
                        <div class="list-group list-group-flush border-bottom scrollarea">
                            @foreach ($blogs as $blog)
                            <a class="blog-title text-white mb-2" href="{{ route('blogDetailsView', ['id' => $blog]) }}">
                                <div class="card custom-card" style="border-radius: 15px;">
                                    <img src="{{ asset('storage/app/public/poster') }}/{{ $blog['image'] }}"
                                        class="card-img-top img-fluid" alt="{{ $blog->title }}" style="border-radius: 15px;">
                                    <div class="card-body blog-body h-100">
                                        @if ($blog->blog_category)
                                            <span class="badge badge-light category-badge">
                                                {{ $blog->blog_category ?? '' }}
                                            </span>
                                        @endif

                                        <div class="card-text mt-auto">
                                            <h5 class="blog-title text-white m-0">
                                                {{ $blog->title }}
                                            </h5>
                                            <p class="blog-created-by text-white">by {{ $blog->added_by }}
                                                {{ $blog->created_at->format('Y-m-d') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>

{{--                             
                                <a href="{{ route('blogDetailsView', ['id' => $blog]) }}"
                                    class="list-group-item list-group-item-action py-3 lh-tight card-item">
                                    <div class="latest-blogs-responsive">
                                        <div class="">
                                            <img class="latest_blog_image"
                                                src="{{ asset('storage/app/public/poster') }}/{{ $blog['image'] }}"
                                                alt="Blog Image">

                                        </div>

                                        <div class="card-text">
                                            <strong>{{ $blog->title }}</strong>
                                            <div class="small">
                                                {{ \Illuminate\Support\Str::words(strip_tags($blog->details), 20, '...') }}
                                            </div>
                                        </div>

                                    </div>

                                </a> --}}
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
