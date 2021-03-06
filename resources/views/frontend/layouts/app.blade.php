<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Real Estate') }}  @yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('public/frontend/css/materialize.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
	<link href="{{ asset('public/frontend/css/jquery.typeahead.css') }}" rel="stylesheet">
    @yield('styles')
    
    <link href="{{ asset('public/frontend/css/styles.css') }}" rel="stylesheet">
	<script type="text/javascript" src="{{ asset('public/frontend/js/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('public/frontend/js/jquery.typeahead.js') }}"></script>
</head>

    <body>
        
        {{-- MAIN NAVIGATION BAR --}}
        @include('frontend.partials.navbar')

        {{-- SLIDER SECTION --}}
        @if(Request::is('/'))
            @include('frontend.partials.slider')
        @endif

        {{-- SEARCH BAR --}}
			{{--@include('frontend.partials.search')--}}
		@include('frontend.partials.searchautocomplete')	
        
        {{-- MAIN CONTENT --}}
        <div class="main">
            @yield('content')
        </div>

        {{-- FOOTER --}}
        @include('frontend.partials.footer')


        <!--JavaScript at end of body for optimized loading-->
        {{-- <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script> --}}
        
        <script type="text/javascript" src="{{ asset('public/frontend/js/materialize.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		
        {!! Toastr::message() !!}
        <script>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error('{{ $error }}','Error',{
                        closeButtor: true,
                        progressBar: true 
                    });
                @endforeach
            @endif
        </script>

        @yield('scripts')

        <script>
        $(document).ready(function(){
            $('.sidenav').sidenav();

            $('.carousel.carousel-slider').carousel({
                fullWidth: true,
                indicators: true,
            });

            $('.carousel.testimonials').carousel({
                indicators: true,
            });

            var city_list =<?php echo json_encode($citylist);?>;
            $('input.autocomplete').autocomplete({
                data: city_list
            });

            $(".dropdown-trigger").dropdown({
                top: '65px'
            });

            $('.tooltipped').tooltip();

        });
        </script>

    </body>
  </html>