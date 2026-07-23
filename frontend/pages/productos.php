<?php
session_start();
if (!isset($_SESSION['usuario_logeado'])) {
    header("Location: login.php");
    exit();
}

// RNF-15: Cierre de sesión automático tras 30 minutos (1800 segundos) de inactividad
$tiempoInactividad = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $tiempoInactividad)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos | MASS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* RESET & BASE */
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #F3F4F6; margin: 0; display: flex; color: #374151; height: 100vh; overflow: hidden; }
        
        /* BARRA LATERAL */
        .sidebar { background: #0F1B2D; color: white; width: 260px; padding: 20px 0; display: flex; flex-direction: column; box-shadow: 4px 0 10px rgba(0,0,0,0.05); z-index: 10; }
        .sidebar h2 { text-align: center; font-weight: 900; font-size: 32px; margin-bottom: 30px; letter-spacing: 1px; color: #FFD100; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; width: 100%; }
        .sidebar ul li { width: 100%; display: flex; justify-content: center; }
        .sidebar ul li a { display: flex; align-items: center; width: 100%; padding: 15px 30px; color: #FFFFFF; text-decoration: none; font-weight: 500; transition: 0.3s; gap: 15px; opacity: 0.8; }
        .sidebar ul li a i { font-size: 18px; width: 24px; text-align: center; }
        .sidebar ul li a:hover, .sidebar ul li.active a { background: rgba(255, 209, 0, 0.1); color: #FFD100; border-left: 4px solid #FFD100; opacity: 1; }
        .sidebar ul li.disabled a { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

        /* CONTENIDO PRINCIPAL */
        .content { flex: 1; padding: 30px 40px; overflow-y: auto; }
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header-top h1 { font-size: 28px; font-weight: 800; color: #0F1B2D; margin: 0; }
        .user-profile { display: flex; align-items: center; gap: 12px; background: white; padding: 8px 16px; border-radius: 50px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); cursor: pointer; position: relative; border: 1px solid #E5E7EB; }
        .user-profile span { font-size: 14px; font-weight: 600; color: #374151; }
        .avatar { background: #0F1B2D; color: #FFD100; font-weight: 700; font-size: 13px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }

        /* PANEL DE CONTROLES */
        .controls-panel { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: flex-end; align-items: center; border: 1px solid #E5E7EB; }

        .search-actions { display: flex; gap: 12px; align-items: center; }
        #inputBuscar { padding: 10px 16px; border: 1px solid #D1D5DB; border-radius: 8px; outline: none; width: 260px; font-family: 'Inter'; font-size: 14px; transition: 0.3s; }
        #inputBuscar:focus { border-color: #0F1B2D; box-shadow: 0 0 0 3px rgba(15,27,45,0.1); }
        
        button { font-family: 'Inter'; cursor: pointer; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; transition: 0.2s; display: flex; align-items: center; gap: 8px; border: none; }
        .btn-filter { background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB; }
        .btn-filter:hover { background: #E5E7EB; }
        .add-btn { background: #FFD100; color: #0F1B2D; box-shadow: 0 4px 6px -1px rgba(255,209,0,0.3); }
        .add-btn:hover { background: #E6BC00; transform: translateY(-1px); }
        .btn-save { width: 100%; justify-content: center; background: #FFD100; color: #0F1B2D; padding: 12px; font-size: 15px; font-weight: 700; border-radius: 8px; }
        .btn-save:hover { background: #E6BC00; }
        .btn-cancel { width: 100%; justify-content: center; background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB; padding: 12px; font-size: 15px; font-weight: 700; border-radius: 8px; }
        .btn-cancel:hover { background: #E5E7EB; }

        /* TABLA DE PRODUCTOS */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #E5E7EB; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #F9FAFB; padding: 16px 20px; font-size: 12px; text-transform: uppercase; color: #6B7280; font-weight: 700; border-bottom: 1px solid #E5E7EB; }
        td { padding: 16px 20px; font-size: 14px; color: #111827; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
        tr:hover td { background: #F9FAFB; }
        span.normal { background: #DEF7EC; color: #03543F; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        span.warning { background: #FEF3C7; color: #92400E; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        span.danger { background: #FDE8E8; color: #9B1C1C; padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .action-icon { font-size: 16px; margin-right: 15px; transition: 0.2s; text-decoration: none; display: inline-block; }
        .action-edit { color: #0F1B2D; } .action-edit:hover { color: #1E365A; }
        .action-delete { color: #EF4444; } .action-delete:hover { color: #B91C1C; }

        /* MODALES */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 27, 45, 0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: white; padding: 30px 35px; border-radius: 16px; width: 100%; max-width: 550px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); position: relative; max-height: 90vh; overflow-y: auto; }
        .modal h2 { margin-top: 0; color: #0F1B2D; font-size: 22px; margin-bottom: 20px; font-weight: 800; text-align: center; }
        .close-btn { position: absolute; top: 20px; right: 25px; font-size: 24px; color: #9CA3AF; cursor: pointer; transition: 0.2s; }
        .close-btn:hover { color: #111827; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group.full-width { grid-column: span 2; }
        .form-group label { display: block; margin-bottom: 6px; color: #4B5563; font-weight: 600; font-size: 13px; }
        .form-group label span.req { color: #EF4444; font-weight: 700; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid #D1D5DB; border-radius: 8px; box-sizing: border-box; font-family: 'Inter'; font-size: 14px; outline: none; background: #F9FAFB; }
        .form-group input.input-error, .form-group select.input-error, .form-group textarea.input-error { border-color: #EF4444 !important; background: #FEF2F2 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.2) !important; }
        .field-error-text { color: #DC2626; font-size: 12px; font-weight: 600; margin-top: 4px; display: none; }
        
        /* INDICADOR DE DISPONIBILIDAD DE CÓDIGO */
        .status-indicator { display: block; font-size: 12px; font-weight: 600; margin-top: 4px; }
        .status-available { color: #059669; }
        .status-duplicate { color: #DC2626; }
        .status-invalid { color: #DC2626; }
        .status-deactivated { color: #D97706; }

        /* TOAST DE NOTIFICACIÓN */
        .toast { position: fixed; bottom: 30px; right: 30px; background: #0F1B2D; color: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2); font-weight: 600; display: flex; align-items: center; gap: 12px; z-index: 2000; opacity: 0; transform: translateY(20px); transition: 0.3s; pointer-events: none; border-left: 5px solid #FFD100; }
        .toast.show { opacity: 1; transform: translateY(0); pointer-events: auto; }

        /* ESTILOS DE PAGINACIÓN */
        .page-btn { padding: 6px 12px; border: 1px solid #D1D5DB; border-radius: 6px; background: white; color: #374151; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px; }
        .page-btn:hover:not(:disabled) { background: #F3F4F6; border-color: #9CA3AF; }
        .page-btn.active { background: #0F1B2D; color: white; border-color: #0F1B2D; }
        .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    </style>
</head>
<body>

    <!-- BARRA LATERAL -->
    <aside class="sidebar">
        <h2>MASS</h2>
        <ul>
            <li class="disabled"><a href="javascript:void(0)" title="Módulo fuera del alcance actual"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li class="active"><a href="productos.php"><i class="fa-solid fa-book-open"></i> Catálogo de Productos</a></li>
            <li class="disabled"><a href="javascript:void(0)" title="Módulo fuera del alcance actual"><i class="fa-solid fa-arrow-right-arrow-left"></i> Movimientos</a></li>
            <li class="disabled"><a href="javascript:void(0)" title="Módulo fuera del alcance actual"><i class="fa-solid fa-clipboard-check"></i> Control de Compras</a></li>
            <li class="disabled"><a href="javascript:void(0)" title="Módulo fuera del alcance actual"><i class="fa-solid fa-boxes-packing"></i> Recepciones</a></li>
            <li class="disabled"><a href="javascript:void(0)" title="Módulo fuera del alcance actual"><i class="fa-solid fa-triangle-exclamation"></i> Reclamos</a></li>
            <li class="disabled"><a href="javascript:void(0)" title="Módulo fuera del alcance actual"><i class="fa-solid fa-users-gear"></i> Gestión de Cuentas</a></li>
            <li class="disabled"><a href="javascript:void(0)" title="Módulo fuera del alcance actual"><i class="fa-solid fa-file-shield"></i> Auditoría de Sistema</a></li>
        </ul>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="content">
        <div class="header-top">
            <div>
                <h1>Catálogo de Productos</h1>
                <p style="color: #6B7280; font-size: 14px; margin-top: 5px; margin-bottom: 0;">Gestión de inventario de la tienda MASS. Presiona <kbd style="background:#E5E7EB; padding:2px 6px; border-radius:4px; font-weight:700;">Alt + N</kbd> para agregar.</p>
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

        <!-- BARRA DE CONTROLES (Buscador a la izquierda, Acciones y Chips a la derecha) -->
        <div class="controls-panel" style="display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
            <div class="search-actions" style="flex: 1; max-width: 420px; min-width: 260px;">
                <div style="position: relative; width: 100%;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 12px; color: #9CA3AF;"></i>
                    <input type="text" id="inputBuscar" placeholder="Buscar por nombre o código..." style="padding-left: 40px; width: 100%;">
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button class="btn-filter" id="btnRefrescar" onclick="actualizarTabla(1); mostrarToast('Listado actualizado.');" title="Actualizar datos del listado">
                    <i class="fa-solid fa-rotate-right"></i> Refrescar
                </button>
                <button class="btn-filter" onclick="abrirModalFiltros()">
                    <i class="fa-solid fa-sliders"></i> Filtros
                </button>
                <button class="add-btn" id="btnAbrirModal" title="Atajo: Alt + N">
                    <i class="fa-solid fa-plus"></i> Agregar Producto <span style="font-size: 11px; opacity: 0.8;">(Alt+N)</span>
                </button>
            </div>
        </div>

        <!-- BARRA DE PAGINACIÓN SUPERIOR -->
        <div class="pagination-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; background: white; border: 1px solid #E5E7EB; border-radius: 8px; margin-bottom: 15px; font-size: 13px; color: #4B5563; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span>Mostrar:</span>
                <select id="selectLimite" style="padding: 6px 12px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 13px; font-weight: 600; background: #F9FAFB; cursor: pointer; outline: none;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>registros</span>
            </div>
            <div id="counterRangeTextTop" style="font-weight: 600; color: #374151;">
                Cargando registros...
            </div>
        </div>

        <!-- TABLA DE PRODUCTOS (i_ListadoProductos) -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Stock Mín.</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaProductos">
                    <!-- Carga asíncrona mediante AJAX -->
                </tbody>
            </table>
        </div>

        <!-- BARRA DE PAGINACIÓN INFERIOR -->
        <div class="pagination-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: white; border: 1px solid #E5E7EB; border-radius: 8px; margin-top: 15px; font-size: 13px; color: #4B5563; flex-wrap: wrap; gap: 10px;">
            <div id="counterRangeTextBottom" style="font-weight: 600; color: #374151;">
                Cargando registros...
            </div>
            <div class="pagination-controls" style="display: flex; align-items: center; gap: 5px;" id="paginationButtons">
                <!-- Se genera dinámicamente: < Anterior 1 2 3 ... Siguiente > -->
            </div>
        </div>
    </main>

    <!-- MODAL AGREGAR PRODUCTO (i_AgregarProducto) -->
    <div id="modalAgregarProducto" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarAgregar">&times;</span>
            <h2><i class="fa-solid fa-cart-plus" style="color: #FFD100; margin-right: 8px;"></i> Agregar Producto</h2>
            
            <div id="alertErrorAgregar" style="display: none; background: #FEE2E2; color: #991B1B; padding: 12px 15px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-bottom: 15px; border: 1px solid #FCA5A5;"></div>

            <form id="formAgregarProducto">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Código del Producto <span class="req">*</span></label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="text" id="add_codigo" name="codigo" placeholder="Ej: P0000001" required autocomplete="off" style="flex: 1;">
                            <button type="button" id="btnGenerarCodigoAuto" class="btn-filter" style="padding: 10px 14px; font-size: 12px; white-space: nowrap;" title="Generar código sugerido automáticamente">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Auto
                            </button>
                        </div>
                        <span id="indicatorCodigo" class="status-indicator"></span>
                    </div>

                    <div class="form-group full-width">
                        <label>Nombre del Producto <span class="req">*</span></label>
                        <input type="text" id="add_nombre" name="nombre" placeholder="Ej: Leche Gloria Evaporada 400g" required>
                        <div id="err_add_nombre" class="field-error-text"></div>
                    </div>

                    <div class="form-group">
                        <label>Categoría <span class="req">*</span></label>
                        <select id="add_categoria" name="categoria" required>
                            <option value="">Cargando categorías...</option>
                        </select>
                        <div id="err_add_categoria" class="field-error-text"></div>
                    </div>

                    <div class="form-group">
                        <label>Presentación <span class="req">*</span></label>
                        <select id="add_unidad_medida" name="unidad_medida" required>
                            <option value="unidad" selected>Unidad</option>
                            <option value="paquete">Paquete</option>
                            <option value="caja">Caja</option>
                        </select>
                        <div id="err_add_unidad_medida" class="field-error-text"></div>
                    </div>

                    <div class="form-group">
                        <label>Precio Unitario (S/) <span class="req">*</span></label>
                        <input type="number" id="add_precio" name="precio" step="0.01" placeholder="0.00" required>
                        <div id="err_add_precio" class="field-error-text"></div>
                    </div>

                    <div class="form-group">
                        <label>Stock Inicial <span class="req">*</span></label>
                        <input type="number" id="add_stock" name="stock" value="0" required>
                        <div id="err_add_stock" class="field-error-text"></div>
                    </div>

                    <div class="form-group full-width">
                        <label>Stock Mínimo Alerta <span class="req">*</span></label>
                        <input type="number" id="add_stock_minimo" name="stock_minimo" value="0" required>
                        <div id="err_add_stock_minimo" class="field-error-text"></div>
                    </div>

                    <div class="form-group full-width">
                        <label>Descripción Adicional</label>
                        <textarea id="add_descripcion" name="descripcion" rows="2" placeholder="Detalles o especificaciones opcionales del producto..."></textarea>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 15px;">
                    <button type="button" class="btn-cancel" style="flex: 1;" onclick="cerrarModalAgregar()">Cancelar</button>
                    <button type="button" id="btnPreGuardar" class="btn-save" style="flex: 1;" title="Atajo: Alt + G">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar (Alt+G)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CONFIRMACIÓN DE REGISTRO (RNF-03) -->
    <div id="modalConfirmacionGuardar" class="modal">
        <div class="modal-content" style="max-width: 420px; text-align: center;">
            <div style="font-size: 48px; color: #FFD100; margin-bottom: 15px;"><i class="fa-solid fa-circle-question"></i></div>
            <h2>Confirmar Registro</h2>
            <p style="color: #4B5563; font-size: 14px; margin-bottom: 25px;">¿Está seguro de registrar este nuevo producto en el inventario?</p>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-cancel" style="flex: 1;" onclick="document.getElementById('modalConfirmacionGuardar').style.display='none'">Cancelar</button>
                <button type="button" id="btnEjecutarGuardar" class="btn-save" style="flex: 1;">Sí, Registrar</button>
            </div>
        </div>
    </div>

    <!-- MODAL REACTIVACIÓN DE PRODUCTO (Flujo 4.3) -->
    <div id="modalReactivar" class="modal">
        <div class="modal-content" style="max-width: 440px; text-align: center;">
            <div style="font-size: 48px; color: #F59E0B; margin-bottom: 15px;"><i class="fa-solid fa-rotate-left"></i></div>
            <h2>Producto Desactivado</h2>
            <p id="msgReactivar" style="color: #4B5563; font-size: 14px; margin-bottom: 25px;">Este código corresponde a un producto desactivado. ¿Desea reactivarlo?</p>
            <input type="hidden" id="reactivar_codigo">
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-cancel" style="flex: 1;" onclick="document.getElementById('modalReactivar').style.display='none'">No, Cancelar</button>
                <button type="button" id="btnEjecutarReactivar" class="btn-save" style="flex: 1; background: #F59E0B; color: white;">Sí, Reactivar</button>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR PRODUCTO (i_ModificarProducto) -->
    <div id="modalEditarProducto" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="btnCerrarEditar" onclick="cerrarModalEditar()">&times;</span>
            <h2><i class="fa-solid fa-pen-to-square" style="color: #0F1B2D; margin-right: 8px;"></i> Modificar Producto</h2>
            
            <div id="alertErrorEditar" style="display: none; background: #FEE2E2; color: #991B1B; padding: 12px 15px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-bottom: 15px; border: 1px solid #FCA5A5;"></div>

            <form id="formEditarProducto">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Código del Producto</label>
                        <input type="text" id="edit_codigo" disabled style="background: #E5E7EB; cursor: not-allowed; font-weight: 700;">
                    </div>
                    <div class="form-group full-width">
                        <label>Nombre del Producto <span class="req">*</span></label>
                        <input type="text" id="edit_nombre" name="nombre" placeholder="Ej: Leche Gloria Evaporada 400g" required>
                    </div>
                    <div class="form-group">
                        <label>Categoría <span class="req">*</span></label>
                        <select id="edit_categoria" name="categoria" required></select>
                    </div>
                    <div class="form-group">
                        <label>Presentación <span class="req">*</span></label>
                        <select id="edit_unidad_medida" name="unidad_medida" required>
                            <option value="unidad" selected>Unidad</option>
                            <option value="paquete">Paquete</option>
                            <option value="caja">Caja</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Precio Unitario (S/) <span class="req">*</span></label>
                        <input type="number" id="edit_precio" name="precio" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Actual <span class="req">*</span></label>
                        <input type="number" id="edit_stock" name="stock" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Stock Mínimo <span class="req">*</span></label>
                        <input type="number" id="edit_stock_minimo" name="stock_minimo" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Descripción Adicional</label>
                        <textarea id="edit_descripcion" name="descripcion" rows="2" placeholder="Detalles o especificaciones opcionales del producto..."></textarea>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 15px;">
                    <button type="button" class="btn-cancel" style="flex: 1;" onclick="cerrarModalEditar()">Cancelar</button>
                    <button type="button" id="btnPreGuardarEditar" class="btn-save" style="flex: 1;" title="Atajo: Alt + G">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios (Alt+G)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CONFIRMACIÓN DE MODIFICACIÓN (RNF-03 / Flujo 7.1) -->
    <div id="modalConfirmacionEditar" class="modal">
        <div class="modal-content" style="max-width: 420px; text-align: center;">
            <div style="font-size: 48px; color: #FFD100; margin-bottom: 15px;"><i class="fa-solid fa-circle-question"></i></div>
            <h2>Confirmar Modificación</h2>
            <p style="color: #4B5563; font-size: 14px; margin-bottom: 25px;">¿Está seguro de guardar los cambios realizados en este producto?</p>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-cancel" style="flex: 1;" onclick="cerrarModalConfirmacionEditar()">Cancelar</button>
                <button type="button" id="btnEjecutarActualizar" class="btn-save" style="flex: 1;">Sí, Actualizar</button>
            </div>
        </div>
    </div>
<!-- MODAL CONFIRMACIÓN DE DESACTIVACIÓN -->
<div id="modalConfirmacionDesactivar" class="modal">
    <div class="modal-content" style="max-width: 450px; text-align: center;">

        <div style="font-size: 48px; color: #DC2626; margin-bottom: 15px;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <h2>Desactivar Producto</h2>

        <div
            id="alertErrorDesactivar"
            style="
                display: none;
                background: #FEE2E2;
                color: #991B1B;
                padding: 12px 15px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                margin-bottom: 15px;
                border: 1px solid #FCA5A5;
                text-align: left;
            "
        ></div>

        <div
            style="
                background: #F3F4F6;
                padding: 16px;
                border-radius: 10px;
                border: 1px solid #E5E7EB;
                text-align: left;
                margin-bottom: 20px;
                font-size: 14px;
                color: #374151;
            "
        >
            <div style="margin-bottom: 6px;">
                <strong>Código:</strong>
                <span id="desactivar_codigo"></span>
            </div>

            <div style="margin-bottom: 6px;">
                <strong>Nombre:</strong>
                <span id="desactivar_nombre"></span>
            </div>

            <div style="margin-bottom: 6px;">
                <strong>Categoría:</strong>
                <span id="desactivar_categoria"></span>
            </div>

            <div style="margin-bottom: 6px;">
                <strong>Estado:</strong>
                <span id="desactivar_estado"></span>
            </div>

            <div>
                <strong>Stock:</strong>
                <span id="desactivar_stock"></span>
            </div>
        </div>

        <p
            style="
                color: #4B5563;
                font-size: 14px;
                margin-bottom: 25px;
                text-align: left;
                line-height: 1.5;
            "
        >
            ¿Está seguro de desactivar este producto? El producto dejará de estar disponible para nuevas operaciones, pero conservará su historial.
        </p>

        <input type="hidden" id="desactivar_id">

        <div style="display: flex; gap: 10px;">
            <button
                type="button"
                id="btnCancelarDesactivar"
                class="btn-cancel"
                style="flex: 1;"
            >
                Cancelar
            </button>

            <button
                type="button"
                id="btnEjecutarDesactivar"
                class="btn-save"
                style="flex: 1; background: #DC2626; color: white;"
            >
                Desactivar
            </button>
        </div>

    </div>
</div>
    <!-- MODAL FILTROS AVANZADOS -->
    <div id="modalBusqueda" class="modal">
        <div class="modal-content" style="max-width: 450px;">
            <span class="close-btn" id="btnCerrarBusqueda">&times;</span>
            <h2><i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Filtros Avanzados</h2>

            <form id="formBusquedaAvanzada">
                <div class="form-group">
                    <label>Categoría</label>
                    <select id="categoriaBusqueda">
                        <option value="">Todas las categorías</option>
                    </select>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1; margin-bottom: 5px;">
                        <label>Precio Mín. (S/)</label>
                        <input type="text" id="precioMin" inputmode="decimal" placeholder="0.00" autocomplete="off">
                        <!-- Espacio siempre reservado: el layout no se mueve al aparecer/desaparecer -->
                        <span id="errorPrecioMin" class="status-indicator status-invalid" style="visibility: hidden; min-height: 18px; white-space: nowrap;">Solo números (máx. 2 decimales)</span>
                    </div>
                    <div class="form-group" style="flex: 1; margin-bottom: 5px;">
                        <label>Precio Máx. (S/)</label>
                        <input type="text" id="precioMax" inputmode="decimal" placeholder="0.00" autocomplete="off">
                        <span id="errorPrecioMax" class="status-indicator status-invalid" style="visibility: hidden; min-height: 18px; white-space: nowrap;">Solo números (máx. 2 decimales)</span>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Estado de stock</label>
                    <select id="estadoStockBusqueda">
                        <option value="Todos">Todos</option>
                        <option value="Normal">Normal</option>
                        <option value="Stock Bajo">Stock Bajo</option>
                        <option value="Agotado">Agotado</option>
                    </select>
                </div>
                <!-- Mensaje general (mín > máx): texto simple con espacio siempre reservado; hace de separación ante los botones -->
                <span id="alertErrorFiltros" class="status-indicator status-invalid" style="visibility: hidden; min-height: 18px; white-space: nowrap;">El mínimo no puede superar al máximo</span>
                <div style="display: flex; gap: 10px; margin-top: 4px;">
                    <button type="button" class="btn-save" style="flex: 1;" onclick="aplicarFiltrosAvanzados()"><i class="fa-solid fa-filter"></i> Aplicar</button>
                    <button type="button" class="btn-cancel" style="flex: 1;" onclick="limpiarFiltrosCatalogo()"><i class="fa-solid fa-eraser"></i> Limpiar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- NOTIFICACIÓN TOAST -->
    <div id="toastNotification" class="toast">
        <i class="fa-solid fa-circle-check" style="font-size: 20px; color: #FFD100;"></i>
        <span id="toastMessage">Acción completada con éxito.</span>
    </div>

    <script>
        const modalAgregar = document.getElementById("modalAgregarProducto");
        const modalEditar = document.getElementById("modalEditarProducto");
        const modalConfirmacionEditar = document.getElementById("modalConfirmacionEditar");
        const modalBusq = document.getElementById("modalBusqueda");
        const inputCodigo = document.getElementById("add_codigo");
        const indicatorCodigo = document.getElementById("indicatorCodigo");
        const alertError = document.getElementById("alertErrorAgregar");
        const alertErrorEditar = document.getElementById("alertErrorEditar");
        const modalDesactivar =
    document.getElementById("modalConfirmacionDesactivar");

const alertErrorDesactivar =
    document.getElementById("alertErrorDesactivar");

const btnEjecutarDesactivar =
    document.getElementById("btnEjecutarDesactivar");

const btnCancelarDesactivar =
    document.getElementById("btnCancelarDesactivar");

let desactivacionEnCurso = false;


        let codigoValido = false;
        let esDesactivado = false;
        let selectedRow = null;
        let initialEditState = {};

        // Seleccionar Fila de la Tabla
        function seleccionarFila(tr) {
            if (selectedRow) {
                selectedRow.classList.remove('selected-row');
            }
            selectedRow = tr;
            if (tr) {
                tr.classList.add('selected-row');
            }
        }

        // Cargar Categorías Dinámicamente (c_ValidarProducto / e_Categoria)
        function cargarCategorias() {
            return fetch('../../backend/acciones/obtener_categorias.php')
                .then(res => res.json())
                .then(cats => {
                    let options = '<option value="">Seleccione categoría...</option>';
                    let filterOptions = '<option value="">Todas las categorías</option>';
                    if (Array.isArray(cats) && cats.length > 0) {
                        cats.forEach(c => {
                            options += `<option value="${c}">${c}</option>`;
                            filterOptions += `<option value="${c}">${c}</option>`;
                        });
                    } else {
                        const defaultCats = ['Abarrotes', 'Bebidas', 'Cuidado Personal', 'Embutidos', 'Lácteos', 'Limpieza', 'Panadería', 'Snacks'];
                        defaultCats.forEach(c => {
                            options += `<option value="${c}">${c}</option>`;
                            filterOptions += `<option value="${c}">${c}</option>`;
                        });
                    }
                    document.getElementById('add_categoria').innerHTML = options;
                    document.getElementById('edit_categoria').innerHTML = options;
                    document.getElementById('categoriaBusqueda').innerHTML = filterOptions;
                })
                .catch(err => {
                    console.error('Error cargando categorías:', err);
                    const defaultCats = ['Abarrotes', 'Bebidas', 'Cuidado Personal', 'Embutidos', 'Lácteos', 'Limpieza', 'Panadería', 'Snacks'];
                    let options = '<option value="">Seleccione categoría...</option>';
                    let filterOptions = '<option value="">Todas las categorías</option>';
                    defaultCats.forEach(c => {
                        options += `<option value="${c}">${c}</option>`;
                        filterOptions += `<option value="${c}">${c}</option>`;
                    });
                    document.getElementById('add_categoria').innerHTML = options;
                    document.getElementById('edit_categoria').innerHTML = options;
                    document.getElementById('categoriaBusqueda').innerHTML = filterOptions;
                });
        }

        // Mostrar Toast Notification (RNF-12)
        function mostrarToast(msg) {
            const toast = document.getElementById("toastNotification");
            document.getElementById("toastMessage").innerText = msg;
            toast.classList.add("show");
            setTimeout(() => toast.classList.remove("show"), 3500);
        }

        // Generar código automático sugerido
        function solicitarCodigoSugerido() {
            fetch('../../backend/acciones/generar_codigo.php')
                .then(res => res.json())
                .then(data => {
                    if (data && data.codigo) {
                        inputCodigo.value = data.codigo;
                        evaluarCodigoEnTiempoReal(data.codigo);
                    }
                });
        }

        // Abrir Modal Agregar (Paso 1-2, RNF-08)
        function abrirModalAgregar() {
            document.getElementById("formAgregarProducto").reset();
            document.getElementById("add_stock").value = "0";
            document.getElementById("add_stock_minimo").value = "0";
            indicatorCodigo.innerText = "";
            indicatorCodigo.className = "status-indicator";
            alertError.style.display = "none";
            codigoValido = false;
            esDesactivado = false;
            modalAgregar.style.display = "flex";

            solicitarCodigoSugerido();
            setTimeout(() => inputCodigo.focus(), 100);
        }

        function cerrarModalAgregar() {
            modalAgregar.style.display = "none";
        }

        function cerrarModalEditar() {
            if (modalEditar) modalEditar.style.display = "none";
            limpiarErroresEditar();
        }

        function cerrarModalConfirmacionEditar() {
            if (modalConfirmacionEditar) modalConfirmacionEditar.style.display = "none";
            // RNF-23: Conserva datos en el formulario de modificación si cancela la confirmación
        }

        document.getElementById("btnAbrirModal").onclick = abrirModalAgregar;
        document.getElementById("btnCerrarAgregar").onclick = cerrarModalAgregar;
        document.getElementById("btnCerrarBusqueda").onclick = () => modalBusq.style.display = "none";
        document.getElementById("btnGenerarCodigoAuto").onclick = solicitarCodigoSugerido;

        // Atajos de teclado (Alt+N, Alt+G, Alt+B, Escape) - RNF-02 / UX
        document.addEventListener("keydown", function(e) {
            // Tecla Escape: cierra el modal de modificación o confirmación automáticamente
            if (e.key === "Escape") {
                if (modalConfirmacionEditar && modalConfirmacionEditar.style.display === "flex") {
                    cerrarModalConfirmacionEditar();
                } else if (modalEditar && modalEditar.style.display === "flex") {
                    cerrarModalEditar();
                } else if (modalAgregar && modalAgregar.style.display === "flex") {
                    cerrarModalAgregar();
                } else if (modalBusq && modalBusq.style.display === "flex") {
                    modalBusq.style.display = "none";
                }
            }

            if (e.altKey && (e.key === "n" || e.key === "N")) {
                e.preventDefault();
                abrirModalAgregar();
            }
            if (e.altKey && (e.key === "g" || e.key === "G")) {
                e.preventDefault();
                if (modalAgregar && modalAgregar.style.display === "flex") {
                    document.getElementById("btnPreGuardar").click();
                } else if (modalEditar && modalEditar.style.display === "flex") {
                    const btnSaveEdit = document.getElementById("btnPreGuardarEditar");
                    if (btnSaveEdit) btnSaveEdit.click();
                }
            }
            if (e.altKey && (e.key === "b" || e.key === "B")) {
                e.preventDefault();
                const inputBuscar = document.getElementById("inputBuscar");
                if (inputBuscar) {
                    inputBuscar.focus();
                    inputBuscar.select();
                }
            }
        });

        // Verificación en tiempo real de Código
        let timerVerificacion;
        function evaluarCodigoEnTiempoReal(val) {
            clearTimeout(timerVerificacion);
            alertError.style.display = "none";

            if (!val || val.trim() === "") {
                indicatorCodigo.innerText = "✕ El código del producto es obligatorio.";
                indicatorCodigo.className = "status-indicator status-invalid";
                codigoValido = false;
                return;
            }

            timerVerificacion = setTimeout(() => {
                fetch(`../../backend/acciones/verificar_codigo.php?codigo=${encodeURIComponent(val.trim())}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.estado === 'disponible') {
                            indicatorCodigo.innerText = '✓ ' + data.mensaje;
                            indicatorCodigo.className = 'status-indicator status-available';
                            codigoValido = true;
                            esDesactivado = false;
                        } else if (data.estado === 'formato_invalido') {
                            indicatorCodigo.innerText = '✕ ' + data.mensaje;
                            indicatorCodigo.className = 'status-indicator status-invalid';
                            codigoValido = false;
                            esDesactivado = false;
                        } else if (data.estado === 'duplicado_activo') {
                            indicatorCodigo.innerText = '✕ ' + data.mensaje;
                            indicatorCodigo.className = 'status-indicator status-duplicate';
                            codigoValido = false;
                            esDesactivado = false;
                        } else if (data.estado === 'desactivado') {
                            indicatorCodigo.innerText = '⚠ ' + data.mensaje;
                            indicatorCodigo.className = 'status-indicator status-deactivated';
                            codigoValido = false;
                            esDesactivado = true;
                            document.getElementById('reactivar_codigo').value = val.trim();
                            document.getElementById('msgReactivar').innerText = data.mensaje;
                            document.getElementById('modalReactivar').style.display = 'flex';
                        }
                    });
            }, 250);
        }

        inputCodigo.addEventListener("input", function() {
            evaluarCodigoEnTiempoReal(this.value);
        });

        // Reactivación de producto desactivado
        document.getElementById('btnEjecutarReactivar').onclick = function() {
            const cod = document.getElementById('reactivar_codigo').value;
            const formData = new FormData();
            formData.append('codigo', cod);

            fetch('../../backend/acciones/reactivar_producto.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalReactivar').style.display = 'none';
                    cerrarModalAgregar();
                    if (data.exito) {
                        mostrarToast(data.mensaje);
                        actualizarTabla();
                    } else {
                        alert(data.error);
                    }
                });
        };

        function limpiarErroresAgregar() {
            alertError.style.display = "none";
            ['add_codigo', 'add_nombre', 'add_categoria', 'add_unidad_medida', 'add_precio', 'add_stock', 'add_stock_minimo'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('input-error');
            });
            ['err_add_nombre', 'err_add_categoria', 'err_add_unidad_medida', 'err_add_precio', 'err_add_stock', 'err_add_stock_minimo'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.innerText = ''; el.style.display = 'none'; }
            });
        }

        function mostrarErrorCampoAgregar(inputId, errId, mensaje) {
            limpiarErroresAgregar();
            const el = document.getElementById(inputId);
            if (el) {
                el.classList.add('input-error');
                el.focus();
            }
            if (errId === 'indicatorCodigo') {
                indicatorCodigo.innerText = '✕ ' + mensaje;
                indicatorCodigo.className = 'status-indicator status-invalid';
            } else {
                const errEl = document.getElementById(errId);
                if (errEl) {
                    errEl.innerText = mensaje;
                    errEl.style.display = 'block';
                }
            }
        }

        // Pre-guardar Agregar Producto
        document.getElementById("btnPreGuardar").onclick = function() {
            limpiarErroresAgregar();

            const inputCodEl = document.getElementById("add_codigo");
            const inputNomEl = document.getElementById("add_nombre");
            const inputCatEl = document.getElementById("add_categoria");
            const inputUniEl = document.getElementById("add_unidad_medida");
            const inputPrecEl = document.getElementById("add_precio");
            const inputStockEl = document.getElementById("add_stock");
            const inputStockMinEl = document.getElementById("add_stock_minimo");

            const cod = inputCodEl ? inputCodEl.value.trim() : '';
            const nom = inputNomEl ? inputNomEl.value.trim() : '';
            const cat = inputCatEl ? inputCatEl.value : '';
            const unidad = inputUniEl ? inputUniEl.value : '';
            const precVal = inputPrecEl ? inputPrecEl.value : '';
            const stockVal = inputStockEl ? inputStockEl.value : '';
            const stockMinVal = inputStockMinEl ? inputStockMinEl.value : '';

            // Validación de Código
            if (!cod) {
                mostrarErrorCampoAgregar('add_codigo', 'indicatorCodigo', 'Este campo es obligatorio.');
                return;
            }
            if (!codigoValido) {
                const msgError = indicatorCodigo.innerText.replace('✕ ', '').replace('⚠ ', '') || "Código inválido o no disponible.";
                mostrarErrorCampoAgregar('add_codigo', 'indicatorCodigo', msgError);
                return;
            }

            // Flujo 6.1: Nombre obligatorio
            if (!nom) {
                mostrarErrorCampoAgregar('add_nombre', 'err_add_nombre', 'Este campo es obligatorio.');
                return;
            }

            // Flujo 6.4: Nombre muy corto
            if (nom.length < 3) {
                mostrarErrorCampoAgregar('add_nombre', 'err_add_nombre', 'El nombre del producto debe tener al menos 3 caracteres.');
                return;
            }

            // Flujo 6.1: Categoría obligatoria
            if (!cat) {
                mostrarErrorCampoAgregar('add_categoria', 'err_add_categoria', 'Este campo es obligatorio.');
                return;
            }

            // Flujo 6.1: Presentación obligatoria
            if (!unidad) {
                mostrarErrorCampoAgregar('add_unidad_medida', 'err_add_unidad_medida', 'Este campo es obligatorio.');
                return;
            }

            // Flujo 6.2: Precio inválido
            if (precVal === "" || isNaN(parseFloat(precVal)) || parseFloat(precVal) <= 0) {
                mostrarErrorCampoAgregar('add_precio', 'err_add_precio', 'El precio debe ser mayor a cero.');
                return;
            }

            // Flujo 6.3: Stock inicial inválido
            if (stockVal === "" || isNaN(parseInt(stockVal)) || parseInt(stockVal) < 0) {
                mostrarErrorCampoAgregar('add_stock', 'err_add_stock', 'El stock no puede ser negativo.');
                return;
            }

            // Flujo 6.3: Stock mínimo alerta inválido
            if (stockMinVal === "" || isNaN(parseInt(stockMinVal)) || parseInt(stockMinVal) < 0) {
                mostrarErrorCampoAgregar('add_stock_minimo', 'err_add_stock_minimo', 'El stock no puede ser negativo.');
                return;
            }

            document.getElementById("modalConfirmacionGuardar").style.display = "flex";
        };

        // Quitar resaltados de error dinámicamente al escribir en modal Agregar
        ['inputCodigo', 'add_nombre', 'add_categoria', 'add_unidad_medida', 'add_precio', 'add_stock', 'add_stock_minimo'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    this.classList.remove('input-error');
                    alertError.style.display = 'none';
                    const errEl = document.getElementById('err_' + id);
                    if (errEl) { errEl.innerText = ''; errEl.style.display = 'none'; }
                });
                el.addEventListener('change', function() {
                    this.classList.remove('input-error');
                    alertError.style.display = 'none';
                    const errEl = document.getElementById('err_' + id);
                    if (errEl) { errEl.innerText = ''; errEl.style.display = 'none'; }
                });
            }
        });

        // Ejecutar Guardar Producto
        document.getElementById("btnEjecutarGuardar").onclick = function() {
            document.getElementById("modalConfirmacionGuardar").style.display = "none";
            const form = document.getElementById("formAgregarProducto");
            const formData = new FormData(form);

            fetch('../../backend/acciones/guardar_producto.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.exito) {
                        cerrarModalAgregar();
                        mostrarToast(data.mensaje);
                        actualizarTabla();
                    } else {
                        alertError.innerText = data.error;
                        alertError.style.display = "block";
                    }
                })
                .catch(() => {
                    alertError.innerText = "No se pudo completar el registro. Intente nuevamente.";
                    alertError.style.display = "block";
                });
        };

        document.getElementById('formAgregarProducto').onsubmit = function(e) {
            e.preventDefault();
            document.getElementById('btnPreGuardar').click();
        };

        // ========================================================
        // MODIFICAR PRODUCTO (CU 2 - RUP)
        // ========================================================
        function limpiarErroresEditar() {
            if (alertErrorEditar) alertErrorEditar.style.display = 'none';
            const campos = ['edit_nombre', 'edit_categoria', 'edit_unidad_medida', 'edit_precio', 'edit_stock', 'edit_stock_minimo'];
            campos.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('input-error');
            });
        }

        function abrirModalEditar(btn) {
            const tr = btn.closest('tr');
            if (tr) {
                seleccionarFila(tr);
            }

            const id = btn.getAttribute('data-id');
            const codigo = btn.getAttribute('data-codigo');
            const nombre = btn.getAttribute('data-nombre');
            const categoria = btn.getAttribute('data-categoria') || '';
            const precio = btn.getAttribute('data-precio');
            const stock = btn.getAttribute('data-stock');
            const stockMinimo = btn.getAttribute('data-stock-minimo');
            const unidadMedida = btn.getAttribute('data-unidad-medida') || 'unidad';
            const descripcion = btn.getAttribute('data-descripcion') || '';

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_codigo').value = codigo;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_precio').value = precio;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_stock_minimo').value = stockMinimo;
            document.getElementById('edit_unidad_medida').value = unidadMedida;
            if (document.getElementById('edit_descripcion')) {
                document.getElementById('edit_descripcion').value = descripcion;
            }

            const editCatSelect = document.getElementById('edit_categoria');
            if (editCatSelect) {
                editCatSelect.value = categoria;
                if (categoria && !editCatSelect.querySelector(`option[value="${CSS.escape(categoria)}"]`)) {
                    const opt = document.createElement('option');
                    opt.value = categoria;
                    opt.textContent = categoria;
                    opt.selected = true;
                    editCatSelect.appendChild(opt);
                }
            }

            // Guardar estado inicial para detectar el Flujo 4.3 ("Sin cambios")
            initialEditState = {
                nombre: (nombre || '').trim(),
                categoria: (categoria || '').trim(),
                unidad_medida: (unidadMedida || '').trim(),
                precio: parseFloat(precio || 0),
                stock: parseInt(stock || 0),
                stock_minimo: parseInt(stockMinimo || 0),
                descripcion: (descripcion || '').trim()
            };

            limpiarErroresEditar();
            modalEditar.style.display = 'flex';
            setTimeout(() => document.getElementById('edit_nombre').focus(), 100);
        }

        // Pre-Guardar / Validación Modificación (Pasos 3-4, Flujos 4.1, 4.2, 4.3)
        const btnPreGuardarEditEl = document.getElementById('btnPreGuardarEditar');
        if (btnPreGuardarEditEl) {
            btnPreGuardarEditEl.onclick = function() {
                limpiarErroresEditar();

                const nomInput = document.getElementById('edit_nombre');
                const catInput = document.getElementById('edit_categoria');
                const uniInput = document.getElementById('edit_unidad_medida');
                const precInput = document.getElementById('edit_precio');
                const stockInput = document.getElementById('edit_stock');
                const stockMinInput = document.getElementById('edit_stock_minimo');
                const descInput = document.getElementById('edit_descripcion');

                const nom = nomInput ? nomInput.value.trim() : '';
                const cat = catInput ? catInput.value.trim() : '';
                const uni = uniInput ? uniInput.value.trim() : '';
                const precVal = precInput ? precInput.value : '';
                const stockVal = stockInput ? stockInput.value : '';
                const stockMinVal = stockMinInput ? stockMinInput.value : '';
                const desc = descInput ? descInput.value.trim() : '';

                // Flujo 4.1: Si se elimina el contenido de un campo obligatorio (RNF-04)
                if (!nom || nom.length < 3) {
                    if (nomInput) nomInput.classList.add('input-error');
                    alertErrorEditar.innerText = "El nombre del producto es obligatorio y debe tener al menos 3 caracteres.";
                    alertErrorEditar.style.display = "block";
                    if (nomInput) nomInput.focus();
                    return;
                }

                if (!cat) {
                    if (catInput) catInput.classList.add('input-error');
                    alertErrorEditar.innerText = "Debe seleccionar una categoría obligatoria para el producto.";
                    alertErrorEditar.style.display = "block";
                    if (catInput) catInput.focus();
                    return;
                }

                if (!uni) {
                    if (uniInput) uniInput.classList.add('input-error');
                    alertErrorEditar.innerText = "Debe seleccionar una unidad de medida para el producto.";
                    alertErrorEditar.style.display = "block";
                    if (uniInput) uniInput.focus();
                    return;
                }

                // Flujo 4.2: Si algún campo numérico contiene valor negativo o no numérico
                if (precVal === "" || isNaN(parseFloat(precVal)) || parseFloat(precVal) <= 0) {
                    if (precInput) precInput.classList.add('input-error');
                    alertErrorEditar.innerText = "El valor ingresado no es válido para este campo.";
                    alertErrorEditar.style.display = "block";
                    if (precInput) precInput.focus();
                    return;
                }

                if (stockVal === "" || isNaN(parseInt(stockVal)) || parseInt(stockVal) < 0) {
                    if (stockInput) stockInput.classList.add('input-error');
                    alertErrorEditar.innerText = "El valor ingresado no es válido para este campo.";
                    alertErrorEditar.style.display = "block";
                    if (stockInput) stockInput.focus();
                    return;
                }

                if (stockMinVal === "" || isNaN(parseInt(stockMinVal)) || parseInt(stockMinVal) < 0) {
                    if (stockMinInput) stockMinInput.classList.add('input-error');
                    alertErrorEditar.innerText = "El valor ingresado no es válido para este campo.";
                    alertErrorEditar.style.display = "block";
                    if (stockMinInput) stockMinInput.focus();
                    return;
                }

                // Flujo 4.3: Si se intenta guardar sin haber modificado ningún campo
                const pVal = parseFloat(precVal);
                const stVal = parseInt(stockVal);
                const stMinVal = parseInt(stockMinVal);

                if (
                    nom === initialEditState.nombre &&
                    cat === initialEditState.categoria &&
                    uni === initialEditState.unidad_medida &&
                    Math.abs(pVal - initialEditState.precio) < 0.001 &&
                    stVal === initialEditState.stock &&
                    stMinVal === initialEditState.stock_minimo &&
                    desc === initialEditState.descripcion
                ) {
                    alertErrorEditar.innerText = "No se detectaron cambios en el formulario.";
                    alertErrorEditar.style.display = "block";
                    return;
                }

                // Pasos 5-7: Solicitar confirmación antes de guardar (RNF-03)
                if (modalConfirmacionEditar) modalConfirmacionEditar.style.display = 'flex';
            };
        }

        // Ejecutar Modificación (Paso 8, RNF-12, RNF-18, Flujo 7.1)
        const btnEjecutarActualizarEl = document.getElementById('btnEjecutarActualizar');
        if (btnEjecutarActualizarEl) {
            btnEjecutarActualizarEl.onclick = function() {
                if (modalConfirmacionEditar) modalConfirmacionEditar.style.display = 'none';

                const form = document.getElementById('formEditarProducto');
                const formData = new FormData(form);

                fetch('../../backend/acciones/actualizar_producto.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.exito) {
                            if (modalEditar) modalEditar.style.display = 'none';
                            mostrarToast(data.mensaje || 'El producto ha sido actualizado correctamente.');
                            actualizarTabla();
                        } else {
                            // RNF-23: Conserva datos modificados en el formulario si ocurre un error
                            alertErrorEditar.innerText = data.error || 'Error al actualizar el producto.';
                            alertErrorEditar.style.display = 'block';
                            if (modalEditar) modalEditar.style.display = 'flex';
                        }
                    })
                    .catch(() => {
                        alertErrorEditar.innerText = 'No se pudo completar la actualización. Intente nuevamente.';
                        alertErrorEditar.style.display = 'block';
                        if (modalEditar) modalEditar.style.display = 'flex';
                    });
            };
        }

        // Quitar resaltado de error dinámicamente al modificar inputs
        ['edit_nombre', 'edit_categoria', 'edit_unidad_medida', 'edit_precio', 'edit_stock', 'edit_stock_minimo'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    this.classList.remove('input-error');
                    if (alertErrorEditar) alertErrorEditar.style.display = 'none';
                });
                el.addEventListener('change', function() {
                    this.classList.remove('input-error');
                    if (alertErrorEditar) alertErrorEditar.style.display = 'none';
                });
            }
        });

        document.getElementById('formEditarProducto').onsubmit = function(e) {
            e.preventDefault();
            const btnSaveEdit = document.getElementById('btnPreGuardarEditar');
            if (btnSaveEdit) btnSaveEdit.click();
        };

        // DESACTIVAR PRODUCTO
function abrirModalDesactivar(btn) {
    const fila = btn.closest("tr");

    if (fila) {
        seleccionarFila(fila);
    }

    const id = parseInt(btn.dataset.id, 10);

    if (isNaN(id) || id <= 0) {
        alert("No se pudo identificar el producto seleccionado.");
        return;
    }

    document.getElementById("desactivar_id").value = id;

    document.getElementById("desactivar_codigo").textContent =
        btn.dataset.codigo || "";

    document.getElementById("desactivar_nombre").textContent =
        btn.dataset.nombre || "";

    document.getElementById("desactivar_categoria").textContent =
        btn.dataset.categoria || "";

    document.getElementById("desactivar_estado").textContent =
        btn.dataset.estado && btn.dataset.estado.trim() !== ""
            ? btn.dataset.estado
            : "Sin estado";

    document.getElementById("desactivar_stock").textContent =
        btn.dataset.stock || "0";

    alertErrorDesactivar.textContent = "";
    alertErrorDesactivar.style.display = "none";

    modalDesactivar.style.display = "flex";
}

function cerrarModalDesactivar() {
    if (desactivacionEnCurso) {
        return;
    }

    alertErrorDesactivar.textContent = "";
    alertErrorDesactivar.style.display = "none";
    modalDesactivar.style.display = "none";
}

btnCancelarDesactivar.onclick = cerrarModalDesactivar;

btnEjecutarDesactivar.onclick = async function () {
    if (desactivacionEnCurso) {
        return;
    }

    const inputId = document.getElementById("desactivar_id");
    const id = parseInt(inputId.value, 10);

    if (isNaN(id) || id <= 0) {
        alertErrorDesactivar.textContent =
            "Debe seleccionar un producto válido.";

        alertErrorDesactivar.style.display = "block";
        return;
    }

    alertErrorDesactivar.textContent = "";
    alertErrorDesactivar.style.display = "none";

    desactivacionEnCurso = true;

    btnEjecutarDesactivar.disabled = true;
    btnCancelarDesactivar.disabled = true;
    btnEjecutarDesactivar.textContent = "Desactivando...";

    const parametros = new URLSearchParams();
    parametros.append("id", String(id));

    try {
        const response = await fetch(
            "../../backend/acciones/eliminar_producto.php",
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded; charset=UTF-8",
                    "Accept": "application/json"
                },
                body: parametros.toString()
            }
        );

        let data;

        try {
            data = await response.json();
        } catch (errorJson) {
            throw new Error("Respuesta JSON inválida.");
        }

        if (!response.ok || data.exito !== true) {
            alertErrorDesactivar.textContent =
                data.error ||
                "No se pudo completar la desactivación. Intente nuevamente.";

            alertErrorDesactivar.style.display = "block";
            return;
        }

        desactivacionEnCurso = false;
        cerrarModalDesactivar();

        mostrarToast(
            data.mensaje ||
            "El producto ha sido desactivado correctamente."
        );

        actualizarTabla();

    } catch (error) {
        console.error("[DesactivarProducto]", error);

        alertErrorDesactivar.textContent =
            "No se pudo completar la desactivación. Intente nuevamente.";

        alertErrorDesactivar.style.display = "block";

    } finally {
        desactivacionEnCurso = false;

        btnEjecutarDesactivar.disabled = false;
        btnCancelarDesactivar.disabled = false;
        btnEjecutarDesactivar.textContent = "Desactivar";
    }
};
        // BÚSQUEDA Y FILTRADO CON PAGINACIÓN GLOBAL (Paso 4 / RNF-11)
        const inputBuscar = document.getElementById('inputBuscar');
        const selectLimite = document.getElementById('selectLimite');
        let filtroActual = 'Todos';
        let paginaActual = 1;
        let limiteActual = 25;
        let timerBusqueda = null;

        function actualizarTabla(pageOverride) {
            if (pageOverride) {
                paginaActual = parseInt(pageOverride, 10) || 1;
            }
            if (selectLimite) {
                limiteActual = parseInt(selectLimite.value, 10) || 25;
            }

            let q = inputBuscar ? inputBuscar.value.trim() : '';
            let min = precioMinInput ? precioMinInput.value.trim() : '';
            let max = precioMaxInput ? precioMaxInput.value.trim() : '';
            let cat = document.getElementById('categoriaBusqueda') ? document.getElementById('categoriaBusqueda').value : '';

            const params = new URLSearchParams({
                q: q,
                f: filtroActual,
                min: min,
                max: max,
                cat: cat,
                pagina: paginaActual,
                limite: limiteActual
            });

            fetch(`../../backend/acciones/filtrar_productos.php?${params.toString()}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('tablaProductos');
                    if (data.exito) {
                        tbody.innerHTML = data.html;
                        paginaActual = data.pagina_actual;
                        limiteActual = data.limite;

                        const textoContador = data.total > 0
                            ? `Mostrando ${data.desde}–${data.hasta} de ${data.total} productos`
                            : `Mostrando 0 productos`;

                        const elTop = document.getElementById('counterRangeTextTop');
                        const elBottom = document.getElementById('counterRangeTextBottom');
                        if (elTop) elTop.innerText = textoContador;
                        if (elBottom) elBottom.innerText = textoContador;

                        renderPaginationControls(data.pagina_actual, data.total_paginas);
                    } else {
                        tbody.innerHTML = data.html || `<tr><td colspan='8' style='text-align: center; padding: 40px; color: #DC2626; font-weight: 600;'>${data.error}</td></tr>`;
                        renderPaginationControls(1, 1);
                    }
                })
                .catch(err => {
                    console.error('Error al actualizar tabla:', err);
                    const tbody = document.getElementById('tablaProductos');
                    if (tbody) {
                        // Flujo Alterno 2.2: Error de carga (RNF-23)
                        tbody.innerHTML = "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #DC2626; font-weight: 600;'><i class='fa-solid fa-circle-exclamation' style='font-size: 24px; margin-bottom: 10px; display: block;'></i>No se pudo cargar el listado. Intente refrescando la página.</td></tr>";
                    }
                });
        }

        function renderPaginationControls(current, totalPages) {
            const container = document.getElementById("paginationButtons");
            if (!container) return;

            if (totalPages <= 1) {
                container.innerHTML = "";
                return;
            }

            let html = "";
            const prevDisabled = current <= 1 ? "disabled" : "";
            html += `<button class="page-btn" ${prevDisabled} onclick="actualizarTabla(${current - 1})"><i class="fa-solid fa-chevron-left"></i> Anterior</button>`;

            const delta = 2;
            const range = [];

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= current - delta && i <= current + delta)) {
                    range.push(i);
                }
            }

            let prevNumber = 0;
            for (const i of range) {
                if (prevNumber) {
                    if (i - prevNumber === 2) {
                        range.push(prevNumber + 1);
                    } else if (i - prevNumber > 2) {
                        html += `<span style="padding: 6px 8px; color: #9CA3AF;">...</span>`;
                    }
                }
                const activeClass = i === current ? "active" : "";
                html += `<button class="page-btn ${activeClass}" onclick="actualizarTabla(${i})">${i}</button>`;
                prevNumber = i;
            }

            const nextDisabled = current >= totalPages ? "disabled" : "";
            html += `<button class="page-btn" ${nextDisabled} onclick="actualizarTabla(${current + 1})">Siguiente <i class="fa-solid fa-chevron-right"></i></button>`;

            container.innerHTML = html;
        }

        // ========================================================
        // CU-4: FILTROS AVANZADOS (Categoría, Precio, Estado de stock)
        // ========================================================
        const precioMinInput = document.getElementById('precioMin');
        const precioMaxInput = document.getElementById('precioMax');
        const errorPrecioMin = document.getElementById('errorPrecioMin');
        const errorPrecioMax = document.getElementById('errorPrecioMax');
        const alertErrorFiltros = document.getElementById('alertErrorFiltros');

        function sanearValorPrecio(valor) {
            let v = valor.trim().replace(/,/g, '.');
            let limpio = '';
            let tienePunto = false;
            let invalido = false;

            for (const ch of v) {
                if (ch >= '0' && ch <= '9') {
                    limpio += ch;
                } else if (ch === '.' && !tienePunto) {
                    tienePunto = true;
                    limpio += ch;
                } else {
                    invalido = true;
                }
            }

            const punto = limpio.indexOf('.');
            if (punto !== -1 && limpio.length - punto - 1 > 2) {
                limpio = limpio.slice(0, punto + 3);
                invalido = true;
            }

            return { limpio, invalido };
        }

        function configurarCampoPrecio(input, errorSpan) {
            input.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey || e.altKey) return;
                if (e.key.length !== 1) return;

                if (e.key === ' ') {
                    e.preventDefault();
                    return;
                }

                const valorRestante = this.value.slice(0, this.selectionStart) + this.value.slice(this.selectionEnd);
                const esDigito = e.key >= '0' && e.key <= '9';
                const esSeparador = (e.key === '.' || e.key === ',') && !valorRestante.includes('.');

                if (esDigito) {
                    const punto = valorRestante.indexOf('.');
                    if (punto !== -1 && this.selectionStart > punto && valorRestante.length - punto - 1 >= 2) {
                        e.preventDefault();
                        errorSpan.style.visibility = 'visible';
                        return;
                    }
                    errorSpan.style.visibility = 'hidden';
                    return;
                }

                if (esSeparador) {
                    errorSpan.style.visibility = 'hidden';
                    return;
                }

                e.preventDefault();
                errorSpan.style.visibility = 'visible';
            });

            input.addEventListener('input', function() {
                const { limpio, invalido } = sanearValorPrecio(this.value);
                if (this.value !== limpio) {
                    this.value = limpio;
                }
                errorSpan.style.visibility = invalido ? 'visible' : 'hidden';
                if (!invalido) alertErrorFiltros.style.visibility = 'hidden';
            });
        }

        if (precioMinInput && errorPrecioMin) configurarCampoPrecio(precioMinInput, errorPrecioMin);
        if (precioMaxInput && errorPrecioMax) configurarCampoPrecio(precioMaxInput, errorPrecioMax);

        function abrirModalFiltros() {
            alertErrorFiltros.style.visibility = 'hidden';
            modalBusq.style.display = 'flex';
            setTimeout(() => document.getElementById('categoriaBusqueda').focus(), 100);
        }

        modalBusq.addEventListener('keydown', function(e) {
            if (e.key !== 'Tab' || modalBusq.style.display !== 'flex') return;
            const enfocables = modalBusq.querySelectorAll('select, input, button');
            if (enfocables.length === 0) return;
            const primero = enfocables[0];
            const ultimo = enfocables[enfocables.length - 1];
            if (e.shiftKey && document.activeElement === primero) {
                e.preventDefault();
                ultimo.focus();
            } else if (!e.shiftKey && document.activeElement === ultimo) {
                e.preventDefault();
                primero.focus();
            }
        });

        function aplicarFiltrosAvanzados() {
            alertErrorFiltros.style.visibility = 'hidden';

            let minStr = precioMinInput.value.trim().replace(/\.$/, '');
            let maxStr = precioMaxInput.value.trim().replace(/\.$/, '');
            precioMinInput.value = minStr;
            precioMaxInput.value = maxStr;

            const regexPrecio = /^\d+(\.\d{1,2})?$/;
            if (minStr !== '' && !regexPrecio.test(minStr)) {
                errorPrecioMin.style.visibility = 'visible';
                precioMinInput.focus();
                return;
            }
            if (maxStr !== '' && !regexPrecio.test(maxStr)) {
                errorPrecioMax.style.visibility = 'visible';
                precioMaxInput.focus();
                return;
            }

            if (minStr !== '' && maxStr !== '' && parseFloat(minStr) > parseFloat(maxStr)) {
                alertErrorFiltros.innerText = 'El mínimo no puede superar al máximo';
                alertErrorFiltros.style.visibility = 'visible';
                return;
            }

            filtroActual = document.getElementById('estadoStockBusqueda').value;
            modalBusq.style.display = 'none';
            actualizarTabla(1);
        }

        function limpiarFiltrosCatalogo() {
            precioMinInput.value = '';
            precioMaxInput.value = '';
            document.getElementById('categoriaBusqueda').value = '';
            document.getElementById('estadoStockBusqueda').value = 'Todos';
            errorPrecioMin.style.visibility = 'hidden';
            errorPrecioMax.style.visibility = 'hidden';
            alertErrorFiltros.style.visibility = 'hidden';

            filtroActual = 'Todos';
            modalBusq.style.display = 'none';
            actualizarTabla(1);
        }

        // Chips de filtro por estado
        document.querySelectorAll('.filter-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                filtroActual = this.getAttribute('data-filter') || 'Todos';
                actualizarTabla(1);
            });
        });

        // Selector de límite por página (10, 25, 50, 100)
        if (selectLimite) {
            selectLimite.addEventListener('change', function() {
                limiteActual = parseInt(this.value, 10) || 25;
                actualizarTabla(1);
            });
        }

        // Búsqueda en tiempo real con debounce
        if (inputBuscar) {
            inputBuscar.addEventListener('input', function() {
                clearTimeout(timerBusqueda);
                timerBusqueda = setTimeout(() => {
                    actualizarTabla(1);
                }, 250);
            });
        }

        window.onload = function() {
            cargarCategorias();
            actualizarTabla(1);
        };
    </script>
</body>
</html>