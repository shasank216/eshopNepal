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
            color: #000;
            /* Change to black */
            font-size: 30px;
        }

        /* Blog Paragraph */
        .blog_detail_paragraph p {
            font-weight: 400;
            font-size: 18px;
            letter-spacing: 0.005em;
            color: #727272;
            /* Change to black */
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
            border: 1px solid #202741;
            border-radius: 10px;
            align-self: flex-start;
            max-height: 67.1vh;
            overflow-y: auto;
            position: relative;
            margin-top: 8.5rem !important;
            background-color: #fff;
            /* Change to white */
            color: #000;
            /* Change to black */
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
            font-size: 34px;
            font-weight: 600;
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
            font-size: 1.2rem;
            color: #000;
            /* Change to black */
        }

        .card-text .small {
            font-size: 14px;
            font-weight: 400;
            color: #727272;
            /* Change to black */
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
            .latest-blogs-responsive{
                display: flex;
                gap: 5px;
            }
        }
        @media (max-width: 450px){
            .latest_blog_image{
                height: 70%;
            }


        }
    </style>

    <div class="bg-white text-dark">
        <div class="container mt-4 padding-rl">
            <div class="row">
                <div class="col-lg-8 col-md-12 padding-rl">
                    <h2 class="font-weight-bold blog_title">{{ ucwords($blogs_details->title) }}</h2>
                    <div class="d-flex justify-content-between mb-3 author_and_admin">
                        <div>Author: {{ $blogs_details->added_by }}</div>
                        <div class="text-right">Date: {{ $blogs_details->created_at->format('Y-m-d') }}</div>
                    </div>
                    <div>
                        <img class="img-fluid blog_detail_image mb-4"
                            src="{{ asset('storage/app/public/poster') }}/{{ $blogs_details['image'] }}" alt="Blog Image">
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

                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
