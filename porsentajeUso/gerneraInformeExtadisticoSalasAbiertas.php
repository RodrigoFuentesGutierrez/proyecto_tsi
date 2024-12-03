<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Exportación</title>
    <!-- Cargando Bootstrap desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <link rel="stylesheet" href="../css/d.css">
</head>
<body>
    <header>
        <div class="logo text-center mb-4">
            <img src="../img/logo.png" alt="Logo USM">
        </div>
        <div class="opciones">
            <a href="../src/menuOpciones.html" title="Ir a Inicio" style=" font-size: 25px; position: relative;">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </header>
    
    <div class="container mt-5">
        <h2 class="text-center mb-4">Opciones de Porcentajes estadisticos para la sala abierta</h2>
        <div class="accordion" id="accordionExample">
            <!-- Opción 1: Filtrado por mes -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        Generar Resultado por estadistica
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="POST" action="informeExtadisticosalaAbierta.php">
                            <div class="mes-anio-group d-flex flex-column">
                            <p>En este apartado podra seleccionar la hora de inicio, mas la hora de termino y la fecha en el que se hizo la estadistica</p>
                                <div class="mb-3">
                                   <input type="time" name="horai" id="horai">
                                   <input type="time" name="horat" id="horat">
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="fechai" id="fechai">
                                    <input type="text" name="fechat" id="fechat">
                                    <button type="submit" id="btn" class="btn btn-primary">Generar reporte</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    
            <!-- Opción 2: Filtrado por año -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Generar Resultado por fecha
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="POST" action="informeExtadisticosalaAbierta.php">
                            <p>en este apartado podra generar el Resultado por fecha de inicio y la fecha de termino del dia</p>
                            <div class="fecha-anios-group">
                                <div class="mb-3">
                                    <input type="text" name="fechai1" class="fechai" id="fechai1" placeholder="01-01-2024">
                                    <input type="text" name="fechat1" class="fechat" id="fechat1" placeholder="06-01-2024">
                                </div>
                                <div class="btn1">
                                    <button type="submit" id="btn1" class="btn btn-primary">Generar reporte</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Opción 3: Filtrado por día -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Generar Resultado mensual
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="POST" action="informeExtadisticosalaAbierta.php">
                        <p>en este apartado puede generar el Resultado mensual, con el mes y año de la ocupacion y su inicio</p>
                            <div class="btn3">
                                <input type="number" id="anio" name="anio" class="form-control" placeholder="Ej. 2024">
                                <button type="submit" id="btn3" class="btn btn-primary">Generar reporte</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    <!-- Cargando los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
