<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="../css/productos.css">
</head>
<body>

    <div class="container">

        <aside class="sidebar">
            <h2>MASS</h2>

            <ul>
                <li><a href="dashboard.php" style="color: white; text-decoration: none; display: block;">Dashboard</a></li>
                <li class="active"><a href="productos.php" style="color: white; text-decoration: none; display: block;">Productos</a></li>
                <li><a href="movimientos.php" style="color: white; text-decoration: none; display: block;">Movimientos</a></li>
                <li><a href="precios.php" style="color: white; text-decoration: none; display: block;">Precios</a></li>
                <li><a href="alertas.php" style="color: white; text-decoration: none; display: block;">Alertas</a></li>
            </ul>
        </aside>

        <main class="content">

            <h1>Gestión de Productos</h1>

            <div class="top-bar">
                <input type="text" placeholder="Buscar producto...">
                <button>Buscar</button>
                <button class="add-btn">+ Agregar Producto</button>
            </div>

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
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>P001</td>
                        <td>Leche</td>
                        <td>Lácteos</td>
                        <td>S/ 4.50</td>
                        <td>5</td>
                        <td>10</td>
                        <td class="warning">Stock Bajo</td>
                    </tr>

                    <tr>
                        <td>P002</td>
                        <td>Arroz</td>
                        <td>Abarrotes</td>
                        <td>S/ 3.00</td>
                        <td>20</td>
                        <td>10</td>
                        <td class="normal">Normal</td>
                    </tr>

                    <tr>
                        <td>P003</td>
                        <td>Aceite</td>
                        <td>Abarrotes</td>
                        <td>S/ 8.50</td>
                        <td>0</td>
                        <td>10</td>
                        <td class="danger">Agotado</td>
                    </tr>
                </tbody>
            </table>

        </main>

    </div>

</body>
</html>