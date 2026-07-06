<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Movimientos | MASS</title>
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
        
        /* HEADER: TÍTULO Y PERFIL */
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-top h1 { font-size: 28px; font-weight: 700; color: #0F1B2D; margin: 0; }
        .user-profile { display: flex; align-items: center; gap: 12px; background: white; padding: 8px 16px; border-radius: 50px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); cursor: pointer; border: 1px solid #E5E7EB; }
        .user-profile span { font-size: 14px; font-weight: 600; color: #374151; }
        .avatar { background: #0F1B2D; color: #FFD100; font-weight: 700; font-size: 13px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }

        /* PANEL DE FILTROS (KÁRDEX) */
        .filters-panel { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 25px; border: 1px solid #E5E7EB; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
        
        .filter-group { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 150px; }
        .filter-group label { font-size: 13px; font-weight: 600; color: #4B5563; }
        .filter-group input, .filter-group select { padding: 10px 12px; border: 1px solid #D1D5DB; border-radius: 8px; outline: none; font-family: 'Inter'; font-size: 14px; background: #F9FAFB; transition: 0.3s; }
        .filter-group input:focus, .filter-group select:focus { border-color: #0F1B2D; background: white; box-shadow: 0 0 0 3px rgba(15,27,45,0.1); }
        
        .btn-consultar { background: #FFD100; color: #0F1B2D; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(255,209,0,0.3); }
        .btn-consultar:hover { background: #E6BC00; transform: translateY(-1px); }

        .btn-limpiar { background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-limpiar:hover { background: #E5E7EB; }

        /* TABLA DE MOVIMIENTOS */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #E5E7EB; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F9FAFB; padding: 16px 20px; font-size: 12px; text-transform: uppercase; color: #6B7280; font-weight: 700; border-bottom: 1px solid #E5E7EB; }
        td { padding: 16px 20px; font-size: 14px; color: #111827; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
        tr:hover td { background: #F9FAFB; }

        /* ETIQUETAS DE TIPO DE MOVIMIENTO */
        .badge-entrada { background: #DEF7EC; color: #03543F; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-salida { background: #FDE8E8; color: #9B1C1C; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-ajuste { background: #E5E7EB; color: #374151; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        
        .cantidad-pos { color: #059669; font-weight: 700; }
        .cantidad-neg { color: #DC2626; font-weight: 700; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>MASS</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="productos.php"><i class="fa-solid fa-book-open"></i> Catálogo</a></li>
            <li class="active"><a href="movimientos.php"><i class="fa-solid fa-arrow-right-arrow-left"></i> Movimientos</a></li>
            <li><a href="control_compras.php"><i class="fa-solid fa-clipboard-check"></i> Control de Compras</a></li>
            <li><a href="recepciones.php"><i class="fa-solid fa-boxes-packing"></i> Recepciones</a></li>
            <li><a href="reclamos.php"><i class="fa-solid fa-triangle-exclamation"></i> Reclamos</a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="header-top">
            <div>
                <h1>Historial de Movimientos</h1>
                <p style="color: #6B7280; font-size: 14px; margin-top: 5px; margin-bottom: 0;">Consulta las entradas, salidas y ajustes del inventario.</p>
            </div>
            <div class="user-profile">
                <span>Administrador</span>
                <div class="avatar">FV</div>
            </div>
        </div>

        <div class="filters-panel">
            <div class="filter-group">
                <label>Fecha Inicio</label>
                <input type="date" id="fechaInicio">
            </div>
            <div class="filter-group">
                <label>Fecha Fin</label>
                <input type="date" id="fechaFin">
            </div>
            <div class="filter-group" style="min-width: 190px;">
                <label>Tipo de Movimiento</label>
                <select id="tipoMovimiento">
                    <option value="">Todos los movimientos</option>
                    <option value="Entrada">Entradas</option>
                    <option value="Salida">Salidas</option>
                    <option value="Ajuste">Ajustes</option>
                </select>
            </div>
            <div class="filter-group" style="flex: 2;">
                <label>Buscar Producto</label>
                <input type="text" id="buscarProducto" placeholder="Código o nombre del producto...">
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn-consultar" onclick="actualizarTabla()"><i class="fa-solid fa-magnifying-glass"></i> Consultar</button>
                <button class="btn-limpiar" onclick="limpiarFiltros()"><i class="fa-solid fa-eraser"></i> Limpiar</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Responsable</th>
                        <th>Documento / Motivo</th>
                    </tr>
                </thead>
                <tbody id="tablaMovimientos">
                    </tbody>
            </table>
        </div>
    </main>

    <script>
        // Función que llama a la base de datos para filtrar en tiempo real
        function actualizarTabla() {
            let inicio = document.getElementById('fechaInicio').value;
            let fin = document.getElementById('fechaFin').value;
            let tipo = document.getElementById('tipoMovimiento').value;
            let q = document.getElementById('buscarProducto').value;

            // Llama al archivo PHP del backend enviando los filtros
            fetch(`../../backend/filtrar_movimientos.php?inicio=${inicio}&fin=${fin}&tipo=${tipo}&q=${q}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('tablaMovimientos').innerHTML = html;
                });
        }

        function limpiarFiltros() {
            document.getElementById('fechaInicio').value = '';
            document.getElementById('fechaFin').value = '';
            document.getElementById('tipoMovimiento').value = '';
            document.getElementById('buscarProducto').value = '';
            actualizarTabla(); // Recarga la tabla automáticamente
        }

        // Cargar tabla con todos los datos apenas se abra la página
        window.onload = actualizarTabla;
    </script>
</body>
</html>