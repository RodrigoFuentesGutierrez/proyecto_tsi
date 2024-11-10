<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Exportación</title>
    <script type="text/javascript">
        // Función para validar que los campos estén completos antes de enviar el formulario
        function validarFormulario() {
            // Obtener los valores de los campos
            var mes = document.getElementById("mes").value;
            var anio = document.getElementById("anio").value;
            var fecha = document.getElementById("fecha").value;
            var anios = document.getElementById("anios").value;

            // Verificar si todos los campos (mes, año, fecha, anio) están vacíos
            if (mes == "" && anio == "" && fecha == "" && anios == "") {
                alert("Por favor, selecciona al menos un mes, año, fecha o año.");
                return false; // Impide el envío del formulario
            }

            return true; // Permite el envío del formulario si no están todos vacíos
        }
    </script>

    <style>
        /* Estilo general de la página */
        body {
            font-family: Arial, sans-serif;
            background-color: #F0F4FF;
            margin: 0;
            padding: 0;
        }

        /* Contenedor principal */
        .container {
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
            margin-top: 150px;
            border-radius: 12px;
            width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilo de los inputs y selects */
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        /* Estilo para los campos en línea */
        .input-group {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 15px;
        }

        /* Botón de envío */
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            width: 100%;
            margin-top: 15px;
        }

        /* Títulos y etiquetas */
        label {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        /* Estilo para la línea de mes y año */
        .mes-anio-group {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            width: 100%;
        }

        /* Estilo para la línea de fecha y año */
        .fecha-anios-group {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            width: 100%;
        }

        /* Ajustar el tamaño de los inputs para que estén al mismo nivel */
        .mes-anio-group input, .mes-anio-group select {
            width: 48%;  /* Para que se alineen en una sola línea */
        }

        #anio{
            margin-top: 30px;
            width: 220px;
            margin-right: 8px ;
        }

        #mes{
            margin-top: 5px; 
            width: 220px;
        }

        button {
            padding: 20px 25px;
            color: white;
            font-size: 150x;
            background-color: #5184DC;
            border: none;
            display: flex;
            width: 200px;
            margin-left: 125px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="expoExecelPer.php" method="post" onsubmit="return validarFormulario()">
            
            <!-- Filtrado por mes y año en la misma línea -->
            <div class="mes-anio-group">
                <div>
                    <label for="mes">Filtrado de informe por mes</label>
                    <select id="mes" name="mes">
                        <option value="">Seleccione un mes</option>
                        <option value="Enero">Enero</option>
                        <option value="Febrero">Febrero</option>
                        <option value="Marzo">Marzo</option>
                        <option value="Abril">Abril</option>
                        <option value="Mayo">Mayo</option>
                        <option value="Junio">Junio</option>
                        <option value="Julio">Julio</option>
                        <option value="Agosto">Agosto</option>
                        <option value="Septiembre">Septiembre</option>
                        <option value="Octubre">Octubre</option>
                        <option value="Noviembre">Noviembre</option>
                        <option value="Diciembre">Diciembre</option>
                    </select>
                </div>
                <div>
                    <input type="number" id="anio" name="anio" placeholder="Ej. 2024">
                </div>
            </div>

            <!-- Filtrado por año y fecha en la misma línea -->
            <div class="fecha-anios-group">
                <div>
                    <label for="anios">Filtrado por año</label>
                    <input type="number" id="anios" name="anios" placeholder="Ej. 2024">
                </div>
                <div>
                    <label for="fecha">Filtrado de informe por día</label>
                    <input type="date" id="fecha" name="fecha">
                </div>
            </div>

            <!-- Botón para enviar -->
            <button type="submit">Generar Reporte</button>
        </form>
    </div>
</body>
</html>