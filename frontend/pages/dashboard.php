<?php
session_start();
require_once '../../backend/acciones/conexion.php';
require_once '../../backend/clases/autoload.php';

// 1. OBTENER ESTADÍSTICAS PARA LAS TARJETAS (KPIs) USANDO CLASES POO
$totales = [
    'productos' => Producto::contarTotal($conexion),
    'alertas_stock' => Producto::contarStockBajo($conexion),
    'compras_pendientes' => SolicitudCompra::contarPendientes($conexion),
    'reclamos_activos' => Reclamo::contarActivos($conexion)
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MASS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* RESET & TIPOGRAFÍA */
        body { font-family: 'Inter', sans-serif; background-color: #F3F4F6; margin: 0; display: flex; color: #374151; height: 100vh; overflow: hidden; }
        
        /* BARRA LATERAL */
        .sidebar { background: #0F1B2D; color: white; width: 260px; padding: 20px 0; display: flex; flex-direction: column; box-shadow: 4px 0 10px rgba(0,0,0,0.05); z-index: 10; }
        .sidebar h2 { text-align: center; font-weight: 900; font-size: 32px; margin-bottom: 30px; letter-spacing: 1px; color: #FFD100; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; width: 100%; }
        .sidebar ul li { width: 100%; display: flex; justify-content: center; }
        .sidebar ul li a { display: flex; align-items: center; width: 100%; padding: 15px 30px; color: #FFFFFF; text-decoration: none; font-weight: 500; transition: 0.3s; gap: 15px; opacity: 0.8; }
        .sidebar ul li a i { font-size: 18px; width: 24px; text-align: center; }
        .sidebar ul li a:hover, .sidebar ul li.active a { background: rgba(255, 209, 0, 0.1); color: #FFD100; border-left: 4px solid #FFD100; opacity: 1; }

        /* CONTENIDO PRINCIPAL */
        .content { flex: 1; padding: 30px 40px; overflow-y: auto; }
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-top h1 { font-size: 28px; font-weight: 700; color: #0F1B2D; margin: 0; }
        .user-profile { display: flex; align-items: center; gap: 12px; background: white; padding: 8px 16px; border-radius: 50px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #E5E7EB; }
        .user-profile span { font-size: 14px; font-weight: 600; color: #374151; }
        .avatar { background: #0F1B2D; color: #FFD100; font-weight: 700; font-size: 13px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }

        /* GRID DE TARJETAS (KPIs) */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .kpi-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #E5E7EB; display: flex; align-items: center; gap: 20px; transition: 0.3s; }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .kpi-icon { width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .kpi-info h3 { margin: 0; font-size: 13px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-bottom: 5px; }
        .kpi-info p { margin: 0; font-size: 28px; font-weight: 800; color: #0F1B2D; }

        /* COLORES DE ICONOS */
        .icon-blue { background: #E0E7FF; color: #4F46E5; }
        .icon-red { background: #FDE8E8; color: #E02424; }
        .icon-yellow { background: #FEF3C7; color: #D97706; }
        .icon-purple { background: #F3E8FF; color: #9333EA; }

        /* SECCIÓN DE ALERTAS INFERIOR */
        .dashboard-sections { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .section-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #E5E7EB; overflow: hidden; }
        .section-header { padding: 20px; border-bottom: 1px solid #E5E7EB; background: #F9FAFB; display: flex; justify-content: space-between; align-items: center; }
        .section-header h2 { margin: 0; font-size: 16px; font-weight: 700; color: #111827; }
        
        /* TABLA SIMPLIFICADA */
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 20px; font-size: 12px; text-transform: uppercase; color: #6B7280; font-weight: 700; border-bottom: 1px solid #E5E7EB; }
        td { padding: 12px 20px; font-size: 13px; color: #374151; border-bottom: 1px solid #F3F4F6; }
        .badge-danger { background: #FDE8E8; color: #9B1C1C; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; }
        .badge-warning { background: #FEF3C7; color: #92400E; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>MASS</h2>
        <ul>
            <li class="active"><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="productos.php"><i class="fa-solid fa-book-open"></i> Catálogo</a></li>
            <li><a href="movimientos.php"><i class="fa-solid fa-arrow-right-arrow-left"></i> Movimientos</a></li>
            <li><a href="control_compras.php"><i class="fa-solid fa-clipboard-check"></i> Control de Compras</a></li>
            <li><a href="recepciones.php"><i class="fa-solid fa-boxes-packing"></i> Recepciones</a></li>
            <li><a href="reclamos.php"><i class="fa-solid fa-triangle-exclamation"></i> Reclamos</a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="header-top">
            <div>
                <h1>Panel de Control Principal</h1>
                <p style="color: #6B7280; font-size: 14px; margin-top: 5px; margin-bottom: 0;">Resumen general del estado del inventario y operaciones.</p>
            </div>
            <div class="user-profile" style="position: relative; cursor: pointer;" onclick="let menu = document.getElementById('dropdown'); menu.style.display = menu.style.display === 'none' ? 'block' : 'none';">
                <span>Administrador</span>
                <div class="avatar">FV</div>
                <div id="dropdown" style="display: none; position: absolute; top: 55px; right: 0; background: white; border: 1px solid #E5E7EB; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 160px; z-index: 1000; overflow: hidden;">
                    <a href="../../backend/acciones/cerrar_sesion.php" style="color: #DC2626; padding: 12px 15px; display: flex; align-items: center; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='white'">
                        <i class="fa-solid fa-right-from-bracket" style="margin-right: 10px;"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon icon-blue"><i class="fa-solid fa-box-open"></i></div>
                <div class="kpi-info">
                    <h3>Total Productos</h3>
                    <p><?= $totales['productos'] ?></p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon icon-red"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="kpi-info">
                    <h3>Alertas de Stock</h3>
                    <p><?= $totales['alertas_stock'] ?></p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon icon-yellow"><i class="fa-solid fa-cart-arrow-down"></i></div>
                <div class="kpi-info">
                    <h3>Compras Pendientes</h3>
                    <p><?= $totales['compras_pendientes'] ?></p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon icon-purple"><i class="fa-solid fa-handshake-angle"></i></div>
                <div class="kpi-info">
                    <h3>Reclamos Activos</h3>
                    <p><?= $totales['reclamos_activos'] ?></p>
                </div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fa-solid fa-boxes-stacked" style="color: #4B5563; margin-right: 8px;"></i> Productos con Stock Crítico</h2>
                    <a href="productos.php" style="font-size: 13px; color: #1D4ED8; text-decoration: none; font-weight: 600;">Ver catálogo &rarr;</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                            <th>Mínimo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                                            <?php
                        $alertas_productos = Producto::obtenerStockBajo($conexion, 5);
                        
                        if (!empty($alertas_productos)) {
                            foreach ($alertas_productos as $fila) {
                                $badge = ($fila['stock'] == 0) ? "<span class='badge-danger'>Agotado</span>" : "<span class='badge-warning'>Stock Bajo</span>";
                                echo "<tr>
                                        <td><strong>{$fila['codigo']}</strong></td>
                                        <td>{$fila['nombre']}</td>
                                        <td style='font-weight: 700; color: " . ($fila['stock']==0 ? '#9B1C1C' : '#92400E') . ";'>{$fila['stock']} und</td>
                                        <td style='color: #6B7280;'>{$fila['stock_minimo']}</td>
                                        <td>{$badge}</td>
                                       </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align: center; padding: 30px; color: #6B7280;'>Todos los productos tienen stock saludable.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fa-solid fa-clock-rotate-left" style="color: #4B5563; margin-right: 8px;"></i> Últimos Movimientos</h2>
                </div>
                <div style="padding: 0;">
                    <?php
                    $ultimos_movimientos = MovimientoInventario::obtenerUltimos($conexion, 4);

                    if (!empty($ultimos_movimientos)) {
                        foreach ($ultimos_movimientos as $mov) {
                            $hora = date("d/m - H:i", strtotime($mov['fecha_hora']));
                            $color = ($mov['tipo_movimiento'] == 'Entrada') ? '#059669' : '#DC2626';
                            $signo = ($mov['tipo_movimiento'] == 'Entrada') ? '+' : '-';
                            
                            // Acortar el nombre para que quepa bien
                            $nombre_corto = strlen($mov['nombre']) > 25 ? substr($mov['nombre'],0,25)."..." : $mov['nombre'];

                            echo "
                            <div style='display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #F3F4F6;'>
                                <div>
                                    <p style='margin: 0; font-size: 13px; font-weight: 600; color: #111827;'>{$nombre_corto}</p>
                                    <p style='margin: 0; font-size: 11px; color: #6B7280; margin-top: 3px;'>{$hora} • {$mov['tipo_movimiento']}</p>
                                </div>
                                <div style='font-weight: 800; font-size: 14px; color: {$color};'>
                                    {$signo}{$mov['cantidad']}
                                </div>
                            </div>";
                        }
                    } else {
                        echo "<p style='text-align: center; color: #6B7280; padding: 30px; font-size: 13px;'>No hay movimientos recientes.</p>";
                    }
                    ?>
                </div>
                <div style="padding: 15px 20px; text-align: center; background: #F9FAFB; border-top: 1px solid #E5E7EB;">
                    <a href="movimientos.php" style="font-size: 13px; color: #4B5563; text-decoration: none; font-weight: 600;">Ver historial completo</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>