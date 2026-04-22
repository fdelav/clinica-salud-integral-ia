<?php
/**
 * Modal de confirmación de eliminación de usuario.
 * Requiere GET: tipoId, idUser
 * POST a: ../Php/eliminarUser.php
 *
 * Uso desde cualquier página:
 *   <?php $mostrarModal = isset($_GET['eliminar']); ?>
 *   <?php include 'modal_confirmar_eliminacion.php'; ?>
 */
$mostrarModal = isset($_GET['tipoId']) && isset($_GET['idUser']);

if ($mostrarModal):
    require_once '../includes/sesiones.php';
    verificarRol(['admin']);
    require '../Php/coneccion.php';

    $tipoId = $_GET['tipoId'];
    $idUser = $_GET['idUser'];

    $stmt = $conn->prepare("SELECT cont, nameUser, secondNameUser, tipoId, idUser, generoUser FROM usuario WHERE tipoId = ? AND idUser = ?");
    $stmt->bind_param('ss', $tipoId, $idUser);
    $stmt->execute();
    $res = $stmt->get_result();
    $usuario = $res->fetch_assoc();
    $stmt->close();
    $conn->close();

    $generosLabel = ['m' => 'Masculino', 'f' => 'Femenino', 'o' => 'Otro'];
    $nombreCompleto = $usuario
        ? htmlspecialchars($usuario['nameUser'] . ' ' . $usuario['secondNameUser'])
        : '';
    $generoLbl = $usuario ? ($generosLabel[$usuario['generoUser']] ?? '—') : '—';
