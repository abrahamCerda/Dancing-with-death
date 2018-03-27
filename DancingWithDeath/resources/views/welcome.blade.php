<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>Dancing with Death</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!--Styles-->
        <link href="{{asset('css/home.css')}}" rel="stylesheet">

    </head>
    <body id="screen-container">
        <div id="app" class="center">
            <h1 class="title">
                Do you wanna dance with the <span class="red">Death?</span>
            </h1>
            <p class="description">
                Here you can schedule your final dance. Be careful with what you want, because you may never return from the appointment...
            </p>
            <appointment>

            </appointment>
        </div>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
