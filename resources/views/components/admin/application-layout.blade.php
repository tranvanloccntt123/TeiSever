<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>
    <link rel="stylesheet" href="{{url("resources/css/admin.css")}}">
    <!--stylesheet-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
</head>
<body>
    <header>
        <div class="container mt-3 mb-3">
            <h1><a href="{{route('m.applications')}}"><i class='fas fa-arrow-left' style='font-size:45px; color: black'></i></a> <span style="margin-left: 15px; font-size: 45px;">ỨNG DỤNG</span></h1>
        </div>
    </header>
    <main>
        <div class="row" style="margin: 0; padding: 0">
            <div class="container" style="margin-top: 25px">
                @include('components.alter')
                {{ $slot }}
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>
</html>