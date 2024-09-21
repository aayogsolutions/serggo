<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>demo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

</head>

<body>
    <form action="{{ route('signup') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" name="email" id="">
        <input type="text" name="password" id="">
        <input type="file" name="image" id="">
        <button type="submit">
            submit
        </button>
    </form>
    <button>
        <a href="{{ route('socialSignUp','google') }}">Google</a>
    </button>
    <button>
        <a href="{{ route('socialSignUp','facebook') }}">Facebook</a>
    </button>
</body>

</html>