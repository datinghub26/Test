<!DOCTYPE html>
<html lang="en" class="bg-dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.cdnfonts.com/css/helvetica-neue-55" rel="stylesheet">

        <style>
            :root {
                --bs-primary: #1ebe73;
                --bs-secondary: #55657e;
                --bs-dark: #223043f2;
                --bs-base: #161f2c;
                --bs-card-bg: #161f2c;
                --bs-headings-color: #1ebe73;
                --bs-body-color: #93acd3;
                --bs-body-bg: #0d131c;
            }

            html, body {
                background-color: var(--bs-body-bg);
                color: var(--bs-secondary);
                font-family: 'Helvetica Neue', sans-serif !important;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }
        </style>
    </head>
    <body>
        <div class="container text-center" style="padding-top: 100px">
            <h4>Whoops, that page is gone.</h4>
            <h6>@yield('code') | @yield('message')</h6>

            <div class="">
                <svg width="200px" height="200px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.20164 18.4695L10.1643 4.00506C10.9021 2.66498 13.0979 2.66498 13.8357 4.00506L21.7984 18.4695C22.4443 19.6428 21.4598 21 19.9627 21H4.0373C2.54022 21 1.55571 19.6428 2.20164 18.4695Z"
                          stroke="var(--bs-secondary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 9V13" stroke="var(--bs-secondary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 17.0195V17" stroke="var(--bs-secondary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <a href="/" class="btn btn-secondary mt-4 ">Home</a>
        </div>

        <!-- Bootstrap JS (Optional if you plan to use Bootstrap's interactive components) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