?>
<link rel="stylesheet" href="../Css/dashboard.css">
<style>
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.55);
    display: flex; align-items: center; justify-content: center;
    z-index: 1000;
    font-family: 'Nunito', sans-serif;
}
.modal-card {
    background: #fff;
    border-radius: 20px;
    padding: 32px 36px;
    max-width: 480px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    text-align: center;
}
.modal-icono {
    width: 72px; height: 72px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
}
.modal-icono.peligro {
    background: #fff1f2;
    color: #be123c;
    border: 2px solid #fecdd3;
}
.modal-titulo {
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 1.4rem;
    color: #1a1a1a;
    margin-bottom: 6px;
}
.modal-subtitulo {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 24px;
}
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    background: #f8f7f4;
    border-radius: 14px;
    padding: 18px 20px;
    margin-bottom: 24px;
    text-align: left;
}
.info-item { display: flex; flex-direction: column; gap: 2px; }
.info-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #b89000;
    font-family: 'Montserrat', sans-serif;
}
.info-value {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2d2d2d;
}
.info-value.nombre-completo {
    grid-column: 1 / -1;
    font-size: 1.1rem;
    color: #1a1a1a;
}
.advertencia {
    background: #fff1f2;
    border: 1.5px solid #fecdd3;
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 22px;
    font-size: 0.85rem;
    color: #be123c;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    text-align: left;
}
.advertencia i { font-size: 1rem; flex-shrink: 0; }
.confirmacion-extra {
    display: none;
    margin-bottom: 18px;
}
.confirmacion-extra.mostrar { display: block; }
.confirmacion-extra label {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: #444;
    margin-bottom: 6px;
}
.confirmacion-extra input {
    width: 100%;
    border: 1.5px solid #fecdd3;
    border-radius: 10px;
    padding: 9px 13px;
    font-size: 0.9rem;
    font-family: 'Nunito', sans-serif;
    outline: none;
    box-sizing: border-box;
}
.confirmacion-extra input:focus {
    border-color: #be123c;
    box-shadow: 0 0 0 3px rgba(190,18,60,0.1);
}
.modal-acciones {
    display: flex;
    gap: 12px;
}
.btn-modal {
    flex: 1;
    padding: 12px 16px;
    border-radius: 12px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    border: 2px solid transparent;
    transition: background 0.15s, transform 0.12s;
}
.btn-modal:hover { transform: scale(1.02); }
.btn-cancelar {
    background: #f3f4f6;
    color: #444;
    border-color: #d1d5db;
}
.btn-cancelar:hover { background: #e5e7eb; }
.btn-confirmar {
    background: #be123c;
    color: #fff;
    border-color: #be123c;
}
.btn-confirmar:hover { background: #9f1232; }
.btn-confirmar:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    transform: none;
}
</style>

<div class="modal-overlay" id="modalConfirmacion">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modalTitulo">
        <?php if (!$usuario): ?>
            <div class="modal-icono peligro"><i class="bi bi-x-circle-fill"></i></div>
            <h2 class="modal-titulo" id="modalTitulo">Usuario no encontrado</h2>
            <p class="modal-subtitulo">No se encontró un usuario con tipo <strong><?= htmlspecialchars($tipoId) ?></strong> e identificación <strong><?= htmlspecialchars($idUser) ?></strong>.</p>
            <div class="modal-acciones">
                <a href="dashboard_admin_eliminar_usuario.php" class="btn-modal btn-cancelar">Cerrar</a>
            </div>
        <?php else: ?>
            <div class="modal-icono peligro"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h2 class="modal-titulo" id="modalTitulo">¿Eliminar usuario?</h2>
            <p class="modal-subtitulo">Esta acción no se puede deshacer. El usuario será eliminado permanentemente.</p>

            <div class="info-grid">
                <div class="info-item nombre-completo">
                    <span class="info-label">Nombre completo</span>
                    <span class="info-value"><?= $nombreCompleto ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo de ID</span>
                    <span class="info-value"><?= strtoupper(htmlspecialchars($usuario['tipoId'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Número de ID</span>
                    <span class="info-value"><?= htmlspecialchars($usuario['idUser']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Género</span>
                    <span class="info-value"><?= $generoLbl ?></span>
                </div>
            </div>

            <div class="advertencia" id="advertencia">
                <i class="bi bi-info-circle-fill"></i>
                Se eliminarán todos los datos asociados a este usuario.
            </div>

            <div class="confirmacion-extra" id="confirmacionExtra">
                <label for="inputConfirmarNombre">Escribe <strong><?= htmlspecialchars($usuario['nameUser']) ?></strong> para confirmar:</label>
                <input type="text" id="inputConfirmarNombre" placeholder="Nombre del usuario">
            </div>

            <div class="modal-acciones">
                <a href="dashboard_admin_eliminar_usuario.php" class="btn-modal btn-cancelar" id="btnCancelar">Cancelar</a>
                <button type="button" class="btn-modal btn-confirmar" id="btnPrimeraConfirmacion"
                    onclick="mostrarConfirmacionExtra()">
                    Confirmar eliminación
                </button>
                <form action="../Php/eliminarUser.php" method="post" id="formEliminar" style="display:none;">
                    <input type="hidden" name="tipoId" value="<?= htmlspecialchars($tipoId) ?>">
                    <input type="hidden" name="idUser" value="<?= htmlspecialchars($idUser) ?>">
                    <button type="submit" class="btn-modal btn-confirmar" id="btnSegundaConfirmacion" disabled>
                        Sí, eliminar usuario
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function mostrarConfirmacionExtra() {
    document.getElementById('confirmacionExtra').classList.add('mostrar');
    document.getElementById('btnPrimeraConfirmacion').style.display = 'none';
    document.getElementById('btnCancelar').style.display = 'none';
    document.getElementById('formEliminar').style.display = '';
    document.getElementById('inputConfirmarNombre').focus();
}

document.getElementById('inputConfirmarNombre').addEventListener('input', function() {
    var nombreEsperado = <?= json_encode($usuario['nameUser']) ?>;
    document.getElementById('btnSegundaConfirmacion').disabled = this.value.trim() !== nombreEsperado;
});
</script>
<?php endif; ?>