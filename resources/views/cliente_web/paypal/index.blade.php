<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Product:</title>
</head>
<body>
    <h1>Producto: </h1>
    <h3>Price: $20</h3>

    <form action="{{asset('paypal/payment')}}" method="post">
        @csrf
        <input type="text" name="price" value="20">
        <button type="submit">Pagar</button>
    </form>
    
</body>
</html>