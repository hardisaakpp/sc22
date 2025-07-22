<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";

if ($userAdmin == 0) {
    echo "ACCESO DENEGADO";
    exit();
}

$whsCica = $_SESSION["whsCica"] ?? null;

// Obtener nombre del almacén
$alm = $db->prepare("SELECT cod_almacen FROM almacen WHERE id = ?");
$alm->execute([$whsCica]);
$almacen = $alm->fetch(PDO::FETCH_OBJ);

// Obtener cuentas para el select
$s1 = $db->query("SELECT AcctCode, AcctName, FormatCode FROM CuentaFinanciera");
$cuentas = $s1->fetchAll(PDO::FETCH_OBJ);

// Obtener fecha desde POST o usar actual
$fecha = $_POST["U_Fecha"] ?? date('Y-m-d');
?>

<div class="content">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Registrar Depósito</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="php/guardar_deposito.php">
                    <!-- Fecha del depósito ingresada por el usuario -->
                    <div class="form-group">
                        <label>Fecha del Depósito</label>
                        <input type="date" name="DepositDate" class="form-control" required>
                    </div>

                    <!-- Fecha U_Fecha (bloqueada) -->
                    <div class="form-group">
                        <label>Fecha de Cierre de caja</label>
                        <input type="date" name="U_Fecha" class="form-control" value="<?= $fecha ?>" readonly>
                    </div>

                    <!-- Cuenta de depósito -->
                    <div class="form-group">
                        <label>Cuenta de Depósito</label>
                        <select name="DepositAccount" class="form-control" required>
                            <option value="">Seleccione una cuenta</option>
                            <?php foreach ($cuentas as $c): ?>
                                <option value="<?= $c->AcctCode ?>">
                                    <?= $c->FormatCode ?> - <?= $c->AcctName ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Almacén (visible pero deshabilitado) -->
                    <div class="form-group">
                        <label>Almacén</label>
                        <input type="text" class="form-control" value="<?= $almacen->cod_almacen ?? 'No definido' ?>" disabled>
                        <input type="hidden" name="U_WhsCode" value="<?= $whsCica ?>">
                    </div>

                    <!-- TotalLC -->
                    <div class="form-group">
                        <label>Total (LC)</label>
                        <input type="number" step="0.01" name="TotalLC" class="form-control" required>
                    </div>

                    <!-- Referencia Bancaria -->
                    <div class="form-group">
                        <label>Referencia Bancaria</label>
                        <input type="text" name="U_Ref_Bancar" class="form-control" required>
                    </div>

                    <!-- Campos ocultos -->
                    <input type="hidden" name="DepositCurrency" value="USD">
                    <input type="hidden" name="DepositType" value="dtCash">
                    <input type="hidden" name="AllocationAccount" value="_SYS00000001151">

                    <!-- Responsable -->
                    <div class="form-group">
                        <label>Responsable</label>
                        <input type="text" name="Responsable" class="form-control" maxlength="50" required>
                    </div>

                    <button type="submit" name="accion" value="guardar_local" class="btn btn-secondary">Guardar Localmente</button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>
