<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                    </div>
                </div>
            </div>

            <form method="get" class="row">
                <div class="col-lg-3 mb-3">
                    <label>From Date</label>
                    <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>">
                </div>
                <div class="col-lg-3 mb-3">
                    <label>To Date</label>
                    <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>">
                </div>
                <div class="col-lg-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </form>

            <div class="row">
                <div class="col-sm-6 col-lg-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Returns</p>
                            <h4 class="mb-0"><?= $total_returns ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Return % <span class="text-muted">(of <?= $delivered_orders ?> delivered orders)</span></p>
                            <h4 class="mb-0"><?= $return_percentage ?>%</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Refund Amount (Completed)</p>
                            <h4 class="mb-0">₹<?= number_format($total_refund_amount, 2) ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>Monthly Returns (last 12 months)</h5>
                            <div id="monthlyReturnsChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>Top Return Reasons</h5>
                            <div id="topReasonsChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>Top Returned Products</h5>
                            <div id="topProductsChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>Courier Performance</h5>
                            <?php if ($courier_performance): ?>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Courier</th>
                                            <th>Returns Handled</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courier_performance as $c): ?>
                                            <tr>
                                                <td><?= $c['shiprocket_courier_name'] ?></td>
                                                <td><?= $c['cnt'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">No courier data yet - populated once Shiprocket pickups are scheduled.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>Top Customers by Return Frequency</h5>
                            <?php if ($customer_frequency): ?>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Mobile</th>
                                            <th>Returns</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($customer_frequency as $c): ?>
                                            <tr>
                                                <td><?= $c['name'] ?></td>
                                                <td><?= $c['contact_no'] ?></td>
                                                <td><?= $c['cnt'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">No returns in the selected range.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
<script>
    var MONTHLY_RETURNS = <?= json_encode($monthly_returns) ?>;
    var TOP_REASONS = <?= json_encode($top_reasons) ?>;
    var TOP_PRODUCTS = <?= json_encode($top_products) ?>;

    new ApexCharts(document.querySelector('#monthlyReturnsChart'), {
        chart: { type: 'area', height: 280, toolbar: { show: false } },
        series: [{ name: 'Returns', data: MONTHLY_RETURNS.map(function(r) { return r.cnt; }) }],
        xaxis: { categories: MONTHLY_RETURNS.map(function(r) { return r.ym; }) },
        colors: ['#556ee6'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
    }).render();

    new ApexCharts(document.querySelector('#topReasonsChart'), {
        chart: { type: 'donut', height: 280 },
        series: TOP_REASONS.map(function(r) { return parseInt(r.cnt, 10); }),
        labels: TOP_REASONS.map(function(r) { return r.reason; }),
    }).render();

    if (TOP_PRODUCTS.length) {
        new ApexCharts(document.querySelector('#topProductsChart'), {
            chart: { type: 'bar', height: 280, toolbar: { show: false } },
            series: [{ name: 'Returns', data: TOP_PRODUCTS.map(function(p) { return p.cnt; }) }],
            plotOptions: { bar: { horizontal: true, barHeight: '60%' } },
            xaxis: { categories: TOP_PRODUCTS.map(function(p) { return p.product_name || ('Product #' + p.product_id); }) },
            colors: ['#34c38f'],
        }).render();
    } else {
        document.querySelector('#topProductsChart').innerHTML = '<p class="text-muted">No returns in the selected range.</p>';
    }
</script>
