<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Números de Hoy</title>
</head>
<body>

    <div class="fecha">
        <h3>{{ $fecha }}</h3>
    </div>

    @if ($mediodia_centena != '-')
        <div class="mediodia">
            <h3>-Mediodía (Centena: {{ $mediodia_centena }}, Fijo: {{ $mediodia_fijo }})</h3>
        </div>
    @endif

    @if ($noche_centena != '-')
        <div class="noche">
            <h3>-Noche (Centena: {{ $noche_centena }}, Fijo: {{ $noche_fijo }})</h3>
        </div>
    @endif

</body>
</html>