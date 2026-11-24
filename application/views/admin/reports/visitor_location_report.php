<?php $this->load->view('admin/template/header', $title); ?>
<?php
$pct = function ($part, $whole) {
    return $whole > 0 ? round($part * 100 / $whole, 1) : 0;
};
$rangeQuery = 'date_from=' . urlencode($date_from) . '&date_to=' . urlencode($date_to);
$countryName = '';
foreach ($by_country as $row) {
    if ($row['country_code'] === $country_filter) {
        $countryName = $row['country'] ?: $row['country_code'];
    }
}
?>

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

            <form method="get" class="row mb-3">
                <div class="col-lg-3">
                    <label>From Date</label>
                    <input type="text" class="form-control" name="date_from" value="<?= $date_from ?>" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true">
                </div>
                <div class="col-lg-3">
                    <label>To Date</label>
                    <input type="text" class="form-control" name="date_to" value="<?= $date_to ?>" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true">
                </div>
                <?php if ($country_filter) : ?>
                    <input type="hidden" name="country" value="<?= html_escape($country_filter) ?>">
                <?php endif; ?>
                <div class="col-lg-2 align-self-end">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>

            <div class="row">
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Visits</p>
                        <h3><?= number_format($visits) ?></h3>
                        <small class="text-muted"><?= number_format($visitors) ?> unique visitors</small>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Visits with Location</p>
                        <h3><?= number_format($located) ?></h3>
                        <small class="text-muted"><?= $pct($located, $visits) ?>% of visits</small>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Countries</p>
                        <h3><?= number_format($country_count) ?></h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">Cities</p>
                        <h3><?= number_format($city_count) ?></h3>
                    </div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h5>By Country</h5>
                            <p class="text-muted small mb-2">Click a country to see its states and cities.</p>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th class="text-end">Visits</th>
                                        <th class="text-end">Visitors</th>
                                        <th style="width:30%">Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($by_country)) : ?>
                                        <tr><td colspan="4" class="text-center">No located visits in this range.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($by_country as $row) : $share = $pct($row['visits'], $located); ?>
                                        <tr class="<?= $row['country_code'] === $country_filter ? 'table-primary' : '' ?>">
                                            <td>
                                                <a href="<?= base_url('visitorLocationReport') . '?' . $rangeQuery . '&country=' . urlencode($row['country_code']) ?>">
                                                    <?= html_escape($row['country'] ?: $row['country_code']) ?>
                                                </a>
                                                <span class="badge bg-light text-dark"><?= html_escape($row['country_code']) ?></span>
                                            </td>
                                            <td class="text-end"><?= number_format($row['visits']) ?></td>
                                            <td class="text-end"><?= number_format($row['visitors']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height:6px"><div class="progress-bar" style="width:<?= $share ?>%"></div></div>
                                                    <small><?= $share ?>%</small>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php foreach ($unlocated as $row) : ?>
                                        <tr class="text-muted">
                                            <td><em><?= html_escape($row['reason']) ?></em></td>
                                            <td class="text-end"><?= number_format($row['visits']) ?></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">By State / Region<?= $country_filter ? ' &mdash; ' . html_escape($countryName ?: $country_filter) : '' ?></h5>
                                <?php if ($country_filter) : ?>
                                    <a href="<?= base_url('visitorLocationReport') . '?' . $rangeQuery ?>" class="btn btn-sm btn-outline-secondary">Show all countries</a>
                                <?php endif; ?>
                            </div>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>State / Region</th>
                                        <?php if (!$country_filter) : ?><th>Country</th><?php endif; ?>
                                        <th class="text-end">Visits</th>
                                        <th class="text-end">Visitors</th>
                                        <th class="text-end">Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($by_region)) : ?>
                                        <tr><td colspan="5" class="text-center">No data.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($by_region as $row) : ?>
                                        <tr>
                                            <td><?= html_escape($row['region']) ?></td>
                                            <?php if (!$country_filter) : ?><td><?= html_escape($row['country_code']) ?></td><?php endif; ?>
                                            <td class="text-end"><?= number_format($row['visits']) ?></td>
                                            <td class="text-end"><?= number_format($row['visitors']) ?></td>
                                            <td class="text-end"><?= $pct($row['visits'], $scoped_total) ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Top Cities<?= $country_filter ? ' &mdash; ' . html_escape($countryName ?: $country_filter) : '' ?></h5>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>City</th>
                                        <th>State / Region</th>
                                        <?php if (!$country_filter) : ?><th>Country</th><?php endif; ?>
                                        <th class="text-end">Visits</th>
                                        <th class="text-end">Visitors</th>
                                        <th class="text-end">Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($by_city)) : ?>
                                        <tr><td colspan="6" class="text-center">No data.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($by_city as $row) : ?>
                                        <tr>
                                            <td><?= html_escape($row['city']) ?></td>
                                            <td><?= html_escape($row['region']) ?></td>
                                            <?php if (!$country_filter) : ?><td><?= html_escape($row['country_code']) ?></td><?php endif; ?>
                                            <td class="text-end"><?= number_format($row['visits']) ?></td>
                                            <td class="text-end"><?= number_format($row['visitors']) ?></td>
                                            <td class="text-end"><?= $pct($row['visits'], $scoped_total) ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-muted small">
                Location is estimated from the visitor's IP address, so city-level results are approximate (mobile networks often show the carrier's hub city).
                A visit is one browser on one day. <a href="https://db-ip.com" target="_blank" rel="noopener">IP Geolocation by DB-IP</a>.
            </p>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
