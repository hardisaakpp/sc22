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
$s1 = $db->query("SELECT AcctCode, AcctName, FormatCode FROM CuentaFinanciera where DepositTiendas = 1");
$cuentas = $s1->fetchAll(PDO::FETCH_OBJ);

// Obtener fecha desde POST o usar actual
$fecha = $_GET["U_Fecha"] ?? date('Y-m-d');
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
                        <input type="hidden" name="U_WhsCode" value="<?= $almacen->cod_almacen ?>">
                    </div>
<!-- Campo Marca -->
<div class="form-group" hidden>
    <label>Marca</label>
    <input type="text" name="Marca" class="form-control" maxlength="20" list="sugerenciasMarca" hidden>
    <datalist id="sugerenciasMarca">
        <option value="ROL">
        <option value="LIL">
        <option value="OUT">
        <option value="REGIONS">
    </datalist>
</div>

                    <!-- TotalLC -->
                    <div class="form-group">
                        <label>Valor Depositado</label>
                        <input type="number" step="0.01" name="TotalLC" class="form-control restrict-copy-paste" required>
                    </div>

                    <!-- Referencia Bancaria -->
                    <div class="form-group">
                        <label>Numero de comprobante</label>
                        <input type="text" name="U_Ref_Bancar" class="form-control restrict-copy-paste" autocomplete="off" maxlength="10" required>
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
                    <input type="hidden" name="accion" value="guardar_local">
                    <!-- Botón para abrir modal -->
                    <button type="button" class="btn btn-secondary" onclick="abrirConfirmacion()">Guardar Localmente</button>
<!-- Modal de confirmación -->
<div id="modalConfirmacion" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:#fff; margin:10% auto; padding:20px; width:400px; border-radius:10px;">
        <h5>Confirmar número de comprobante</h5>
        <p>Por favor, vuelve a ingresar el número de comprobante:</p>
        <input type="text" id="confirmacionComprobante" class="form-control" maxlength="10" autocomplete="off">
        <div style="margin-top:10px; display:flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="validarConfirmacion()">Confirmar y Guardar</button>
        </div>
        <p id="errorMensaje" style="color:red; display:none; margin-top:10px;">❌ Los números no coinciden. Por favor revise los datos ingresados.</p>
    </div>
</div>


                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('.restrict-copy-paste').forEach(input => {
        input.addEventListener('copy', e => e.preventDefault());
        input.addEventListener('cut', e => e.preventDefault());
        input.addEventListener('paste', e => e.preventDefault());
    });

function abrirConfirmacion() {
    document.getElementById('confirmacionComprobante').value = '';
    document.getElementById('errorMensaje').style.display = 'none';
    document.getElementById('modalConfirmacion').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalConfirmacion').style.display = 'none';
}

function validarConfirmacion() {
    const original = document.querySelector('input[name="U_Ref_Bancar"]').value.trim();
    const confirmacion = document.getElementById('confirmacionComprobante').value.trim();

    
    if (original === confirmacion && original.length > 0) {
        document.querySelector('form').submit();
    } else {
        document.getElementById('errorMensaje').style.display = 'block';
    }
}
</script>


<?php include_once "footer.php"; ?>
