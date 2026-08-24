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
            <div class="form-group">
                <label>Cargo *</label>
                <select name="rol" required>
                    <option value="">Seleccione</option>
                    <option value="recepcionista">Recepcionista</option>
                    <option value="tecnico">Técnico</option>
                    <option value="jefe_tecnico">Jefe Técnico</option>
                    <option value="almacenista">Almacenista</option>
                    <option value="admin_sucursal">Admin. Sucursal</option>
                    <option value="rrhh">Recursos Humanos</option>
                    <option value="gerente">Gerente</option>
                </select>
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
                    <th>Cargo</th>
                    <th>Estado</th>
                    <th>Registrado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <?php if ($u['foto']): ?>
                            <img src="<?= APP_URL ?>/public/imagen/foto-usuario?id=<?= $u['id'] ?>" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #E0E0E0;"></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido_paterno'] . ' ' . ($u['apellido_materno'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($u['carnet']) ?></td>
                    <td><?= htmlspecialchars($u['sucursal_nombre'] ?? 'Sin asignar') ?></td>
                    <td><span class="badge badge-negro"><?= ucfirst(str_replace('_', ' ', $u['rol'])) ?></span></td>
                    <td>
                        <?php if ($u['activo']): ?>
                            <span class="badge badge-verde">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-rojo">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['registrado_por_nombre'] ?? 'Sistema') ?></td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="<?= APP_URL ?>/public/usuarios/editar?id=<?= $u['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
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

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
