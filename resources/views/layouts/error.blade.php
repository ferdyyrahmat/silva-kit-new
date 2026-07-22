<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials.title-meta', ['title' => $title])
    @include('layouts.partials.head-css')
</head>

<body class="bg-primary-subtle bg-opacity-10">
    
    <!-- Begin page -->
    <div class="maintenance-pages">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-xl-12 align-self-center">
                    @yield('content')
                </div>
            </div>

        </div>
    </div>

</body>

</html>