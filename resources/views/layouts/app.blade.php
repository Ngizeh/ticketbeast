<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticket Beast</title>
    <link rel="icon" href="data:;base64,=">
    <link rel="stylesheet" href="/css/app.css">
    <script>
        var stripeKey = "{{ config('services.stripe.key') }}"
    </script>
</head>
<body class="bg-gray-100">
 <div id="app">
     @yield('content')
 </div>
 <script src="https://checkout.stripe.com/checkout.js"></script>
 <script src="/js/app.js"></script>
</body>
</html>
