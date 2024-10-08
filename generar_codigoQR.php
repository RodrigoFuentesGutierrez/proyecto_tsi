<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Código QR</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="img/logo.png" alt="Logo"> 
        </div>
    </header>
    
    <div class="container">
        <div class="contenido">
            <h1>Generador de Código QR</h1>
            <div class="qr">
                <div id="qrcode"></div>
            </div>
            <div class="button-container">
                <button id="generate" type="submit">Generar Código QR</button> 
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#generate').click(function(event) {
                event.preventDefault(); // Evita que el formulario recargue la página
                const text = 'https://www.google.com'; // URL fija para generar el código QR
                $('#qrcode').empty(); // Limpiar código QR previo
                $('#qrcode').qrcode({
                    text: text
                });
            });
        });
    </script>
</body>
</html>

