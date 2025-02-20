<!DOCTYPE html>
<html>
<head>
    <title>Su carrito</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<section>
    <div class="product">
        <img src="https://i.imgur.com/EHyR2nP.png" alt="The cover of Stubborn Attachments" />
        <div class="description">
            <h3>Ejemplo de producto</h3>
            <h5>10,00€</h5>
        </div>
    </div>
    <form action="{{url('/api/crear-sesion-pago')}}" method="POST">
        @csrf
        <input type="hidden" name="price_id" value="tu_price_id_de_stripe">
        <button type="submit" id="checkout-button">Pagar</button>
        <a href="{{ route('inicio') }}" class="mt-6 inline-block px-6 py-3 text-2xl font-semibold text-gray-900 bg-[#BFF205] rounded-lg hover:bg-[#A8D004] dark:bg-[#BFF205] dark:hover:bg-[#A8D004]">Volver a la página de inicio</a>
    </form>
</section>
</body>
</html>
