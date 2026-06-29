<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos | MASS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* RESET & TIPOGRAFÍA */
        body { font-family: 'Inter', sans-serif; background-color: #F3F4F6; margin: 0; display: flex; color: #374151; height: 100vh; overflow: hidden; }
        
        /* BARRA LATERAL (SIDEBAR) - AZUL DEL LOGIN (#0F1B2D) */
        .sidebar { background: #0F1B2D; color: white; width: 260px; padding: 20px 0; display: flex; flex-direction: column; box-shadow: 4px 0 10px rgba(0,0,0,0.05); z-index: 10; }
        .sidebar h2 { text-align: center; font-weight: 900; font-size: 32px; margin-bottom: 30px; letter-spacing: 1px; color: #FFD100; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; width: 100%; }
        .sidebar ul li { width: 100%; display: flex; justify-content: center; }
        .sidebar ul li a { display: flex; align-items: center; width: 100%; padding: 15px 30px; color: #FFFFFF; text-decoration: none; font-weight: 500; transition: 0.3s; gap: 15px; opacity: 0.8; }
        .sidebar ul li a i { font-size: 18px; width: 24px; text-align: center; }
        .sidebar ul li a:hover, .sidebar ul li.active a { background: rgba(255, 209, 0, 0.1); color: #FFD100; border-left: 4px solid #FFD100; opacity: 1; }

        /* CONTENIDO PRINCIPAL */
        .content { flex: 1; padding: 30px 40px; overflow-y: auto; }
        
        /* HEADER: TÍTULO Y PERFIL */
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-top h1 { font-size: 28px; font-weight: 700; color: #0F1B2D; margin: 0; }
        .user-profile { display: flex; align-items: center; gap: 12px; background: white; padding: 8px 16px; border-radius: 50px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); cursor: pointer; position: relative; border: 1px solid #E5E7EB; }
        .user-profile span { font-size: 14px; font-weight: 600; color: #374151; }
        .avatar { background: #0F1B2D; color: #FFD100; font-weight: 700; font-size: 13px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }

        /* PANEL DE CONTROLES (BUSCADOR Y FILTROS) */
        .controls-panel { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #E5E7EB; }
        
        .filters { display: flex; gap: 10px; }
        .filter-chip { padding: 8px 18px; background: #F3F4F6; color: #4B5563; border-radius: 50px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; border: 1px solid transparent; }
        .filter-chip:hover { background: #E5E7EB; }
        .filter-chip.active { background: #FFF9D6; color: #0F1B2D; border-color: #FFD100; }

        .search-actions { display: flex; gap: 12px; align-items: center; }
        #inputBuscar { padding: 10px 16px; border: 1px solid #D1D5DB; border-radius: 8px; outline: none; width: 260px; font-family: 'Inter'; font-size: 14px; transition: 0.3s; }
        #inputBuscar:focus { border-color: #0F1B2D; box-shadow: 0 0 0 3px rgba(15,27,45,0.1); }
        
        button { font-family: 'Inter'; cursor: pointer; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; transition: 0.2s; display: flex; align-items: center; gap: 8px; border: none; }
        .btn-filter { background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB; }
        .btn-filter:hover { background: #E5E7EB; }
        
        /* BOTONES PRINCIPALES (AMARILLO MASS) */
        .add-btn { background: #FFD100; color: #0F1B2D; box-shadow: 0 4px 6px -1px rgba(255,209,0,0.3); }
        .add-btn:hover { background: #E6BC00; transform: translateY(-1px); }
        .btn-save { width: 100%; justify-content: center; background: #FFD100; color: #0F1B2D; padding: 14px; font-size: 15px; margin-top: 10px; font-weight: 700; }
        .btn-save:hover { background: #E6BC00; }

        /* TABLA MODERNA */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #E5E7EB; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F9FAFB; padding: 16px 20px; font-size: 12px; text-transform: uppercase; color: #6B7280; font-weight: 700; letter-spacing: 0.5px; border-bottom: 1px solid #E5E7EB; }
        td { padding: 16px 20px; font-size: 14px; color: #111827; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #F9FAFB; }

        /* BADGES / ETIQUETAS ESTADO */
        span.normal { background: #DEF7EC; color: #03543F; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        span.warning { background: #FEF3C7; color: #92400E; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        span.danger { background: #FDE8E8; color: #9B1C1C; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }

        /* ICONOS DE ACCIÓN */
        .action-icon { font-size: 16px; margin-right: 15px; transition: 0.2s; }
        .action-edit { color: #0F1B2D; } .action-edit:hover { color: #1E365A; }
        .action-delete { color: #EF4444; } .action-delete:hover { color: #B91C1C; }

        /* MODALES */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 27, 45, 0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: white; padding: 35px; border-radius: 16px; width: 100%; max-width: 450px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); position: relative; }
        .modal h2 { margin-top: 0; color: #0F1B2D; font-size: 22px; margin-bottom: 25px; font-weight: 800; text-align: center; }
        .close-btn { position: absolute; top: 20px; right: 25px; font-size: 24px; color: #9CA3AF; cursor: pointer; transition: 0.2s; }
        .close-btn:hover { color: #111827; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #4B5563; font-weight: 600; font-size: 13px; }
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border: 1px solid #D1D5DB; border-radius: 8px; box-sizing: border-box; font-family: 'Inter'; font-size: 14px; outline: none; transition: 0.2s; background: #F9FAFB; }
        .form-group input:focus, .form-group select:focus { border-color: #0F1B2D; background: white; box-shadow: 0 0 0 3px rgba(15,27,45,0.1); }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>MASS</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li class="active"><a href="productos.php"><i class="fa-solid fa-box"></i> Productos</a></li>
            <li><a href="movimientos.php"><i class="fa-solid fa-arrow-right-arrow-left"></i> Movimientos</a></li>
            <li><a href="precios.php"><i class="fa-solid fa-tags"></i> Precios</a></li>
            <li><a href="alertas.php"><i class="fa-solid fa-bell"></i> Alertas</a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="header-top">
            <h1>Gestión de Inventario</h1>
            <div class="user-profile" onclick="let menu = document.getElementById('dropdown'); menu.style.display = menu.style.display === 'none' ? 'block' : 'none';">
            <span>Administrador</span>
            <div class="avatar">FV</div>
            <div id="dropdown" style="display: none; position: absolute; top: 55px; right: 0; background: white; border: 1px solid #E5E7EB; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 160px; z-index: 1000; overflow: hidden;">
                <a href="../../backend/cerrar_sesion.php" style="color: #DC2626; padding: 12px 15px; display: flex; align-items: center; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='white'">
                    <i class="fa-solid fa-right-from-bracket" style="margin-right: 10px;"></i> Cerrar Sesión
                </a>
            </div>
        </div>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'codigo_duplicado'): ?>
            <div style="background-color: #FEF2F2; color: #991B1B; padding: 15px; margin-bottom: 20px; border-radius: 8px; border-left: 4px solid #EF4444; font-weight: 500; font-size: 14px;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> <strong>¡Error!</strong> El código que intentaste ingresar ya existe.
            </div>
        <?php endif; ?>

        <div class="controls-panel">
            <div class="filters">
                <span class="filter-chip active">Todos</span>
                <span class="filter-chip">Stock Bajo</span>
                <span class="filter-chip">Agotados</span>
            </div>
            <div class="search-actions">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 12px; color: #9CA3AF;"></i>
                    <input type="text" id="inputBuscar" placeholder="Buscar por nombre o código..." style="padding-left: 40px;">
                </div>
                <button class="btn-filter" onclick="document.getElementById('modalBusqueda').style.display = 'flex'">
                    <i class="fa-solid fa-sliders"></i> Filtros
                </button>
                <button class="add-btn" id="btnAbrirModal">
                    <i class="fa-solid fa-plus"></i> Nuevo Producto
                </button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Mínimo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaProductos">
                   <?php
                    require_once '../../backend/conexion.php';
                    $sql = "SELECT * FROM productos";
                    $resultado = $conexion->query($sql);
                    if ($resultado && $resultado->num_rows > 0) {
                        while($fila = $resultado->fetch_assoc()) {
                            $estado = ($fila['stock'] <= 0) ? "danger" : (($fila['stock'] < $fila['stock_minimo']) ? "warning" : "normal");
                            $textoEstado = ($fila['stock'] <= 0) ? "Agotado" : (($fila['stock'] < $fila['stock_minimo']) ? "Stock Bajo" : "Normal");
                            
                            echo "<tr>
                                    <td><strong>{$fila['codigo']}</strong></td>
                                    <td>{$fila['nombre']}</td>
                                    <td><span style='background: #F3F4F6; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #4B5563;'>{$fila['categoria']}</span></td>
                                    <td style='font-weight: 600; color: #111827;'>S/ {$fila['precio']}</td>
                                    <td>{$fila['stock']}</td>
                                    <td style='color: #6B7280;'>{$fila['stock_minimo']}</td>
                                    <td><span class='$estado'>$textoEstado</span></td>
                                    <td>
                                        <a href='#' data-id='{$fila['id_producto']}' data-nombre='{$fila['nombre']}' data-precio='{$fila['precio']}' data-stock='{$fila['stock']}' onclick='abrirModalEditar(this)' class='action-icon action-edit' title='Editar'><i class='fa-solid fa-pen-to-square'></i></a>
                                        <a href='../../backend/eliminar_producto.php?id={$fila['id_producto']}' class='action-icon action-delete' title='Eliminar' onclick='return confirm(\"¿Seguro que deseas eliminar el producto {$fila['nombre']}?\")'><i class='fa-solid fa-trash-can'></i></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #6B7280;'>No hay productos registrados en el inventario.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalProducto" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarModal">&times;</span>
            <h2>Registrar Nuevo Producto</h2>
            <form action="../../backend/guardar_producto.php" method="POST">
                <div class="form-group"><label>Código del Producto</label><input type="text" name="codigo" required placeholder="Ej. P004"></div>
                <div class="form-group"><label>Nombre</label><input type="text" name="nombre" required placeholder="Ej. Aceite Primor 1L"></div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="categoria" required>
                        <option value="Abarrotes">Abarrotes</option>
                        <option value="Lácteos">Lácteos</option>
                        <option value="Bebidas">Bebidas</option>
                        <option value="Limpieza">Limpieza</option>
                    </select>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;"><label>Precio (S/)</label><input type="number" step="0.10" min="0" name="precio" required placeholder="0.00"></div>
                    <div class="form-group" style="flex: 1;"><label>Stock Mínimo</label><input type="number" min="0" name="stock_minimo" required value="10"></div>
                </div>
                <button type="submit" class="btn-save"><i class="fa-solid fa-check"></i> Guardar Producto</button>
            </form>
        </div>
    </div>

    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarEditar">&times;</span>
            <h2>Editar Producto</h2>
            <form action="../../backend/actualizar_producto.php" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group"><label>Nombre</label><input type="text" name="nombre" id="edit_nombre" required></div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;"><label>Precio (S/)</label><input type="number" step="0.10" min="0" name="precio" id="edit_precio" required></div>
                    <div class="form-group" style="flex: 1;"><label>Stock Actual</label><input type="number" min="0" name="stock" id="edit_stock" required></div>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" class="btn-save" style="flex: 1;"><i class="fa-solid fa-floppy-disk"></i> Actualizar</button>
                    <button type="button" class="btn-save" id="btnCancelarEditar" style="flex: 1; background-color: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;"><i class="fa-solid fa-xmark"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalBusqueda" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarBusqueda">&times;</span>
            <h2>Filtros Avanzados</h2>
            <form id="formBusquedaAvanzada">
                <div class="form-group">
                    <label>Precio Mínimo (S/)</label>
                    <input type="number" id="precioMin" step="0.10" min="0" oninput="if(this.value < 0) this.value = ''" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Precio Máximo (S/)</label>
                    <input type="number" id="precioMax" step="0.10" min="0" oninput="if(this.value < 0) this.value = ''" placeholder="0.00">
                </div>
                <button type="button" class="btn-save" onclick="aplicarFiltroPrecio()"><i class="fa-solid fa-filter"></i> Aplicar Filtros</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("modalProducto");
        const modalBusq = document.getElementById("modalBusqueda");
        const modalEditar = document.getElementById("modalEditar");

        document.getElementById("btnAbrirModal").onclick = () => modal.style.display = "flex";
        document.getElementById("btnCerrarModal").onclick = () => modal.style.display = "none";
        document.getElementById("btnCerrarBusqueda").onclick = () => modalBusq.style.display = "none";
        document.getElementById("btnCerrarEditar").onclick = () => modalEditar.style.display = "none";
        document.getElementById("btnCancelarEditar").onclick = () => modalEditar.style.display = "none";

        window.onclick = (e) => { 
            if (e.target == modal) modal.style.display = "none";
            if (e.target == modalBusq) modalBusq.style.display = "none";
            if (e.target == modalEditar) modalEditar.style.display = "none";
        }

        function abrirModalEditar(elemento) {
            document.getElementById('edit_id').value = elemento.getAttribute('data-id');
            document.getElementById('edit_nombre').value = elemento.getAttribute('data-nombre');
            document.getElementById('edit_precio').value = elemento.getAttribute('data-precio');
            document.getElementById('edit_stock').value = elemento.getAttribute('data-stock');
            modalEditar.style.display = 'flex';
        }

        const inputBuscar = document.getElementById('inputBuscar');
        const filtros = document.querySelectorAll('.filter-chip');
        let filtroActual = 'Todos';

        function actualizarTabla() {
            let q = inputBuscar.value;
            fetch(`../../backend/filtrar_productos.php?q=${q}&f=${filtroActual}`)
                .then(res => res.text())
                .then(html => document.getElementById('tablaProductos').innerHTML = html);
        }

        function aplicarFiltroPrecio() {
            let min = document.getElementById('precioMin').value;
            let max = document.getElementById('precioMax').value;
            fetch(`../../backend/filtrar_productos.php?min=${min}&max=${max}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('tablaProductos').innerHTML = html;
                    modalBusq.style.display = 'none';
                });
        }

        inputBuscar.addEventListener('input', actualizarTabla);
        filtros.forEach(chip => {
            chip.addEventListener('click', () => {
                filtros.forEach(f => f.classList.remove('active'));
                chip.classList.add('active');
                filtroActual = chip.innerText;
                actualizarTabla();
            });
        });
    </script>
</body>
</html>