<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reclamos | MASS</title>
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

        /* TABLA DE RECLAMOS */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #E5E7EB; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F9FAFB; padding: 16px 20px; font-size: 12px; text-transform: uppercase; color: #6B7280; font-weight: 700; border-bottom: 1px solid #E5E7EB; }
        td { padding: 16px 20px; font-size: 14px; color: #111827; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
        tr:hover td { background: #F9FAFB; }

        /* BADGES DE ESTADO */
        .badge-pendiente { background: #FEF3C7; color: #92400E; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .badge-atencion { background: #E0E7FF; color: #3730A3; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .badge-cerrado { background: #DEF7EC; color: #03543F; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }

        /* BOTONES */
        .btn-accion { background: #F3F4F6; color: #1D4ED8; border: 1px solid #BFDBFE; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-accion:hover { background: #EFF6FF; border-color: #93C5FD; }

        /* MODAL */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 27, 45, 0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: white; padding: 35px; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); position: relative; }
        .modal h2 { margin-top: 0; color: #0F1B2D; font-size: 22px; margin-bottom: 20px; font-weight: 800; border-bottom: 2px solid #F3F4F6; padding-bottom: 15px; }
        .close-btn { position: absolute; top: 20px; right: 25px; font-size: 24px; color: #9CA3AF; cursor: pointer; transition: 0.2s; }
        .close-btn:hover { color: #111827; }

        .info-box { background: #F9FAFB; padding: 15px; border-radius: 8px; border: 1px solid #E5E7EB; margin-bottom: 20px; font-size: 14px; }
        .info-box strong { color: #4B5563; font-size: 12px; text-transform: uppercase; display: block; margin-bottom: 4px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #4B5563; font-weight: 600; font-size: 13px; }
        .form-group textarea, .form-group select { width: 100%; padding: 12px 15px; border: 1px solid #D1D5DB; border-radius: 8px; box-sizing: border-box; font-family: 'Inter'; font-size: 14px; outline: none; background: #F9FAFB; }
        .form-group textarea:focus, .form-group select:focus { border-color: #0F1B2D; background: white; box-shadow: 0 0 0 3px rgba(15,27,45,0.1); }

        .btn-guardar { width: 100%; background: #FFD100; color: #0F1B2D; border: none; padding: 14px; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-guardar:hover { background: #E6BC00; }
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
            <li><a href="recepciones.php"><i class="fa-solid fa-boxes-packing"></i> Recepciones</a></li>
            <li class="active"><a href="reclamos.php"><i class="fa-solid fa-triangle-exclamation"></i> Reclamos</a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="header-top">
            <div>
                <h1>Gestión de Reclamos</h1>
                <p style="color: #6B7280; font-size: 14px; margin-top: 5px; margin-bottom: 0;">Seguimiento y resoluciones de incidencias con proveedores.</p>
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
                        <th style="width: 100px;">N° Reclamo</th>
                        <th style="width: 130px;">Informe Origen</th>
                        <th>Descripción de Incidencia</th>
                        <th style="width: 140px;">Estado</th>
                        <th style="width: 120px; text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once '../../backend/conexion.php';
                    $sql = "SELECT r.*, i.codigo_informe FROM reclamos r 
                            JOIN informe_recepcion i ON r.id_informe = i.id_informe 
                            ORDER BY r.id_reclamo DESC";
                    $resultado = $conexion->query($sql);
                    
                    if ($resultado && $resultado->num_rows > 0) {
                        while($fila = $resultado->fetch_assoc()) {
                            
                            $clase_badge = "badge-pendiente";
                            if($fila['estado'] == 'En atención') $clase_badge = "badge-atencion";
                            if($fila['estado'] == 'Cerrado') $clase_badge = "badge-cerrado";

                            echo "<tr>
                                    <td><strong>#{$fila['id_reclamo']}</strong></td>
                                    <td><i class='fa-solid fa-file-shield' style='color:#9CA3AF'></i> {$fila['codigo_informe']}</td>
                                    <td style='color: #4B5563;'>{$fila['descripcion_incidencia']}</td>
                                    <td><span class='{$clase_badge}'>{$fila['estado']}</span></td>
                                    <td style='text-align: center;'>";
                            
                            if($fila['estado'] != 'Cerrado') {
                                echo "<button class='btn-accion' onclick='abrirModalReclamo(\"{$fila['id_reclamo']}\", \"{$fila['codigo_informe']}\", \"{$fila['descripcion_incidencia']}\", \"{$fila['estado']}\", `{$fila['solucion_proveedor']}`)'><i class='fa-solid fa-gavel'></i> Gestionar</button>";
                            } else {
                                echo "<button class='btn-accion' style='color:#059669; border-color:#A7F3D0;' onclick='alert(\"Solución aplicada: {$fila['solucion_proveedor']}\")'><i class='fa-solid fa-circle-check'></i> Ver Solución</button>";
                            }
                            
                            echo "  </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; padding: 40px; color: #6B7280;'>No hay reclamos registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalReclamo" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarModal">&times;</span>
            <h2>Actualizar Estado del Reclamo</h2>
            
            <div class="info-box">
                <strong>Informe de Origen</strong>
                <span id="mod_informe">INF-0000</span>
                <strong style="margin-top: 10px;">Incidencia Reportada</strong>
                <span id="mod_descripcion" style="font-style: italic; color:#4B5563;">...</span>
            </div>

            <form id="formReclamo">
                <input type="hidden" id="mod_id_reclamo">
                
                <div class="form-group">
                    <label>Respuesta / Solución del Proveedor</label>
                    <textarea id="mod_solucion" rows="3" placeholder="Ej: Proveedor repondrá las unidades dañadas el próximo martes..."></textarea>
                </div>

                <div class="form-group">
                    <label>Cambiar Estado</label>
                    <select id="mod_estado">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En atención">En atención</option>
                        <option value="Cerrado">Cerrado (Caso Resuelto)</option>
                    </select>
                </div>

                <button type="button" class="btn-guardar" onclick="guardarSolucion()"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("modalReclamo");
        document.getElementById("btnCerrarModal").onclick = () => modal.style.display = "none";
        window.onclick = (e) => { if (e.target == modal) modal.style.display = "none"; }

        function abrirModalReclamo(id, informe, descripcion, estado, solucion) {
            document.getElementById('mod_id_reclamo').value = id;
            document.getElementById('mod_informe').innerText = informe;
            document.getElementById('mod_descripcion').innerText = descripcion;
            document.getElementById('mod_estado').value = estado;
            document.getElementById('mod_solucion').value = solucion === 'null' ? '' : solucion;
            
            modal.style.display = 'flex';
        }

        function guardarSolucion() {
            let id = document.getElementById('mod_id_reclamo').value;
            let solucion = document.getElementById('mod_solucion').value;
            let estado = document.getElementById('mod_estado').value;

            if (solucion.trim() === "") {
                alert("Por favor, ingresa la respuesta o solución brindada por el proveedor.");
                return;
            }

            let formData = new FormData();
            formData.append('id', id);
            formData.append('solucion', solucion);
            formData.append('estado', estado);

            fetch('../../backend/procesar_reclamo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === 'ok') {
                    alert("Reclamo actualizado correctamente.");
                    location.reload();
                } else {
                    alert("Error al procesar el reclamo.");
                }
            });
        }
    </script>
</body>
</html>