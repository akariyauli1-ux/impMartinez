<?php $titulo = 'Gestión de Usuarios'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Crear Nuevo Usuario</h2>
    </div>
    <form method="POST" action="<?= APP_URL ?>/public/usuarios/guardar" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Apellido Paterno *</label>
                <input type="text" name="apellido_paterno" required>
            </div>
            <div class="form-group">
                <label>Apellido Materno</label>
                <input type="text" name="apellido_materno">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Carnet *</label>
                <input type="text" name="carnet" required>
            </div>
            <div class="form-group">
                <label>Contraseña *</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Teléfono</label>
                <input type="tel" name="telefono">
            </div>
            <div class="form-group">
                <label>Sucursal *</label>
                <select name="sucursal_id" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Roles *</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <?php foreach ($roles as $rol): ?>
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="roles[]" value="<?= $rol['id'] ?>">
                        <?= htmlspecialchars($rol['descripcion']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label>Foto</label>
            <input type="file" name="foto" accept="image/*">
        </div>
        
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Lista de Usuarios</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nombre Completo</th>
                    <th>Carnet</th>
                    <th>Sucursal</th>
                    <th>Roles</th>
                    <th>Estado</th>
                    <th>Registrado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <?php if (!empty($u['foto'])): ?>
                            <img src="<?= APP_URL ?>/public/imagen/foto-usuario?id=<?= $u['id'] ?>" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #E0E0E0;"></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido_paterno'] . ' ' . ($u['apellido_materno'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($u['carnet']) ?></td>
                    <td><?= htmlspecialchars($u['sucursal_nombre'] ?? 'Sin asignar') ?></td>
                    <td>
                        <?php 
                        $roles_usuario = explode(',', $u['roles'] ?? '');
                        foreach ($roles_usuario as $rol): 
                            if (!empty($rol)):
                        ?>
                            <span class="badge badge-negro" style="margin: 2px; display: inline-block;"><?= ucfirst(str_replace('_', ' ', $rol)) ?></span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </td>
                    <td>
                        <?php if ($u['activo']): ?>
                            <span class="badge badge-verde">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-rojo">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['registrado_por_nombre'] ?? 'Sistema') ?></td>
                    <td>
                        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <a href="<?= APP_URL ?>/public/usuarios/editar?id=<?= $u['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="abrirModalRoles(<?= $u['id'] ?>, '<?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido_paterno']) ?>')">Roles</button>
                            <form method="POST" action="<?= APP_URL ?>/public/usuarios/toggle-estado" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn <?= $u['activo'] ? 'btn-danger' : 'btn-secondary' ?> btn-sm">
                                    <?= $u['activo'] ? 'Deshabilitar' : 'Habilitar' ?>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para gestionar roles -->
<div id="modalRoles" class="modal-overlay" style="display: none;">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <h2>Gestionar Roles de <span id="modalUsuarioNombre"></span></h2>
            <button class="modal-close" onclick="cerrarModalRoles()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/usuarios/gestionar-roles">
            <input type="hidden" name="id" id="modalUsuarioId">
            <div class="form-group">
                <label>Seleccionar Roles</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <?php foreach ($roles as $rol): ?>
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" name="roles[]" value="<?= $rol['id'] ?>" class="rol-checkbox">
                            <?= htmlspecialchars($rol['descripcion']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Guardar Roles</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModalRoles()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalRoles(usuarioId, usuarioNombre) {
    document.getElementById('modalUsuarioId').value = usuarioId;
    document.getElementById('modalUsuarioNombre').textContent = usuarioNombre;
    
    // Limpiar checkboxes
    document.querySelectorAll('.rol-checkbox').forEach(cb => cb.checked = false);
    
    // Cargar roles actuales del usuario
    fetch('<?= APP_URL ?>/public/usuarios/obtener-roles?id=' + usuarioId)
        .then(response => response.json())
        .then(data => {
            data.forEach(rolId => {
                const checkbox = document.querySelector(`.rol-checkbox[value="${rolId}"]`);
                if (checkbox) checkbox.checked = true;
            });
        });
    
    document.getElementById('modalRoles').style.display = 'flex';
}

function cerrarModalRoles() {
    document.getElementById('modalRoles').style.display = 'none';
}
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
