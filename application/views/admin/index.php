<?php $this->load->view('admin/template/header', $title); ?>
<style>
    .bg-soft-warning {
        background-color: var(--warning-transparent) !important;
    }

    /* ===== Header row ===== */
    .analytics-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .range-pills .btn {
        border-radius: 20px;
        font-size: 13px;
        padding: 6px 14px;
    }

    /* ===== KPI cards ===== */
    .kpi-card {
        border-radius: 12px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: default;
        height: 100%;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
    }

    .kpi-card .card-body {
        position: relative;
        z-index: 1;
        padding: 20px;
    }

    .kpi-card .kpi-icon {
        position: absolute;
        right: 16px;
        top: 16px;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .kpi-card .kpi-label {
        opacity: 0.9;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .kpi-card .kpi-value {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .kpi-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.25);
    }

    .kpi-trend.down {
        background: rgba(0, 0, 0, 0.18);
    }

    .kpi-sparkline {
        margin-top: 8px;
        height: 40px;
    }

    /* ===== Generic section card ===== */
    .panel-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    }

    .panel-title {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 0;
    }

    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 8px;
    }

    .toggle-pills .btn {
        font-size: 12px;
        padding: 4px 12px;
    }

    /* ===== Sales summary ===== */
    .summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px solid #f1f1f5;
    }

    .summary-row:last-child {
        border-bottom: none;
        padding-top: 12px;
        font-weight: 700;
    }

    .summary-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    /* ===== Product table ===== */
    .product-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
    }

    .growth-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .growth-up {
        background: #e6f7ed;
        color: #1d9d5b;
    }

    .growth-down {
        background: #fde8e8;
        color: #d6493e;
    }

    .growth-new {
        background: #eef1ff;
        color: #556ee6;
    }

    /* ===== Skeleton ===== */
    .chart-skeleton {
        height: 280px;
        border-radius: 8px;
        background: linear-gradient(90deg, #f0f1f5 25%, #f7f8fa 37%, #f0f1f5 63%);
        background-size: 400% 100%;
        animation: skeleton-shimmer 1.4s ease infinite;
    }

    @keyframes skeleton-shimmer {
        0% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0 50%;
        }
    }

    .empty-state {
        text-align: center;
        padding: 40px 10px;
        color: #98a6ad;
    }

    .empty-state i {
        font-size: 36px;
        display: block;
        margin-bottom: 8px;
    }

    .mini-stats-wid {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .mini-stats-wid:hover {
        transform: translateY(-4px);
    }
</style>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- ===== Header ===== -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 font-size-18">Analytics Dashboard</h4>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                                <li class="breadcrumb-item active">Analytics</li>
                            </ol>
                        </div>
                        <div class="d-flex gap-2">
                            <!-- <a href="<?= base_url('addOrder') ?>" class="btn btn-success btn-sm">
                                <i class="bx bx-plus align-middle"></i> New Order
                            </a> -->
                            <button type="button" id="refreshDataBtn" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-refresh align-middle"></i> Refresh Data
                            </button>
                            <button type="button" id="exportProductsBtn" class="btn btn-primary btn-sm">
                                <i class="bx bx-download align-middle"></i> Export Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Date range toolbar ===== -->
            <form method="post" action="<?= base_url('dashboard') ?>" id="rangeForm">
                <div class="analytics-toolbar">
                    <div class="range-pills btn-group" role="group">
                        <?php
                        $presets = [
                            'today' => 'Today',
                            'yesterday' => 'Yesterday',
                            'last7' => 'Last 7 Days',
                            'last30' => 'Last 30 Days',
                            'this_month' => 'This Month',
                        ];
                        foreach ($presets as $key => $label):
                        ?>
                            <button type="submit" name="range" value="<?= $key ?>" class="btn <?= $range == $key ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $label ?></button>
                        <?php endforeach; ?>
                        <button type="button" class="btn <?= $range == 'custom' ? 'btn-primary' : 'btn-outline-secondary' ?>" id="customRangeToggle">Custom Range</button>
                    </div>

                    <div class="d-flex gap-2 align-items-end <?= $range == 'custom' ? '' : 'd-none' ?>" id="customRangeInputs">
                        <input type="text" id="searchByDateFrom" class="form-control form-control-sm" name="searchByDateFrom" placeholder="From dd-mm-yyyy" autocomplete="off" value="<?= date('d-m-Y', strtotime($dateFrom)) ?>">
                        <input type="text" id="searchByDateTo" class="form-control form-control-sm" name="searchByDateTo" placeholder="To dd-mm-yyyy" autocomplete="off" value="<?= date('d-m-Y', strtotime($dateTo)) ?>">
                        <button type="submit" name="range" value="custom" class="btn btn-primary btn-sm">Apply</button>
                    </div>
                </div>
            </form>

            <!-- ===== KPI cards ===== -->
            <div class="row">
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card kpi-card gr1">
                        <div class="card-body">
                            <div class="kpi-icon"><i class="bx bx-rupee"></i></div>
                            <p class="kpi-label">Gross Sales</p>
                            <h4 class="kpi-value">₹<span class="counter" data-counter="<?= $gross_sale ?>" data-decimals="2">0</span></h4>
                            <span class="kpi-trend <?= $gross_sale_change < 0 ? 'down' : '' ?>">
                                <i class="bx bx-<?= $gross_sale_change < 0 ? 'down-arrow-alt' : 'up-arrow-alt' ?>"></i>
                                <?= abs($gross_sale_change) ?>%
                            </span>
                            <div class="kpi-sparkline" id="sparkGross"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card kpi-card gr2">
                        <div class="card-body">
                            <div class="kpi-icon"><i class="bx bx-wallet"></i></div>
                            <p class="kpi-label">Net Sales</p>
                            <h4 class="kpi-value">₹<span class="counter" data-counter="<?= $net_sale ?>" data-decimals="2">0</span></h4>
                            <span class="kpi-trend <?= $net_sale_change < 0 ? 'down' : '' ?>">
                                <i class="bx bx-<?= $net_sale_change < 0 ? 'down-arrow-alt' : 'up-arrow-alt' ?>"></i>
                                <?= abs($net_sale_change) ?>%
                            </span>
                            <div class="kpi-sparkline" id="sparkNet"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card kpi-card gr3">
                        <div class="card-body">
                            <div class="kpi-icon"><i class="bx bx-cart-alt"></i></div>
                            <p class="kpi-label">Total Orders</p>
                            <h4 class="kpi-value"><span class="counter" data-counter="<?= $total_orders ?>">0</span></h4>
                            <span class="kpi-trend <?= $orders_change < 0 ? 'down' : '' ?>">
                                <i class="bx bx-<?= $orders_change < 0 ? 'down-arrow-alt' : 'up-arrow-alt' ?>"></i>
                                <?= abs($orders_change) ?>%
                            </span>
                            <div class="kpi-sparkline" id="sparkOrders"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card kpi-card gr4">
                        <div class="card-body">
                            <div class="kpi-icon"><i class="bx bx-show"></i></div>
                            <p class="kpi-label">Sessions</p>
                            <h4 class="kpi-value"><span class="counter" data-counter="<?= $total_sessions ?>">0</span></h4>
                            <span class="kpi-trend <?= $sessions_change < 0 ? 'down' : '' ?>">
                                <i class="bx bx-<?= $sessions_change < 0 ? 'down-arrow-alt' : 'up-arrow-alt' ?>"></i>
                                <?= abs($sessions_change) ?>%
                            </span>
                            <div class="kpi-sparkline" id="sparkSessions"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Main trend + summary ===== -->
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <div class="card panel-card h-100">
                        <div class="card-body">
                            <div class="panel-head">
                                <h5 class="panel-title">Sales Trend</h5>
                                <div class="d-flex gap-2 flex-wrap">
                                    <div class="btn-group toggle-pills" role="group" id="metricToggle">
                                        <button type="button" class="btn btn-primary" data-metric="gross">Gross</button>
                                        <button type="button" class="btn btn-outline-secondary" data-metric="net">Net</button>
                                        <button type="button" class="btn btn-outline-secondary" data-metric="orders">Orders</button>
                                    </div>
                                    <div class="btn-group toggle-pills" role="group" id="granularityToggle">
                                        <button type="button" class="btn btn-primary" data-granularity="daily">Daily</button>
                                        <button type="button" class="btn btn-outline-secondary" data-granularity="weekly">Weekly</button>
                                        <button type="button" class="btn btn-outline-secondary" data-granularity="monthly">Monthly</button>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-skeleton" id="trendSkeleton"></div>
                            <div id="trendChart" style="display:none;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="card panel-card h-100">
                        <div class="card-body">
                            <h5 class="panel-title mb-3">Sales Summary</h5>
                            <?php
                            $gross = max($gross_sale, 0.01);
                            $rows = [
                                ['Gross Sales', $gross_sale, '#556ee6'],
                                ['Discounts', -$discounts, '#f1556c'],
                                ['Shipping', $shipping, '#f1b44c'],
                            ];
                            ?>
                            <?php foreach ($rows as $r): ?>
                                <div class="summary-row">
                                    <span><span class="summary-dot" style="background:<?= $r[2] ?>"></span><?= $r[0] ?></span>
                                    <span>
                                        <?= $r[1] < 0 ? '-' : '' ?>₹<?= number_format(abs($r[1]), 2) ?>
                                        <small class="text-muted">(<?= number_format((abs($r[1]) / $gross) * 100, 1) ?>%)</small>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                            <div class="summary-row">
                                <span>Net Sales</span>
                                <span>₹<?= number_format($net_sale, 2) ?></span>
                            </div>
                            <div id="summaryBar" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Product performance ===== -->
            <div class="row">
                <div class="col-lg-7 mb-3">
                    <div class="card panel-card h-100">
                        <div class="card-body">
                            <h5 class="panel-title mb-3">Top Selling Products</h5>
                            <?php if ($top_products): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Units Sold</th>
                                                <th>Orders</th>
                                                <th>Revenue</th>
                                                <th>Growth</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_products as $row): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?= $row['image_path'] ? base_url('upload/product/' . $row['image_path']) : base_url('assets/img/logo.jpg') ?>" onerror="this.src='<?= base_url('assets/img/logo.jpg') ?>'" class="product-thumb me-2" alt="">
                                                            <span><?= htmlspecialchars($row['product_name']) ?></span>
                                                        </div>
                                                    </td>
                                                    <td><?= $row['total_qty'] ?></td>
                                                    <td><?= $row['total_orders'] ?></td>
                                                    <td>₹<?= number_format($row['total_revenue'], 2) ?></td>
                                                    <td>
                                                        <?php if ($row['growth'] === null): ?>
                                                            <span class="growth-badge growth-new">New</span>
                                                        <?php else: ?>
                                                            <span class="growth-badge <?= $row['growth'] >= 0 ? 'growth-up' : 'growth-down' ?>">
                                                                <?= $row['growth'] >= 0 ? '▲' : '▼' ?> <?= abs($row['growth']) ?>%
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="bx bx-package"></i>
                                    No product sales in this date range
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 mb-3">
                    <div class="card panel-card h-100">
                        <div class="card-body">
                            <h5 class="panel-title mb-3">Top 10 by Revenue</h5>
                            <?php if ($top_products): ?>
                                <div id="topProductsChart"></div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="bx bx-bar-chart-alt-2"></i>
                                    Nothing to chart yet
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Sessions ===== -->
            <div class="row">
                <div class="col-lg-12 mb-3">
                    <div class="card panel-card">
                        <div class="card-body">
                            <div class="panel-head">
                                <h5 class="panel-title">Sessions Over Time</h5>
                                <small class="text-muted">Current period vs previous period</small>
                            </div>
                            <div class="chart-skeleton" id="sessionsSkeleton"></div>
                            <div id="sessionsChart" style="display:none;"></div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <p class="text-muted mb-0">Total Sessions</p>
                                    <h4><span class="counter" data-counter="<?= $total_sessions ?>">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Existing overview widgets ===== -->
            <div class="row mt-2">
                <div class="col-lg-12">
                    <h4>Users</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="<?= base_url('activeUser') ?>">
                                <div class="card mini-stats-wid gr1">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">Active User</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $active_user ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('inactiveUser') ?>">
                                <div class="card mini-stats-wid gr2">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">Inactive User</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $inactive_user ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <h4>Products</h4>
                        <div class="col-md-3">
                            <a href="<?= base_url('categoryAll') ?>">
                                <div class="card mini-stats-wid gr3">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">Total Category</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $product_category ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('productAll') ?>">
                                <div class="card mini-stats-wid gr4">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">Total Product</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $total_product ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <h4>Orders</h4>
                        <div class="col-md-3">
                            <a href="<?= base_url('allOrders?status=0') ?>">
                                <div class="card mini-stats-wid gr3">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">New Orders</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $recent_orders ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('allOrders?status=1') ?>">
                                <div class="card mini-stats-wid gr1">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">Accepted Orders</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $accepted_orders ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('allOrders?status=3') ?>">
                                <div class="card mini-stats-wid gr2">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">Dispatch Orders</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $dispatch_orders ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="<?= base_url('allOrders?status=4') ?>">
                                <div class="card mini-stats-wid gr3">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">Completed Orders</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $completed_orders ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('allOrders?status=2') ?>">
                                <div class="card mini-stats-wid gr4">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="text-muted fw-medium">Canceled Orders</p>
                                                <h4 class="mb-0"><span class="counter" data-counter="<?= $canceled_orders ?>">0</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var DAILY_SERIES = <?= json_encode($daily_series) ?>;
    var SESSIONS_SERIES = <?= json_encode($sessions_series) ?>;
    var SESSIONS_SERIES_PREV = <?= json_encode($sessions_series_prev) ?>;
    var TOP_PRODUCTS = <?= json_encode(array_map(function ($p) {
                            return ['name' => $p['product_name'], 'revenue' => (float) $p['total_revenue']];
                        }, array_slice($top_products ?: [], 0, 10))) ?>;

    document.addEventListener('DOMContentLoaded', function () {

        // ---- Count-up counters ----
        document.querySelectorAll('.counter').forEach(function (el) {
            var target = parseFloat(el.getAttribute('data-counter')) || 0;
            var decimals = parseInt(el.getAttribute('data-decimals')) || 0;
            var duration = 1200;
            var start = null;

            function step(timestamp) {
                if (!start) start = timestamp;
                var progress = Math.min((timestamp - start) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = (target * eased).toLocaleString('en-IN', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals,
                });
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = target.toLocaleString('en-IN', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals,
                    });
                }
            }

            requestAnimationFrame(step);
        });

        // ---- KPI sparklines ----
        function sparkline(elId, key, color) {
            new ApexCharts(document.querySelector(elId), {
                chart: { type: 'area', height: 40, sparkline: { enabled: true } },
                series: [{ data: DAILY_SERIES.map(function (d) { return d[key]; }) }],
                stroke: { curve: 'smooth', width: 2 },
                fill: { opacity: 0.3 },
                colors: [color],
                tooltip: { enabled: false },
            }).render();
        }
        sparkline('#sparkGross', 'gross', '#fff');
        sparkline('#sparkNet', 'net', '#fff');
        sparkline('#sparkOrders', 'orders', '#fff');
        sparkline('#sparkSessions', 'sessions', '#fff');
        // Sessions sparkline uses its own series (not part of DAILY_SERIES)
        new ApexCharts(document.querySelector('#sparkSessions'), {
            chart: { type: 'area', height: 40, sparkline: { enabled: true } },
            series: [{ data: SESSIONS_SERIES.map(function (d) { return d.sessions; }) }],
            stroke: { curve: 'smooth', width: 2 },
            fill: { opacity: 0.3 },
            colors: ['#fff'],
            tooltip: { enabled: false },
        }).render();

        // ---- Sales summary breakdown bar ----
        new ApexCharts(document.querySelector('#summaryBar'), {
            chart: { type: 'bar', height: 60, stacked: true, toolbar: { show: false } },
            series: [
                { name: 'Net', data: [<?= max($net_sale, 0) ?>] },
                { name: 'Discounts', data: [<?= $discounts ?>] },
                { name: 'Shipping', data: [<?= $shipping ?>] },
            ],
            colors: ['#556ee6', '#f1556c', '#f1b44c'],
            plotOptions: { bar: { horizontal: true, barHeight: '60%' } },
            xaxis: { categories: ['Breakdown'] },
            legend: { position: 'bottom' },
            dataLabels: { enabled: false },
        }).render();

        // ---- Main trend chart (toggle metric + granularity) ----
        function bucketSeries(granularity, metric) {
            if (granularity === 'daily') {
                return {
                    categories: DAILY_SERIES.map(function (d) { return d.date; }),
                    values: DAILY_SERIES.map(function (d) { return d[metric]; }),
                };
            }
            var buckets = {};
            DAILY_SERIES.forEach(function (d) {
                var dateObj = new Date(d.date);
                var key;
                if (granularity === 'weekly') {
                    var firstDay = new Date(dateObj);
                    firstDay.setDate(dateObj.getDate() - dateObj.getDay());
                    key = firstDay.toISOString().slice(0, 10);
                } else {
                    key = d.date.slice(0, 7);
                }
                buckets[key] = (buckets[key] || 0) + d[metric];
            });
            var keys = Object.keys(buckets).sort();
            return { categories: keys, values: keys.map(function (k) { return buckets[k]; }) };
        }

        var currentMetric = 'gross';
        var currentGranularity = 'daily';
        var trendChart = new ApexCharts(document.querySelector('#trendChart'), {
            chart: { type: 'area', height: 280, toolbar: { show: false } },
            series: [{ name: 'Gross', data: bucketSeries('daily', 'gross').values }],
            xaxis: { categories: bucketSeries('daily', 'gross').categories },
            colors: ['#556ee6'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
            tooltip: { x: { show: true } },
        });
        trendChart.render();
        document.getElementById('trendSkeleton').style.display = 'none';
        document.getElementById('trendChart').style.display = 'block';

        function refreshTrendChart() {
            var bucketed = bucketSeries(currentGranularity, currentMetric);
            trendChart.updateOptions({
                series: [{ name: currentMetric, data: bucketed.values }],
                xaxis: { categories: bucketed.categories },
            });
        }

        document.querySelectorAll('#metricToggle [data-metric]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#metricToggle .btn').forEach(function (b) {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.add('btn-primary');
                this.classList.remove('btn-outline-secondary');
                currentMetric = this.getAttribute('data-metric');
                refreshTrendChart();
            });
        });

        document.querySelectorAll('#granularityToggle [data-granularity]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#granularityToggle .btn').forEach(function (b) {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.add('btn-primary');
                this.classList.remove('btn-outline-secondary');
                currentGranularity = this.getAttribute('data-granularity');
                refreshTrendChart();
            });
        });

        // ---- Top products horizontal bar ----
        if (TOP_PRODUCTS.length) {
            new ApexCharts(document.querySelector('#topProductsChart'), {
                chart: { type: 'bar', height: 320, toolbar: { show: false } },
                series: [{ name: 'Revenue', data: TOP_PRODUCTS.map(function (p) { return p.revenue; }) }],
                plotOptions: { bar: { horizontal: true, barHeight: '60%' } },
                xaxis: { categories: TOP_PRODUCTS.map(function (p) { return p.name; }) },
                colors: ['#34c38f'],
                dataLabels: { enabled: false },
            }).render();
        }

        // ---- Sessions over time (current vs previous) ----
        new ApexCharts(document.querySelector('#sessionsChart'), {
            chart: { type: 'line', height: 280, toolbar: { show: false } },
            series: [
                { name: 'Current Period', data: SESSIONS_SERIES.map(function (d) { return d.sessions; }) },
                { name: 'Previous Period', data: SESSIONS_SERIES_PREV.map(function (d) { return d.sessions; }) },
            ],
            xaxis: { categories: SESSIONS_SERIES.map(function (d) { return d.date; }) },
            colors: ['#556ee6', '#c3c8d6'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: [3, 2], dashArray: [0, 4] },
            tooltip: { x: { show: true } },
        }).render();
        document.getElementById('sessionsSkeleton').style.display = 'none';
        document.getElementById('sessionsChart').style.display = 'block';

        // ---- Product table: DataTables search/sort/pagination + export ----
        if (document.getElementById('productTable')) {
            $('#productTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                dom: 'Bfrtip',
                buttons: ['excel', 'pdf'],
            });
            document.getElementById('exportProductsBtn').addEventListener('click', function () {
                $('.buttons-excel').trigger('click');
            });
        }

        // ---- Custom range toggle ----
        $('#searchByDateFrom, #searchByDateTo').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        var customToggle = document.getElementById('customRangeToggle');
        if (customToggle) {
            customToggle.addEventListener('click', function () {
                document.getElementById('customRangeInputs').classList.remove('d-none');
            });
        }

        // ---- Refresh ----
        document.getElementById('refreshDataBtn').addEventListener('click', function () {
            window.location.reload();
        });
    });
</script>

<?php $this->load->view('admin/template/footer'); ?>
