<?php $titulo = 'Editar Usuario'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Editar Usuario: <?= htmlspecialchars($usuario_editar['nombre'] . ' ' . $usuario_editar['apellido_paterno']) ?></h2>
    </div>
    <form method="POST" action="<?= APP_URL ?>/public/usuarios/actualizar" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $usuario_editar['id'] ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario_editar['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label>Apellido Paterno *</label>
                <input type="text" name="apellido_paterno" value="<?= htmlspecialchars($usuario_editar['apellido_paterno']) ?>" required>
            </div>
            <div class="form-group">
                <label>Apellido Materno</label>
                <input type="text" name="apellido_materno" value="<?= htmlspecialchars($usuario_editar['apellido_materno'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Carnet *</label>
                <input type="text" name="carnet" value="<?= htmlspecialchars($usuario_editar['carnet']) ?>" required>
            </div>
            <div class="form-group">
                <label>Contraseña (dejar vacío para no cambiar)</label>
                <input type="password" name="password" minlength="6">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario_editar['email'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Teléfono</label>
                <input type="tel" name="telefono" value="<?= htmlspecialchars($usuario_editar['telefono'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Sucursal *</label>
                <select name="sucursal_id" required>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $usuario_editar['sucursal_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Cargo *</label>
                <select name="rol" required>
                    <option value="recepcionista" <?= $usuario_editar['rol'] === 'recepcionista' ? 'selected' : '' ?>>Recepcionista</option>
                    <option value="tecnico" <?= $usuario_editar['rol'] === 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                    <option value="jefe_tecnico" <?= $usuario_editar['rol'] === 'jefe_tecnico' ? 'selected' : '' ?>>Jefe Técnico</option>
                    <option value="almacenista" <?= $usuario_editar['rol'] === 'almacenista' ? 'selected' : '' ?>>Almacenista</option>
                    <option value="admin_sucursal" <?= $usuario_editar['rol'] === 'admin_sucursal' ? 'selected' : '' ?>>Admin. Sucursal</option>
                    <option value="rrhh" <?= $usuario_editar['rol'] === 'rrhh' ? 'selected' : '' ?>>Recursos Humanos</option>
                    <option value="gerente" <?= $usuario_editar['rol'] === 'gerente' ? 'selected' : '' ?>>Gerente</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Foto (dejar vacío para mantener la actual)</label>
            <?php if ($usuario_editar['foto']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?= APP_URL ?>/public/imagen/foto-usuario?id=<?= $usuario_editar['id'] ?>" alt="Foto actual" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                </div>
            <?php endif; ?>
            <input type="file" name="foto" accept="image/*">
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="<?= APP_URL ?>/public/usuarios" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
