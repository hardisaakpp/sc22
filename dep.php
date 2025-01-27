<?php
 include_once "header.php";
include 'php/depcru.php';

// Manejo de operaciones CRUD
if (isset($_POST['crear'])) {
    crearDeposito($db, $_POST['fecha'], $_POST['cantidad'], $_POST['descripcion']);
}

if (isset($_GET['eliminar'])) {
    eliminarDeposito($db, $_GET['eliminar']);
}

if (isset($_POST['actualizar'])) {
    actualizarDeposito($db, $_POST['id'], $_POST['fecha'], $_POST['cantidad'], $_POST['descripcion']);
}

$depositos = obtenerDepositos($db, $_POST['fecha_inicio'] ?? null, $_POST['fecha_fin'] ?? null);
$rows = $depositos->fetchAll(PDO::FETCH_OBJ); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Depósitos Bancarios</title>
</head>
<body>
    <h1>Gestión de Depósitos Bancarios</h1>
    
    <form method="post">
        <input type="date" name="fecha" required>
        <input type="number" step="0.01" name="cantidad" placeholder="Cantidad" required>
        <input type="text" name="descripcion" placeholder="Descripción" required>
        <button type="submit" name="crear">Crear Depósito</button>
    </form>
    
    <h2>Filtrar por Fecha</h2>
    <form method="post">
        <input type="date" name="fecha_inicio" required>
        <input type="date" name="fecha_fin" required>
        <button type="submit" name="filtrar_fecha">Filtrar</button>
    </form>
    
    <h2>Lista de Depósitos</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Cantidad</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        <?php 
            
            foreach($rows as $row){
            
            ?>
        <tr>
            <td><?php echo $row->id; ?></td>
            <td><?php echo $row->fec_dep; ?></td>
            <td><?php echo $row->valor; ?></td>
            <td><?php echo $row->observacion; ?></td>
            <td>
                <a href="index.php?eliminar=<?php echo $row['id']; ?>">Eliminar</a>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="date" name="fecha" value="<?php echo $row['fecha']; ?>" required>
                    <input type="number" step="0.01" name="cantidad" value="<?php echo $row['cantidad']; ?>" required>
                    <input type="text" name="descripcion" value="<?php echo $row['descripcion']; ?>" required>
                    <button type="submit" name="actualizar">Actualizar</button>
                </form>
            </td>
        </tr>
        <?php }; ?>
    </table>
</body>
</html>
