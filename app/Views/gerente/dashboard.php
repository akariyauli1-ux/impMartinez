<?php $titulo = 'Dashboard General'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($sucursales) ?></div>
        <div class="stat-label">Sucursales Activas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $total_equipos ?></div>
        <div class="stat-label">Total Equipos</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= $en_reparacion ?></div>
        <div class="stat-label">En Reparacion</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $completados ?></div>
        <div class="stat-label">CompletadoS...</div>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0;">Filtrar por Periodo</h2>
        <div class="filtro-periodo" id="filtroPeriodo">
            <button class="btn-filtro active" data-filtro="todo">Todo</button>
            <button class="btn-filtro" data-filtro="semana">Semana</button>
            <button class="btn-filtro" data-filtro="mes">Mes</button>
            <button class="btn-filtro" data-filtro="anio">Ano</button>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    <div class="card">
        <div class="card-header">
            <h2>Trabajos por Sucursal</h2>
        </div>
        <div style="padding: 15px; position: relative; height: 320px;">
            <canvas id="chartTrabajos"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2>Solicitudes de Componentes por Sucursal</h2>
        </div>
        <div style="padding: 15px; position: relative; height: 320px;">
            <canvas id="chartSolicitudes"></canvas>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <h2>Productos Mas Solicitados al Almacen</h2>
    </div>
    <div style="padding: 15px; position: relative; height: 380px;">
        <canvas id="chartProductos"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Acciones Rapidas</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="<?= APP_URL ?>/public/usuarios" class="btn btn-primary">Gestion Usuarios</a>
        <a href="<?= APP_URL ?>/public/gerente/sucursales" class="btn btn-secondary">Sucursales</a>
        <a href="<?= APP_URL ?>/public/gerente/tecnicos" class="btn btn-outline">Tecnicos</a>
        <a href="<?= APP_URL ?>/public/gerente/almacen" class="btn btn-outline">Almacen</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    const colores = ['#D32F2F', '#1a1a1a', '#F57C00', '#1976D2', '#388E3C', '#7B1FA2', '#00796B', '#C2185B'];
    const coloresAlpha = colores.map(c => c + '33');
    const urlAjax = '<?= APP_URL ?>/public/gerente/estadisticas-ajax';

    let chartTrabajos, chartSolicitudes, chartProductos;

    function crearChartTrabajos(data) {
        const ctx = document.getElementById('chartTrabajos').getContext('2d');
        if (chartTrabajos) chartTrabajos.destroy();
        chartTrabajos = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.sucursal),
                datasets: [
                    { label: 'Completados', data: data.map(d => parseInt(d.completados)), backgroundColor: '#388E3C', borderRadius: 6 },
                    { label: 'En Reparacion', data: data.map(d => parseInt(d.en_reparacion)), backgroundColor: '#F57C00', borderRadius: 6 },
                    { label: 'Pendientes', data: data.map(d => parseInt(d.pendientes)), backgroundColor: '#1976D2', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    function crearChartSolicitudes(data) {
        const ctx = document.getElementById('chartSolicitudes').getContext('2d');
        if (chartSolicitudes) chartSolicitudes.destroy();
        chartSolicitudes = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.sucursal),
                datasets: [{
                    data: data.map(d => parseInt(d.total_solicitudes)),
                    backgroundColor: colores,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15 } }
                }
            }
        });
    }

    function crearChartProductos(data) {
        const ctx = document.getElementById('chartProductos').getContext('2d');
        if (chartProductos) chartProductos.destroy();
        chartProductos = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.nombre),
                datasets: [{
                    label: 'Total Solicitudes',
                    data: data.map(d => parseInt(d.total)),
                    backgroundColor: coloresAlpha.slice(0, data.length),
                    borderColor: colores.slice(0, data.length),
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    function cargarEstadisticas(filtro) {
        const url = filtro && filtro !== 'todo' ? urlAjax + '?filtro=' + filtro : urlAjax;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                crearChartTrabajos(data.trabajosPorSucursal);
                crearChartSolicitudes(data.solicitudesPorSucursal);
                crearChartProductos(data.productosMasSolicitados);
            })
            .catch(err => console.error('Error al cargar estadisticas:', err));
    }

    document.querySelectorAll('.btn-filtro').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-filtro').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            cargarEstadisticas(this.dataset.filtro);
        });
    });

    const labelsTrabajos = <?= json_encode(array_column($trabajosPorSucursal, 'sucursal')) ?>;
    const datosCompletados = <?= json_encode(array_column($trabajosPorSucursal, 'completados')) ?>;
    const datosEnReparacion = <?= json_encode(array_column($trabajosPorSucursal, 'en_reparacion')) ?>;
    const datosPendientes = <?= json_encode(array_column($trabajosPorSucursal, 'pendientes')) ?>;

    chartTrabajos = new Chart(document.getElementById('chartTrabajos'), {
        type: 'bar',
        data: {
            labels: labelsTrabajos,
            datasets: [
                { label: 'Completados', data: datosCompletados, backgroundColor: '#388E3C', borderRadius: 6 },
                { label: 'En Reparacion', data: datosEnReparacion, backgroundColor: '#F57C00', borderRadius: 6 },
                { label: 'Pendientes', data: datosPendientes, backgroundColor: '#1976D2', borderRadius: 6 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                x: { stacked: true, grid: { display: false } },
                y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    const labelsSolicitudes = <?= json_encode(array_column($solicitudesPorSucursal, 'sucursal')) ?>;
    const datosSolicitudes = <?= json_encode(array_column($solicitudesPorSucursal, 'total_solicitudes')) ?>;

    chartSolicitudes = new Chart(document.getElementById('chartSolicitudes'), {
        type: 'doughnut',
        data: {
            labels: labelsSolicitudes,
            datasets: [{
                data: datosSolicitudes,
                backgroundColor: colores,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15 } }
            }
        }
    });

    const labelsProductos = <?= json_encode(array_column($productosMasSolicitados, 'nombre')) ?>;
    const datosProductos = <?= json_encode(array_column($productosMasSolicitados, 'total')) ?>;

    chartProductos = new Chart(document.getElementById('chartProductos'), {
        type: 'bar',
        data: {
            labels: labelsProductos,
            datasets: [{
                label: 'Total Solicitudes',
                data: datosProductos,
                backgroundColor: coloresAlpha.slice(0, labelsProductos.length),
                borderColor: colores.slice(0, labelsProductos.length),
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } },
                y: { grid: { display: false } }
            }
        }
    });
</script>

<style>
    .filtro-periodo {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-filtro {
        padding: 8px 18px;
        border: 2px solid var(--negro, #1a1a1a);
        background: white;
        color: var(--negro, #1a1a1a);
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-filtro:hover {
        background: var(--blanco-humo, #f5f5f5);
    }
    .btn-filtro.active {
        background: var(--negro, #1a1a1a);
        color: white;
    }
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
