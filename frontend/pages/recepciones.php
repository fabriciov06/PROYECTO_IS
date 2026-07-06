<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepciones de Almacén | MASS</title>
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

        /* TABLA DE INFORMES */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #E5E7EB; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F9FAFB; padding: 16px 20px; font-size: 12px; text-transform: uppercase; color: #6B7280; font-weight: 700; border-bottom: 1px solid #E5E7EB; }
        td { padding: 16px 20px; font-size: 14px; color: #111827; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
        tr:hover td { background: #F9FAFB; }

        /* BADGES ESTADO INFORME */
        .badge-pendiente { background: #FEF3C7; color: #92400E; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .badge-conforme { background: #DEF7EC; color: #03543F; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .badge-incidencia { background: #FDE8E8; color: #9B1C1C; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }

        /* BOTÓN DE REVISIÓN */
        .btn-revisar { background: #F3F4F6; color: #1D4ED8; border: 1px solid #BFDBFE; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-revisar:hover { background: #EFF6FF; border-color: #93C5FD; }

        /* MODAL */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 27, 45, 0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: white; padding: 35px; border-radius: 16px; width: 100%; max-width: 650px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); position: relative; }
        .modal h2 { margin-top: 0; color: #0F1B2D; font-size: 22px; margin-bottom: 20px; font-weight: 800; border-bottom: 2px solid #F3F4F6; padding-bottom: 15px; }
        .close-btn { position: absolute; top: 20px; right: 25px; font-size: 24px; color: #9CA3AF; cursor: pointer; transition: 0.2s; }
        .close-btn:hover { color: #111827; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; background: #F9FAFB; padding: 15px; border-radius: 8px; border: 1px solid #E5E7EB; }
        .info-item label { display: block; font-size: 12px; color: #6B7280; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
        .info-item span { font-size: 14px; color: #111827; font-weight: 500; }
        .obs-box { grid-column: span 2; background: #FFF9D6; padding: 12px; border-left: 4px solid #FFD100; border-radius: 4px; font-size: 13px; color: #92400E; font-style: italic; }

        /* BOTONES MODAL */
        .btn-conforme { background: #10B981; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; flex: 1; }
        .btn-conforme:hover { background: #059669; }
        .btn-incidencia { background: #EF4444; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; flex: 1; }
        .btn-incidencia:hover { background: #DC2626; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>MASS</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="productos.php"><i class="fa-solid fa-book-open"></i> Catálogo</a></li>
            <li><a href="movimientos.php"><i class="fa-solid fa-arrow-right-arrow-left"></i> Movimientos</a></li>
            <li><a href="control_compras.php"><i class="fa-solid fa-clipboard-check"></i> Control de Compras</a></li>
            <li class="active"><a href="recepciones.php"><i class="fa-solid fa-boxes-packing"></i> Recepciones</a></li>
            <li><a href="reclamos.php"><i class="fa-solid fa-triangle-exclamation"></i> Reclamos</a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="header-top">
            <div>
                <h1>Revisar Informe de Recepción</h1>
                <p style="color: #6B7280; font-size: 14px; margin-top: 5px; margin-bottom: 0;">Valida la conformidad de los productos que ingresan al almacén.</p>
            </div>
            <div class="user-profile">
                <span>Administrador</span>
                <div class="avatar">FV</div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Código Informe</th>
                        <th>Guía Ref.</th>
                        <th>Fecha de Recepción</th>
                        <th>Operador</th>
                        <th>Estado</th>
                        <th style="width: 110px; text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                   <?php
                    require_once '../../backend/conexion.php';
                    $sql = "SELECT i.*, g.codigo_guia FROM informe_recepcion i 
                            JOIN guia_productos g ON i.id_guia = g.id_guia 
                            ORDER BY i.fecha_recepcion DESC";
                    $resultado = $conexion->query($sql);
                    
                    if ($resultado && $resultado->num_rows > 0) {
                        while($fila = $resultado->fetch_assoc()) {
                            $fecha = date("d/m/Y h:i A", strtotime($fila['fecha_recepcion']));
                            
                            $clase_badge = "";
                            if($fila['estado'] == 'Pendiente') $clase_badge = "badge-pendiente";
                            if($fila['estado'] == 'Conforme') $clase_badge = "badge-conforme";
                            if($fila['estado'] == 'Con Incidencias') $clase_badge = "badge-incidencia";

                            echo "<tr>
                                    <td><strong>{$fila['codigo_informe']}</strong></td>
                                    <td><i class='fa-solid fa-file-invoice' style='color:#9CA3AF'></i> {$fila['codigo_guia']}</td>
                                    <td style='color: #4B5563; font-size: 13px;'>{$fecha}</td>
                                    <td>{$fila['operador']}</td>
                                    <td><span class='{$clase_badge}'>{$fila['estado']}</span></td>
                                    <td style='text-align: center;'>";
                            
                            if($fila['estado'] == 'Pendiente') {
                                echo "<button class='btn-revisar' onclick='abrirModalRecepcion(\"{$fila['id_informe']}\", \"{$fila['codigo_informe']}\", \"{$fila['codigo_guia']}\", \"{$fila['operador']}\", \"{$fila['observaciones']}\")'><i class='fa-solid fa-magnifying-glass'></i> Revisar</button>";
                            } else {
                                echo "<span style='color:#9CA3AF; font-size: 12px; font-weight:600;'><i class='fa-solid fa-check-double'></i> Revisado</span>";
                            }
                            
                            echo "  </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; padding: 40px; color: #6B7280;'>No hay informes de recepción pendientes.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalRecepcion" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarModal">&times;</span>
            <h2>Detalle de Recepción</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <label>Informe N°</label>
                    <span id="mod_codigo">INF-000</span>
                </div>
                <div class="info-item">
                    <label>Guía del Proveedor</label>
                    <span id="mod_guia">G-000</span>
                </div>
                <div class="info-item">
                    <label>Operador de Almacén</label>
                    <span id="mod_operador">Nombre</span>
                </div>
                <div class="obs-box" id="mod_observaciones">
                    </div>
            </div>

            <h3 style="font-size: 15px; color: #374151; margin-bottom: 10px;">Estado Físico de Productos:</h3>
            <div style="border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; margin-bottom: 25px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                        <tr>
                            <th style="padding: 10px 15px; font-size: 11px;">Producto</th>
                            <th style="padding: 10px 15px; font-size: 11px; text-align: center;">Cant. Recibida</th>
                            <th style="padding: 10px 15px; font-size: 11px; text-align: center;">Condición</th>
                        </tr>
                    </thead>
                    <tbody id="listaProductosRecepcion">
                        </tbody>
                </table>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="button" class="btn-conforme"><i class="fa-solid fa-check"></i> Validar Conformidad</button>
                <button type="button" class="btn-incidencia"><i class="fa-solid fa-triangle-exclamation"></i> Registrar Incidencias</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById("modalRecepcion");
        let informeActual = 0;

        document.getElementById("btnCerrarModal").onclick = () => modal.style.display = "none";
        
        function abrirModalRecepcion(id, codigo, guia, operador, obs) {
            informeActual = id;
            document.getElementById('mod_codigo').innerText = codigo;
            document.getElementById('mod_guia').innerText = guia;
            document.getElementById('mod_operador').innerText = operador;
            document.getElementById('mod_observaciones').innerText = "📝 Observación del operador: " + obs;

            // Aquí se cargarían los productos reales vía fetch, esto es para tu visualización del informe:
            let tablaProductos = document.getElementById('listaProductosRecepcion');
            if (codigo === 'INF-2001') {
                tablaProductos.innerHTML = `<tr><td style="padding: 10px 15px; font-size: 13px;"><strong>P001</strong> - Leche Gloria 400g</td><td style="text-align: center;">100 und</td><td style="text-align: center;"><span class="badge-conforme">Buen estado</span></td></tr>`;
            } else {
                tablaProductos.innerHTML = `
                    <tr><td style="padding: 10px 15px; font-size: 13px;"><strong>P004</strong> - Mayonesa Alacena 100g</td><td style="text-align: center;">130 und</td><td style="text-align: center;"><span class="badge-incidencia">Faltante (-10)</span></td></tr>
                    <tr><td style="padding: 10px 15px; font-size: 13px;"><strong>P009</strong> - Aceite Primor 1L</td><td style="text-align: center;">118 und</td><td style="text-align: center;"><span class="badge-pendiente" style="background:#FEF3C7; color:#92400E">Dañado (2)</span></td></tr>`;
            }
            modal.style.display = 'flex';
        }

        // Lógica para guardar la validación
        function evaluarRecepcion(estado) {
            let formData = new FormData();
            formData.append('id', informeActual);
            formData.append('estado', estado);

            fetch('../../backend/evaluar_recepcion.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === 'ok') {
                    alert("Informe procesado como: " + estado);
                    location.reload();
                } else {
                    alert("Error al actualizar el informe.");
                }
            });
        }

        document.querySelector('.btn-conforme').onclick = () => evaluarRecepcion('Conforme');
        document.querySelector('.btn-incidencia').onclick = () => evaluarRecepcion('Con Incidencias');
    </script>
</body>
</html>