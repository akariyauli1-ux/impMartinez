<?php $titulo = 'Nuevo Cliente'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Registrar Nuevo Cliente</h2>
    </div>
    <form method="POST" action="<?= APP_URL ?>/public/recepcion/guardar-cliente">
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
                <label>DNI</label>
                <input type="text" name="dni" maxlength="8">
            </div>
            <div class="form-group">
                <label>Teléfono *</label>
                <input type="tel" name="telefono" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email">
            </div>
        </div>
        
        <div class="form-group">
            <label>Dirección</label>
            <textarea name="direccion" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar Cliente</button>
    </form>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
