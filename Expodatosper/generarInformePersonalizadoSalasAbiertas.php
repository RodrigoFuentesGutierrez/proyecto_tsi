<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Exportación</title>
    <!-- Cargando Bootstrap desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <link rel="stylesheet" href="../css/a.css">
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
        <h2 class="text-center mb-4">Opciones de informe</h2>
        <div class="accordion" id="accordionExample">
            <!-- Opción 1: Filtrado por mes -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        Generar informe por mes
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="POST" action="visualizar_informe_a_exportar.php">
                            <div class="mes-anio-group d-flex flex-column">
                                <div class="mb-6">
                                    <p>En este apartado puedes seleccionar el mes para poder generar el informe por el mes que se eligio</p>
                                    <select id="mes" name="mes" class="form-select">
                                        <option value="">Seleccione un mes</option>
                                        <option value="1">Enero</option>
                                        <option value="2">Febrero</option>
                                        <option value="3">Marzo</option>
                                        <option value="4">Abril</option>
                                        <option value="5">Mayo</option>
                                        <option value="6">Junio</option>
                                        <option value="7">Julio</option>
                                        <option value="8">Agosto</option>
                                        <option value="9">Septiembre</option>
                                        <option value="10">Octubre</option>
                                        <option value="11">Noviembre</option>
                                        <option value="12">Diciembre</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <input type="number" id="anio" name="anio" class="form-control" placeholder="Ej. 2024">
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
                        Generar informe por año
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="POST" action="visualizar_informe_a_exportar.php">
                            <div class="fecha-anios-group">
                                <div class="mb-3">
                                <p>En este apartado podra generar el informe por el año en el que se genero eventualmente</p>
                                    <input type="number" id="anios" name="anios" class="form-control" placeholder="Ej. 2024">
                                </div>
                                <div class="btn1">
                                    <button type="submit" id="btn2" class="btn btn-primary">Generar reporte</button>
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
                        Generar informe por día
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="POST" action="visualizar_informe_a_exportar.php">
                            <div class="mb-3">
                            <p>En este apartado podra generar el informe por el dia en el que se genero habitualmente</p>
                                <input type="date" id="fecha" name="fecha" class="form-control">
                            </div>
                            <div class="btn3">
                                <button type="submit" id="btn3" class="btn btn-primary">Generar reporte</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    
            <!-- Opción 4: Filtrado por intervalo -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Generar informe de intervalo con mayor asistencia
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="POST" action="visualizar_informe_a_exportar.php">
                            <div class="mb-3">
                                <p>En este apartado se genera el informe por el intervalo con la mayor asistencia en el que se genero por cantidad</p>
                                <div class="mb-3">
                                    <input type="date" name="fechai" id="fechai" class="form-control" >
                                    <input type="date" name="fechat" id="fechat" class="form-control" >
                                </div>
                                <div class="btn4">
                                    <button type="submit" class="btn btn-primary">Generar reporte</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cargando los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
