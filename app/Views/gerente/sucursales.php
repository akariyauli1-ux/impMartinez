<?php $titulo = 'Gestión de Sucursales'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Logo de la Empresa</h2>
    </div>
    <form method="POST" action="<?= APP_URL ?>/public/gerente/subir-logo" enctype="multipart/form-data">
        <div class="form-group">
            <label>Logo Actual</label>
            <?php if ($logo_empresa && file_exists(__DIR__ . '/../../../uploads/logos/' . $logo_empresa)): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?= APP_URL ?>/uploads/logos/<?= $logo_empresa ?>" alt="Logo" style="max-width: 200px; max-height: 200px; border: 1px solid var(--gris-claro); border-radius: 8px;">
                </div>
            <?php else: ?>
                <p style="color: var(--gris); margin-bottom: 10px;">No hay logo establecido</p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Cambiar Logo</label>
            <input type="file" name="logo_empresa" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir Logo</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Sucursales</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Equipos</th>
                    <th>En Reparación</th>
                    <th>Completados</th>
                    <th>Pendientes</th>
                    <th>Personal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sucursales as $sucursal): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($sucursal['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($sucursal['direccion'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($sucursal['telefono'] ?? '-') ?></td>
                    <td><?= $sucursal['total_equipos'] ?? 0 ?></td>
                    <td><span class="badge badge-amarillo"><?= $sucursal['en_reparacion'] ?? 0 ?></span></td>
                    <td><span class="badge badge-verde"><?= $sucursal['completados'] ?? 0 ?></span></td>
                    <td><span class="badge badge-gris"><?= $sucursal['pendientes'] ?? 0 ?></span></td>
                    <td><?= $sucursal['total_personal'] ?? 0 ?></td>
                    <td>
                        <button onclick="editarSucursal(<?= $sucursal['id'] ?>, '<?= htmlspecialchars($sucursal['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($sucursal['direccion'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($sucursal['telefono'] ?? '', ENT_QUOTES) ?>')" class="btn btn-primary btn-sm">Editar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalEditar" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Editar Sucursal</h2>
            <button class="modal-close" onclick="cerrarModal()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/gerente/guardar-sucursal">
            <input type="hidden" name="sucursal_id" id="sucursal_id">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <textarea name="direccion" id="direccion" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="tel" name="telefono" id="telefono">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarSucursal(id, nombre, direccion, telefono) {
    document.getElementById('sucursal_id').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('direccion').value = direccion;
    document.getElementById('telefono').value = telefono;
    document.getElementById('modalEditar').classList.add('active');
}

function cerrarModal() {
    document.getElementById('modalEditar').classList.remove('active');
}

document.getElementById('modalEditar').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
