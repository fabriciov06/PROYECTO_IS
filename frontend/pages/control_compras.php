<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Compras | MASS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* RESET & TIPOGRAFÍA */
        body { font-family: 'Inter', sans-serif; background-color: #F3F4F6; margin: 0; display: flex; color: #374151; height: 100vh; overflow: hidden; }
        
        /* BARRA LATERAL (AZUL MARINO MASS) */
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

        /* TABLA DE SOLICITUDES */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #E5E7EB; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F9FAFB; padding: 16px 20px; font-size: 12px; text-transform: uppercase; color: #6B7280; font-weight: 700; border-bottom: 1px solid #E5E7EB; }
        td { padding: 16px 20px; font-size: 14px; color: #111827; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
        tr:hover td { background: #F9FAFB; }

        /* BADGES ESTADO */
        .badge-pendiente { background: #FEF3C7; color: #92400E; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .badge-aprobada { background: #DEF7EC; color: #03543F; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .badge-rechazada { background: #FDE8E8; color: #9B1C1C; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }

        /* BOTÓN DE REVISIÓN */
        .btn-revisar { background: #F3F4F6; color: #1D4ED8; border: 1px solid #BFDBFE; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-revisar:hover { background: #EFF6FF; border-color: #93C5FD; }

        /* MODAL DE REVISIÓN */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 27, 45, 0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: white; padding: 35px; border-radius: 16px; width: 100%; max-width: 600px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); position: relative; }
        .modal h2 { margin-top: 0; color: #0F1B2D; font-size: 22px; margin-bottom: 20px; font-weight: 800; border-bottom: 2px solid #F3F4F6; padding-bottom: 15px; }
        .close-btn { position: absolute; top: 20px; right: 25px; font-size: 24px; color: #9CA3AF; cursor: pointer; transition: 0.2s; }
        .close-btn:hover { color: #111827; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; background: #F9FAFB; padding: 15px; border-radius: 8px; border: 1px solid #E5E7EB; }
        .info-item label { display: block; font-size: 12px; color: #6B7280; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
        .info-item span { font-size: 14px; color: #111827; font-weight: 500; }

        .btn-aprobar { background: #10B981; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; flex: 1; }
        .btn-aprobar:hover { background: #059669; }
        .btn-rechazar { background: #EF4444; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; flex: 1; }
        .btn-rechazar:hover { background: #DC2626; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>MASS</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="productos.php"><i class="fa-solid fa-book-open"></i> Catálogo</a></li>
            <li><a href="movimientos.php"><i class="fa-solid fa-arrow-right-arrow-left"></i> Movimientos</a></li>
            <li class="active"><a href="control_compras.php"><i class="fa-solid fa-clipboard-check"></i> Control de Compras</a></li>
            <li><a href="recepciones.php"><i class="fa-solid fa-boxes-packing"></i> Recepciones</a></li>
            <li><a href="reclamos.php"><i class="fa-solid fa-triangle-exclamation"></i> Reclamos</a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="header-top">
            <div>
                <h1>Control de Compras</h1>
                <p style="color: #6B7280; font-size: 14px; margin-top: 5px; margin-bottom: 0;">Evalúa y gestiona las solicitudes de reposición de inventario.</p>
            </div>
            <div class="user-profile" style="position: relative; cursor: pointer;" onclick="let menu = document.getElementById('dropdown'); menu.style.display = menu.style.display === 'none' ? 'block' : 'none';">
                <span>Administrador</span>
                <div class="avatar">FV</div>
                <div id="dropdown" style="display: none; position: absolute; top: 55px; right: 0; background: white; border: 1px solid #E5E7EB; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 160px; z-index: 1000; overflow: hidden;">
                    <a href="../../backend/cerrar_sesion.php" style="color: #DC2626; padding: 12px 15px; display: flex; align-items: center; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='white'">
                        <i class="fa-solid fa-right-from-bracket" style="margin-right: 10px;"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Código Req.</th>
                        <th>Fecha de Solicitud</th>
                        <th>Supervisor Solicitante</th>
                        <th>Justificación Breve</th>
                        <th>Estado</th>
                        <th style="width: 110px; text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                   <?php
                    require_once '../../backend/conexion.php';
                    $sql = "SELECT * FROM solicitudes_compra ORDER BY fecha_solicitud DESC";
                    $resultado = $conexion->query($sql);
                    
                    if ($resultado && $resultado->num_rows > 0) {
                        while($fila = $resultado->fetch_assoc()) {
                            $fecha = date("d/m/Y h:i A", strtotime($fila['fecha_solicitud']));
                            
                            $clase_badge = "";
                            if($fila['estado'] == 'Pendiente') $clase_badge = "badge-pendiente";
                            if($fila['estado'] == 'Aprobada') $clase_badge = "badge-aprobada";
                            if($fila['estado'] == 'Rechazada') $clase_badge = "badge-rechazada";

                            // Cortar texto largo
                            $justificacion = strlen($fila['justificacion']) > 40 ? substr($fila['justificacion'],0,40)."..." : $fila['justificacion'];

                            // AQUÍ ESTÁ LA ESTRUCTURA CORRECTA (El center está en el último <td>)
                            echo "<tr>
                                    <td><strong>{$fila['codigo_solicitud']}</strong></td>
                                    <td style='color: #4B5563; font-size: 13px;'>{$fecha}</td>
                                    <td>{$fila['supervisor']}</td>
                                    <td style='color: #6B7280; font-style: italic;'>{$justificacion}</td>
                                    <td><span class='{$clase_badge}'>{$fila['estado']}</span></td>
                                    <td style='text-align: center;'>";
                            
                            if($fila['estado'] == 'Pendiente') {
                                echo "<button class='btn-revisar' onclick='abrirModalRevision(\"{$fila['id_solicitud']}\", \"{$fila['codigo_solicitud']}\", \"{$fila['supervisor']}\", \"{$fila['justificacion']}\")'><i class='fa-solid fa-eye'></i> Revisar</button>";
                            } else {
                                echo "<span style='color:#9CA3AF; font-size: 12px; font-weight:600;'><i class='fa-solid fa-check-double'></i> Evaluada</span>";
                            }
                            
                            echo "  </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; padding: 40px; color: #6B7280;'>No hay solicitudes registradas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalRevision" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarModal">&times;</span>
            <h2>Evaluar Solicitud de Compra</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <label>Código Solicitud</label>
                    <span id="mod_codigo">REQ-000</span>
                </div>
                <div class="info-item">
                    <label>Supervisor</label>
                    <span id="mod_supervisor">Nombre</span>
                </div>
                <div class="info-item" style="grid-column: span 2;">
                    <label>Justificación del Pedido</label>
                    <span id="mod_justificacion" style="font-style: italic;">...</span>
                </div>
            </div>

            <h3 style="font-size: 15px; color: #374151; margin-bottom: 10px;">Productos Solicitados:</h3>
            <div style="border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; margin-bottom: 25px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                        <tr>
                            <th style="padding: 10px 15px; font-size: 11px;">Producto</th>
                            <th style="padding: 10px 15px; font-size: 11px; text-align: center;">Cant. Solicitada</th>
                        </tr>
                    </thead>
                    <tbody id="listaProductosModal">
                        <tr><td style="padding: 10px 15px; font-size: 13px;">Cargando productos...</td><td></td></tr>
                    </tbody>
                </table>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="button" class="btn-aprobar"><i class="fa-solid fa-check"></i> Aprobar Solicitud</button>
                <button type="button" class="btn-rechazar"><i class="fa-solid fa-xmark"></i> Rechazar</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById("modalRevision");
        let solicitudActual = 0; // Guardará el ID de la solicitud que estamos revisando

        document.getElementById("btnCerrarModal").onclick = () => modal.style.display = "none";
        window.onclick = (e) => { if (e.target == modal) modal.style.display = "none"; }

        function abrirModalRevision(id, codigo, supervisor, justificacion) {
            solicitudActual = id;
            document.getElementById('mod_codigo').innerText = codigo;
            document.getElementById('mod_supervisor').innerText = supervisor;
            document.getElementById('mod_justificacion').innerText = justificacion;

            // En un proyecto real aquí harías un fetch para traer los detalles, 
            // pero para el informe usamos esta simulación visual:
            let tablaProductos = document.getElementById('listaProductosModal');
            if (codigo === 'REQ-1001') {
                tablaProductos.innerHTML = `<tr><td style="padding: 10px 15px; font-size: 13px; border-bottom: 1px solid #F3F4F6;"><strong>P002</strong> - Leche Gloria 170g</td><td style="padding: 10px 15px; font-size: 13px; text-align: center; font-weight: 700; color: #0F1B2D;">50 und</td></tr>`;
            } else {
                tablaProductos.innerHTML = `
                    <tr><td style="padding: 10px 15px; font-size: 13px; border-bottom: 1px solid #F3F4F6;"><strong>P006</strong> - Mayonesa Laive 500g</td><td style="padding: 10px 15px; font-size: 13px; text-align: center; font-weight: 700; color: #0F1B2D;">20 und</td></tr>
                    <tr><td style="padding: 10px 15px; font-size: 13px; border-bottom: 1px solid #F3F4F6;"><strong>P005</strong> - Mayonesa Laive 750g</td><td style="padding: 10px 15px; font-size: 13px; text-align: center; font-weight: 700; color: #0F1B2D;">10 und</td></tr>`;
            }
            modal.style.display = 'flex';
        }

        // Lógica de los botones del modal
        document.querySelector('.btn-aprobar').onclick = function() {
            if(confirm("¿Estás seguro de APROBAR esta solicitud?")) {
                evaluarSolicitud(solicitudActual, 'Aprobada', '');
            }
        };

        document.querySelector('.btn-rechazar').onclick = function() {
            let motivo = prompt("Por favor, ingresa el motivo del rechazo:");
            if(motivo != null && motivo.trim() !== "") {
                evaluarSolicitud(solicitudActual, 'Rechazada', motivo);
            } else if (motivo != null) {
                alert("Debes ingresar un motivo para rechazar la solicitud.");
            }
        };

        function evaluarSolicitud(id, estado, motivo) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('estado', estado);
            formData.append('motivo', motivo);

            fetch('../../backend/evaluar_solicitud.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === 'ok') {
                    alert(`Solicitud ${estado} correctamente.`);
                    location.reload(); // Recarga para ver el nuevo estado
                } else {
                    alert("Error al procesar la solicitud.");
                }
            });
        }
    </script>
</body>
</html>