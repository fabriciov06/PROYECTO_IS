<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos | MASS</title>
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
        .user-profile { display: flex; align-items: center; gap: 12px; background: white; padding: 8px 16px; border-radius: 50px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); cursor: pointer; position: relative; border: 1px solid #E5E7EB; }
        .user-profile span { font-size: 14px; font-weight: 600; color: #374151; }
        .avatar { background: #0F1B2D; color: #FFD100; font-weight: 700; font-size: 13px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }

        /* CONTROLES */
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
        .add-btn { background: #FFD100; color: #0F1B2D; box-shadow: 0 4px 6px -1px rgba(255,209,0,0.3); }
        .add-btn:hover { background: #E6BC00; transform: translateY(-1px); }
        .btn-save { width: 100%; justify-content: center; background: #FFD100; color: #0F1B2D; padding: 14px; font-size: 15px; font-weight: 700; }
        .btn-save:hover { background: #E6BC00; }
        .btn-limpiar { width: 100%; justify-content: center; background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB; padding: 14px; font-size: 15px; font-weight: 700; }
        .btn-limpiar:hover { background: #E5E7EB; }

        /* TABLA */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #E5E7EB; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F9FAFB; padding: 16px 20px; font-size: 12px; text-transform: uppercase; color: #6B7280; font-weight: 700; border-bottom: 1px solid #E5E7EB; }
        td { padding: 16px 20px; font-size: 14px; color: #111827; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
        tr:hover td { background: #F9FAFB; }
        span.normal { background: #DEF7EC; color: #03543F; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        span.warning { background: #FEF3C7; color: #92400E; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        span.danger { background: #FDE8E8; color: #9B1C1C; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
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
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border: 1px solid #D1D5DB; border-radius: 8px; box-sizing: border-box; font-family: 'Inter'; font-size: 14px; outline: none; background: #F9FAFB; }
        .form-group input:focus, .form-group select:focus { border-color: #0F1B2D; background: white; box-shadow: 0 0 0 3px rgba(15,27,45,0.1); }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>MASS</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li class="active"><a href="productos.php"><i class="fa-solid fa-book-open"></i> Catálogo</a></li>
            <li><a href="movimientos.php"><i class="fa-solid fa-arrow-right-arrow-left"></i> Movimientos</a></li>
            <li><a href="control_compras.php"><i class="fa-solid fa-clipboard-check"></i> Control de Compras</a></li>
            <li><a href="recepciones.php"><i class="fa-solid fa-boxes-packing"></i> Recepciones</a></li>
            <li><a href="reclamos.php"><i class="fa-solid fa-triangle-exclamation"></i> Reclamos</a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="header-top">
            <div>
                <h1>Catálogo de Productos</h1>
                <p style="color: #6B7280; font-size: 14px; margin-top: 5px; margin-bottom: 0;">Gestiona el inventario activo de la tienda.</p>
            </div>
            <div class="user-profile" onclick="let menu = document.getElementById('dropdown'); menu.style.display = menu.style.display === 'none' ? 'block' : 'none';">
                <span>Administrador</span>
                <div class="avatar">FV</div>
                <div id="dropdown" style="display: none; position: absolute; top: 55px; right: 0; background: white; border: 1px solid #E5E7EB; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 160px; z-index: 1000; overflow: hidden;">
                    <a href="../../backend/acciones/cerrar_sesion.php" style="color: #DC2626; padding: 12px 15px; display: flex; align-items: center; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='white'">
                        <i class="fa-solid fa-right-from-bracket" style="margin-right: 10px;"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>

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
                   </tbody>
            </table>
        </div>
    </main>

    <div id="modalBusqueda" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarBusqueda">&times;</span>
            <h2>Filtros Avanzados</h2>
            <form id="formBusquedaAvanzada">
                <div class="form-group">
                    <label>Categoría</label>
                    <select id="categoriaBusqueda">
                        <option value="">Todas las categorías</option>
                        <option value="Abarrotes">Abarrotes</option>
                        <option value="Lácteos">Lácteos</option>
                        <option value="Bebidas">Bebidas</option>
                        <option value="Limpieza">Limpieza</option>
                    </select>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Precio Mín. (S/)</label>
                        <input type="number" id="precioMin" step="0.10" min="0" oninput="if(this.value < 0) this.value = ''" placeholder="0.00">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Precio Máx. (S/)</label>
                        <input type="number" id="precioMax" step="0.10" min="0" oninput="if(this.value < 0) this.value = ''" placeholder="0.00">
                    </div>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="button" class="btn-save" style="flex: 1;" onclick="aplicarFiltrosAvanzados()"><i class="fa-solid fa-filter"></i> Aplicar</button>
                    <button type="button" class="btn-limpiar" style="flex: 1;" onclick="limpiarFiltrosCatalogo()"><i class="fa-solid fa-eraser"></i> Limpiar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalBusq = document.getElementById("modalBusqueda");
        document.getElementById("btnCerrarBusqueda").onclick = () => modalBusq.style.display = "none";

        const inputBuscar = document.getElementById('inputBuscar');
        const filtros = document.querySelectorAll('.filter-chip');
        let filtroActual = 'Todos';

        function actualizarTabla() {
            let q = inputBuscar.value;
            let min = document.getElementById('precioMin').value;
            let max = document.getElementById('precioMax').value;
            let cat = document.getElementById('categoriaBusqueda').value;

            fetch(`../../backend/acciones/filtrar_productos.php?q=${q}&f=${filtroActual}&min=${min}&max=${max}&cat=${cat}`)
                .then(res => res.text())
                .then(html => document.getElementById('tablaProductos').innerHTML = html);
        }

        function aplicarFiltrosAvanzados() {
            actualizarTabla();
            modalBusq.style.display = 'none';
        }

        function limpiarFiltrosCatalogo() {
            document.getElementById('precioMin').value = '';
            document.getElementById('precioMax').value = '';
            document.getElementById('categoriaBusqueda').value = '';
            document.getElementById('inputBuscar').value = '';
            
            // Reiniciar los chips al "Todos"
            filtros.forEach(f => f.classList.remove('active'));
            filtros[0].classList.add('active');
            filtroActual = 'Todos';

            actualizarTabla();
            modalBusq.style.display = 'none';
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

        // Carga inicial
        window.onload = actualizarTabla;
    </script>
</body>
</html>