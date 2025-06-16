<!DOCTYPE html>
<html>
<head>
    <title>Procesar Almacenes</title>
</head>
<body>
    <h1>Procesar Datos de Almacenes</h1>
    <form method="POST" action="php/procesar_datos.php">
        <label for="empresa">Empresa:</label>
        <input type="text" name="empresa" required><br><br>

        <label for="fecha">Fecha (YYYY-MM-DD):</label>
        <input type="date" name="fecha" required><br><br>

        <input type="submit" value="Procesar">
    </form>
</body>
</html>
