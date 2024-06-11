<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Incluye Chart.js desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Incluye tus estilos CSS (si tienes) -->
    <style>
        .container {
            padding: 20px;
        }
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .chart-wrapper {
            margin: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1 1 calc(33.333% - 40px); /* Ajuste flexible para tres gráficos por fila */
            max-width: calc(33.333% - 40px); /* Ajuste flexible para tres gráficos por fila */
        }
        .chart-wrapper.bigger {
            flex: 2 2 calc(66.666% - 40px); /* Doble ancho */
            max-width: calc(66.666% - 40px); /* Doble ancho */
        }
        .chart-container {
            position: relative;
            height: 40vh;
            width: 100%;
        }
        .chart-container canvas {
            background: #F8F9F9;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container is-fluid">
        <h1 class="title">Home</h1>
        <div class="columns is-flex is-justify-content-center">
            <figure class="image is-128x128">
                <?php
                    if (is_file("./app/views/fotos/" . $_SESSION['foto'])) {
                        echo '<img class="is-rounded" src="' . APP_URL . 'app/views/fotos/' . $_SESSION['foto'] . '">';
                    } else {
                        echo '<img class="is-rounded" src="' . APP_URL . 'app/views/fotos/default.png">';
                    }
                ?>
            </figure>
        </div>
        <div class="columns is-flex is-justify-content-center">
            <h2 class="subtitle">¡Bienvenido <?php echo $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?>!</h2>
        </div>
    </div>

    <?php
        $total_cajas = $insLogin->seleccionarDatos("Normal", "caja", "caja_id != 0", 0)->rowCount();
        $total_usuarios = $insLogin->seleccionarDatos("Normal", "usuario", "usuario_id != '1' AND usuario_id != '" . $_SESSION['id'] . "'", 0)->rowCount();
        $total_clientes = $insLogin->seleccionarDatos("Normal", "cliente", "cliente_id != '1'", 0)->rowCount();
        $total_categorias = $insLogin->seleccionarDatos("Normal", "categoria", "categoria_id != 0", 0)->rowCount();
        $total_productos = $insLogin->seleccionarDatos("Normal", "producto", "producto_id != 0", 0)->rowCount();
        $total_ventas = $insLogin->seleccionarDatos("Normal", "venta", "venta_id != 0", 0)->rowCount();

        // Obtener el año actual
        $current_year = date("Y");

        // Conexión directa para obtener el valor total de las ventas del año actual
        try {
            $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("SELECT SUM(venta_total) as total FROM venta WHERE YEAR(venta_fecha) = :current_year");
            $stmt->bindParam(':current_year', $current_year, PDO::PARAM_INT);
            $stmt->execute();
            $total_ventas_valor = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Consulta para obtener el top 10 de productos más vendidos del año actual
            $stmt_top_productos = $pdo->prepare("
                SELECT producto.producto_nombre, SUM(venta_detalle.venta_detalle_cantidad) as cantidad_vendida
                FROM venta_detalle
                INNER JOIN producto ON venta_detalle.producto_id = producto.producto_id
                INNER JOIN venta ON venta_detalle.venta_codigo = venta.venta_codigo
                WHERE YEAR(venta.venta_fecha) = :current_year
                GROUP BY producto.producto_nombre
                ORDER BY cantidad_vendida DESC
                LIMIT 10
            ");
            $stmt_top_productos->bindParam(':current_year', $current_year, PDO::PARAM_INT);
            $stmt_top_productos->execute();
            $top_productos = $stmt_top_productos->fetchAll(PDO::FETCH_ASSOC);

            // Consulta para obtener ventas por día de la semana del año actual
            $stmt_ventas_dia = $pdo->prepare("
                SELECT DAYNAME(venta_fecha) as dia_semana, SUM(venta_total) as total_ventas
                FROM venta
                WHERE YEAR(venta_fecha) = :current_year
                GROUP BY dia_semana
                ORDER BY FIELD(dia_semana, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')
            ");
            $stmt_ventas_dia->bindParam(':current_year', $current_year, PDO::PARAM_INT);
            $stmt_ventas_dia->execute();
            $ventas_dia = $stmt_ventas_dia->fetchAll(PDO::FETCH_ASSOC);

            // Mapeo de días en inglés a español
            $dias_espanol = [
                'Sunday' => 'Domingo',
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado'
            ];

            // Convertir los días al español
            foreach ($ventas_dia as &$venta) {
                $venta['dia_semana'] = $dias_espanol[$venta['dia_semana']];
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $total_ventas_valor = 0;
            $top_productos = [];
            $ventas_dia = [];
        }
    ?>

    <div class="container pb-6 pt-6">
        <?php if ($_SESSION['rol'] === 'Administrador'): ?>
            <div class="columns pb-6">
                <div class="column">
                    <nav class="level is-mobile">
                        <div class="level-item has-text-centered">
                            <a href="<?php echo APP_URL; ?>cashierList/">
                                <p class="heading"><i class="fas fa-cash-register fa-fw"></i> &nbsp; Cajas</p>
                                <p class="title"><?php echo $total_cajas; ?></p>
                            </a>
                        </div>
                        <div class="level-item has-text-centered">
                            <a href="<?php echo APP_URL; ?>userList/">
                                <p class="heading"><i class="fas fa-users fa-fw"></i> &nbsp; Usuarios</p>
                                <p class="title"><?php echo $total_usuarios; ?></p>
                            </a>
                        </div>
                        <div class="level-item has-text-centered">
                            <a href="<?php echo APP_URL; ?>clientList/">
                                <p class="heading"><i class="fas fa-address-book fa-fw"></i> &nbsp; Clientes</p>
                                <p class="title"><?php echo $total_clientes; ?></p>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>

            <div class="columns pt-6">
                <div class="column">
                    <nav class="level is-mobile">
                        <div class="level-item has-text-centered">
                            <a href="<?php echo APP_URL; ?>categoryList/">
                                <p class="heading"><i class="fas fa-tags fa-fw"></i> &nbsp; Categorías</p>
                                <p class="title"><?php echo $total_categorias; ?></p>
                            </a>
                        </div>
                        <div class="level-item has-text-centered">
                            <a href="<?php echo APP_URL; ?>productList/">
                                <p class="heading"><i class="fas fa-cubes fa-fw"></i> &nbsp; Productos</p>
                                <p class="title"><?php echo $total_productos; ?></p>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
        <?php endif; ?>

        <div class="columns pt-6">
            <div class="column">
                <nav class="level is-mobile">
                    <div class="level-item has-text-centered">
                        <a href="<?php echo APP_URL; ?>saleList/">
                            <p class="heading"><i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Ventas</p>
                            <p class="title"><?php echo $total_ventas; ?></p>
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Contenedor de Gráficas -->
        <div class="charts-container">
            <!-- Gráfico de Ventas Generales -->
            <div class="chart-wrapper">
                <div class="chart-container">
                    <canvas id="ventasChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Gráfico Top 10 Productos Más Vendidos -->
            <div class="chart-wrapper">
                <div class="chart-container">
                    <canvas id="topProductosChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Gráfico de Ventas por Día de la Semana -->
            <div class="chart-wrapper bigger">
                <div class="chart-container">
                    <canvas id="ventasDiaChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            var totalVentasValor = '<?php echo number_format($total_ventas_valor, 2); ?>';

            // Gráfico de Ventas Generales
            var ctxVentas = document.getElementById('ventasChart').getContext('2d');
            var ventasChart = new Chart(ctxVentas, {
                type: 'pie',
                data: {
                    labels: ['Cajas', 'Usuarios', 'Clientes', 'Categorías', 'Productos', 'Ventas'],
                    datasets: [{
                        label: 'Total',
                        data: [
                            <?php echo $total_cajas; ?>,
                            <?php echo $total_usuarios; ?>,
                            <?php echo $total_clientes; ?>,
                            <?php echo $total_categorias; ?>,
                            <?php echo $total_productos; ?>,
                            <?php echo $total_ventas; ?>
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#000000', // Cambiar color del texto a negro
                                font: {
                                    weight: 'bold' // Cambiar a negrita
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                afterLabel: function(tooltipItem) {
                                    if (tooltipItem.label === 'Ventas') {
                                        return 'Valor Total: ' + totalVentasValor + ' COP';
                                    }
                                }
                            }
                        }
                    }
                }
            });

            // Gráfico Top 10 Productos Más Vendidos
            var topProductosLabels = <?php echo json_encode(array_column($top_productos, 'producto_nombre')); ?>;
            var topProductosData = <?php echo json_encode(array_column($top_productos, 'cantidad_vendida')); ?>;
            
            var ctxTopProductos = document.getElementById('topProductosChart').getContext('2d');
            var topProductosChart = new Chart(ctxTopProductos, {
                type: 'pie',
                data: {
                    labels: topProductosLabels,
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: topProductosData,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 205, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#000000', // Cambiar color del texto a negro
                                font: {
                                    weight: 'bold' // Cambiar a negrita
                                }
                            }
                        }
                    }
                }
            });

            // Gráfico de Ventas por Día de la Semana
            var ventasDiaLabels = <?php echo json_encode(array_column($ventas_dia, 'dia_semana')); ?>;
            var ventasDiaData = <?php echo json_encode(array_column($ventas_dia, 'total_ventas')); ?>;
            
            var ctxVentasDia = document.getElementById('ventasDiaChart').getContext('2d');
            var ventasDiaChart = new Chart(ctxVentasDia, {
                type: 'bar',
                data: {
                    labels: ventasDiaLabels,
                    datasets: [{
                        label: 'Ventas por Día de la Semana',
                        data: ventasDiaData,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#000000', // Cambiar color del texto a negro
                                font: {
                                    weight: 'bold' // Cambiar a negrita
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                color: '#000000', // Cambiar color del texto a negro
                                font: {
                                    weight: 'bold' // Cambiar a negrita
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#000000', // Cambiar color del texto a negro
                                font: {
                                    weight: 'bold' // Cambiar a negrita
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
