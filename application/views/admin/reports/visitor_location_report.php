<?php $this->load->view('admin/template/header', $title); ?>
<?php
// ---- helpers -------------------------------------------------------------
$fmtDate = function ($d) {
    return date('j M Y', strtotime($d));
};
$pct = function ($part, $whole) {
    return $whole > 0 ? round($part * 100 / $whole, 1) : 0;
};
// Change vs previous period, Shopify style: arrow + % (never colour alone).
$change = function ($cur, $prev) {
    if ($prev == 0 && $cur == 0) {
        return '<span class="vl-chg vl-flat">&ndash;</span>';
    }
    if ($prev == 0) {
        return '<span class="vl-chg vl-up" title="No sessions in the previous period">New</span>';
    }
    $p = round(($cur - $prev) * 100 / $prev);
    if ($p == 0) {
        return '<span class="vl-chg vl-flat">0%</span>';
    }
    $up = $p > 0;
    return '<span class="vl-chg ' . ($up ? 'vl-up' : 'vl-down') . '" title="Previous period: ' . number_format($prev) . '">'
        . '<i class="bx ' . ($up ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt') . '"></i>' . abs($p) . '%</span>';
};
$baseQuery = function ($extra = []) use ($preset, $date_from, $date_to) {
    $q = $preset === 'custom' ? ['date_from' => $date_from, 'date_to' => $date_to] : ['range' => $preset];
    return base_url('visitorLocationReport') . '?' . http_build_query(array_merge($q, $extra));
};

$located = $totals['located'];
// Denominator for state/city shares: everything located, or one country.
$scopedTotal = $located;
if ($country_filter) {
    $scopedTotal = 0;
    foreach ($by_country as $r) {
        if ($r['country_code'] === $country_filter) {
            $scopedTotal = $r['sessions'];
        }
    }
}
$scopeLabel = $country_filter ? html_escape($country_filter_name) : 'all countries';

$kpis = [
    ['key' => 'sessions', 'label' => 'Sessions', 'help' => 'One browser visiting on one day.'],
    ['key' => 'visitors', 'label' => 'Visitors', 'help' => 'Distinct browsers. Someone returning on another day is still one visitor.'],
    ['key' => 'countries', 'label' => 'Countries', 'help' => 'Countries with at least one session.'],
    ['key' => 'cities', 'label' => 'Cities', 'help' => 'Cities with at least one session.'],
];

// Thin horizontal bar list (Shopify "Top ..." cards).
$barList = function ($rows, $nameFn, $total, $limit, $linkFn = null) use ($change, $pct) {
    if (empty($rows)) {
        return '<p class="text-muted vl-mb0">No sessions in this period.</p>';
    }
    $max = max(array_column($rows, 'sessions')) ?: 1;
    $html = '<ul class="vl-bars">';
    foreach (array_slice($rows, 0, $limit) as $r) {
        $name = $nameFn($r);
        $label = $linkFn ? '<a href="' . $linkFn($r) . '">' . $name . '</a>' : $name;
        $html .= '<li title="' . number_format($r['sessions']) . ' sessions, ' . number_format($r['visitors']) . ' visitors (' . $pct($r['sessions'], $total) . '%)">'
            . '<div class="vl-bar-row"><span class="vl-bar-name">' . $label . '</span>'
            . '<span class="vl-bar-val">' . number_format($r['sessions']) . '</span>'
            . $change($r['sessions'], $r['prev_sessions']) . '</div>'
            . '<div class="vl-bar-track"><div class="vl-bar-fill" style="width:' . max(1, round($r['sessions'] * 100 / $max, 1)) . '%"></div></div>'
            . '</li>';
    }
    return $html . '</ul>';
};
$countryLink = function ($r) use ($baseQuery) {
    return $baseQuery(['country' => $r['country_code']]);
};

// Data for the charts and maps.
$cityMarkers = [];
foreach ($by_city as $r) {
    if ($r['lat'] !== null && $r['lng'] !== null && count($cityMarkers) < 300) {
        $cityMarkers[] = [
            'latLng' => [$r['lat'], $r['lng']],
            'name' => $r['city'] . ($r['region'] ? ', ' . $r['region'] : '') . ', ' . $r['country'],
            'cc' => $r['country_code'],
            'sessions' => $r['sessions'],
        ];
    }
}
$indiaStates = [];
$unmappedStates = [];
foreach ($by_region as $r) {
    if ($r['country_code'] !== 'IN') {
        continue;
    }
    if ($r['map_code']) {
        $indiaStates[$r['map_code']] = ($indiaStates[$r['map_code']] ?? 0) + $r['sessions'];
    } else {
        $unmappedStates[] = $r['region'];
    }
}
$hasIndia = false;
foreach ($by_country as $r) {
    if ($r['country_code'] === 'IN') {
        $hasIndia = true;
    }
}
$chartData = [
    'daily' => $daily,
    'prevDaily' => $prev_daily,
    'countries' => array_column($by_country, 'sessions', 'country_code'),
    'countryNames' => array_column($by_country, 'country', 'country_code'),
    'indiaStates' => $indiaStates,
    'markers' => $cityMarkers,
    'countryFilter' => $country_filter,
    'filterUrl' => $baseQuery(['country' => '__CC__']),
    'defaultMap' => ($country_filter === 'IN' || (!$country_filter && $hasIndia && count($by_country) === 1)) ? 'india' : 'world',
];
?>
<link href="<?= base_url('assets/admin/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') ?>" rel="stylesheet" type="text/css" />
<style>
    /* Not Bootstrap .mb-0: header_link.php redefines it as a floating 60px circle. */
    .vl-mb0 { margin-bottom: 0 !important; }
    .vl-sub { color: #74788d; }
    .vl-kpi .vl-kpi-label { color: #495057; font-weight: 500; border-bottom: 1px dotted #adb5bd; cursor: help; }
    .vl-kpi .vl-kpi-value { font-size: 1.6rem; font-weight: 600; color: #212529; line-height: 1.2; }
    .vl-chg { display: inline-flex; align-items: center; gap: 1px; font-size: .8rem; font-weight: 600; padding: 1px 6px; border-radius: 10px; white-space: nowrap; }
    .vl-chg i { font-size: 1rem; }
    .vl-up { color: #0f7a4d; background: #e3f5ec; }
    .vl-down { color: #b42318; background: #fdecea; }
    .vl-flat { color: #6c757d; background: #f1f3f5; }
    .vl-spark { margin: 4px -6px -10px; }
    .vl-bars { list-style: none; padding: 0; margin: 0; }
    .vl-bars li { padding: 7px 0; }
    .vl-bars li + li { border-top: 1px solid #f1f3f5; }
    .vl-bar-row { display: flex; align-items: center; gap: 10px; font-size: .875rem; }
    .vl-bar-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #343a40; }
    .vl-bar-name a { color: #343a40; }
    .vl-bar-name a:hover { color: #2a78d6; text-decoration: underline; }
    .vl-bar-val { font-weight: 600; color: #212529; font-variant-numeric: tabular-nums; }
    .vl-bar-track { height: 6px; margin-top: 5px; background: #eef2f7; border-radius: 3px; overflow: hidden; }
    .vl-bar-fill { height: 100%; background: #2a78d6; border-radius: 0 3px 3px 0; }
    .vl-map { height: 440px; position: relative; }
    .vl-legend { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; font-size: .8rem; color: #495057; }
    .vl-ramp { display: inline-block; width: 110px; height: 8px; border-radius: 4px; background: linear-gradient(90deg, #cde2fb, #2a78d6, #104281); vertical-align: middle; margin: 0 6px; }
    .vl-dot { display: inline-block; width: 11px; height: 11px; border-radius: 50%; background: #d95926; border: 2px solid #fff; box-shadow: 0 0 0 1px #d95926; vertical-align: middle; margin-right: 4px; }
    .jvectormap-tip { background: #212529; border: 0; border-radius: 6px; padding: 6px 9px; font-family: inherit; font-size: 12px; line-height: 1.4; }
    .jvectormap-zoomin, .jvectormap-zoomout { background: #fff; color: #495057; border: 1px solid #dee2e6; width: 22px; height: 22px; line-height: 18px; padding: 0; text-align: center; border-radius: 4px; }
    .jvectormap-zoomout { top: 36px; }
    .vl-table td, .vl-table th { white-space: nowrap; }
    .vl-table td.num, .vl-table th.num { text-align: right; font-variant-numeric: tabular-nums; }
</style>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
                <div>
                    <h2 class="mb-1"><?= $title ?></h2>
                    <div class="vl-sub">
                        <?= $fmtDate($date_from) ?><?= $date_from !== $date_to ? ' &ndash; ' . $fmtDate($date_to) : '' ?>
                        &middot; compared to <?= $fmtDate($prev_from) ?><?= $prev_from !== $prev_to ? ' &ndash; ' . $fmtDate($prev_to) : '' ?>
                    </div>
                </div>
                <form method="get" class="d-flex flex-wrap align-items-end gap-2" id="vlRangeForm">
                    <div>
                        <select name="range" id="vlRange" class="form-select form-control">
                            <?php foreach ($presets as $key => $p) : ?>
                                <option value="<?= $key ?>" <?= $preset === $key ? 'selected' : '' ?>><?= $p[0] ?></option>
                            <?php endforeach; ?>
                            <option value="custom" <?= $preset === 'custom' ? 'selected' : '' ?>>Custom range</option>
                        </select>
                    </div>
                    <div class="vl-custom" style="<?= $preset === 'custom' ? '' : 'display:none' ?>">
                        <input type="text" class="form-control" name="date_from" value="<?= $date_from ?>" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" style="width:120px" aria-label="From date">
                    </div>
                    <div class="vl-custom" style="<?= $preset === 'custom' ? '' : 'display:none' ?>">
                        <input type="text" class="form-control" name="date_to" value="<?= $date_to ?>" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" style="width:120px" aria-label="To date">
                    </div>
                    <?php if ($country_filter) : ?>
                        <input type="hidden" name="country" value="<?= html_escape($country_filter) ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary vl-custom" style="<?= $preset === 'custom' ? '' : 'display:none' ?>">Apply</button>
                </form>
            </div>

            <?php if ($country_filter) : ?>
                <div class="alert alert-info d-flex justify-content-between align-items-center py-2">
                    <span>States, cities and city bubbles are filtered to <strong><?= html_escape($country_filter_name) ?></strong>.</span>
                    <a href="<?= $baseQuery() ?>" class="btn btn-sm btn-light">Show all countries</a>
                </div>
            <?php endif; ?>

            <!-- KPI tiles -->
            <div class="row">
                <?php foreach ($kpis as $k) : ?>
                    <div class="col-md-6 col-xl-3">
                        <div class="card vl-kpi">
                            <div class="card-body pb-2">
                                <span class="vl-kpi-label" title="<?= $k['help'] ?>"><?= $k['label'] ?></span>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <span class="vl-kpi-value"><?= number_format($totals[$k['key']]) ?></span>
                                    <?= $change($totals[$k['key']], $prev_totals[$k['key']]) ?>
                                </div>
                                <div class="vl-spark" id="vlSpark-<?= $k['key'] ?>" role="img" aria-label="<?= $k['label'] ?> per day"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Trend -->
            <div class="card">
                <div class="card-body">
                    <h5 class="vl-mb0">Sessions over time</h5>
                    <div id="vlTrend" role="img" aria-label="Sessions per day, this period and previous period"></div>
                </div>
            </div>

            <!-- Map + top countries -->
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <h5 class="vl-mb0">Sessions by location</h5>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Map">
                                    <button type="button" class="btn btn-outline-primary vl-map-btn" data-map="world">World</button>
                                    <button type="button" class="btn btn-outline-primary vl-map-btn" data-map="india">India</button>
                                </div>
                            </div>
                            <div class="vl-map" id="vlMapWorld"></div>
                            <div class="vl-map" id="vlMapIndia" style="display:none"></div>
                            <div class="vl-legend mt-2">
                                <span>Fewer<span class="vl-ramp"></span>More sessions</span>
                                <span><span class="vl-dot"></span>City &middot; bubble size = sessions</span>
                                <span class="text-muted">Click a country to see its states and cities.</span>
                            </div>
                            <?php if ($unmappedStates) : ?>
                                <p class="text-muted small vl-mb0 mt-1">No shape on the India map for <?= html_escape(implode(', ', array_unique($unmappedStates))) ?> &ndash; shown as city bubbles and in the lists.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="vl-mb0">Top countries</h5>
                                <a href="#vlAll" class="small" data-vl-tab="country">View all</a>
                            </div>
                            <?= $barList($by_country, function ($r) {
                                return html_escape($r['country'] ?: $r['country_code']);
                            }, $located, 10, $countryLink) ?>
                            <?php if ($unlocated) : ?>
                                <div class="border-top mt-2 pt-2 small text-muted">
                                    <?php foreach ($unlocated as $u) : ?>
                                        <div class="d-flex justify-content-between"><span><?= html_escape($u['reason']) ?></span><span><?= number_format($u['sessions']) ?></span></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top states + cities -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="vl-mb0">Top states / regions <small class="text-muted fw-normal">&middot; <?= $scopeLabel ?></small></h5>
                                <a href="#vlAll" class="small" data-vl-tab="region">View all</a>
                            </div>
                            <?= $barList($by_region, function ($r) use ($country_filter) {
                                return html_escape($r['region']) . ($country_filter ? '' : ' <span class="text-muted">&middot; ' . html_escape($r['country_code']) . '</span>');
                            }, $scopedTotal, 10) ?>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="vl-mb0">Top cities <small class="text-muted fw-normal">&middot; <?= $scopeLabel ?></small></h5>
                                <a href="#vlAll" class="small" data-vl-tab="city">View all</a>
                            </div>
                            <?= $barList($by_city, function ($r) {
                                return html_escape($r['city']) . ' <span class="text-muted">&middot; ' . html_escape($r['region'] ?: $r['country']) . '</span>';
                            }, $scopedTotal, 10) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Every location -->
            <div class="card" id="vlAll">
                <div class="card-body">
                    <h5>All locations</h5>
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#vlTabCountry" role="tab">Countries (<?= count($by_country) ?>)</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#vlTabRegion" role="tab">States / regions (<?= count($by_region) ?>)</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#vlTabCity" role="tab">Cities (<?= count($by_city) ?>)</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="vlTabCountry" role="tabpanel">
                            <table class="table table-sm table-hover vl-table w-100" id="vlTableCountry">
                                <thead><tr><th>Country</th><th class="num">Sessions</th><th class="num">Visitors</th><th class="num">% of sessions</th><th class="num">Previous period</th><th class="num">Change</th></tr></thead>
                                <tbody>
                                    <?php foreach ($by_country as $r) : ?>
                                        <tr>
                                            <td><a href="<?= $countryLink($r) ?>"><?= html_escape($r['country'] ?: $r['country_code']) ?></a></td>
                                            <td class="num" data-order="<?= $r['sessions'] ?>"><?= number_format($r['sessions']) ?></td>
                                            <td class="num" data-order="<?= $r['visitors'] ?>"><?= number_format($r['visitors']) ?></td>
                                            <td class="num" data-order="<?= $pct($r['sessions'], $located) ?>"><?= $pct($r['sessions'], $located) ?>%</td>
                                            <td class="num" data-order="<?= $r['prev_sessions'] ?>"><?= number_format($r['prev_sessions']) ?></td>
                                            <td class="num" data-order="<?= $r['sessions'] - $r['prev_sessions'] ?>"><?= $change($r['sessions'], $r['prev_sessions']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="vlTabRegion" role="tabpanel">
                            <table class="table table-sm table-hover vl-table w-100" id="vlTableRegion">
                                <thead><tr><th>State / region</th><th>Country</th><th class="num">Sessions</th><th class="num">Visitors</th><th class="num">% of sessions</th><th class="num">Previous period</th><th class="num">Change</th></tr></thead>
                                <tbody>
                                    <?php foreach ($by_region as $r) : ?>
                                        <tr>
                                            <td><?= html_escape($r['region']) ?></td>
                                            <td><?= html_escape($r['country']) ?></td>
                                            <td class="num" data-order="<?= $r['sessions'] ?>"><?= number_format($r['sessions']) ?></td>
                                            <td class="num" data-order="<?= $r['visitors'] ?>"><?= number_format($r['visitors']) ?></td>
                                            <td class="num" data-order="<?= $pct($r['sessions'], $scopedTotal) ?>"><?= $pct($r['sessions'], $scopedTotal) ?>%</td>
                                            <td class="num" data-order="<?= $r['prev_sessions'] ?>"><?= number_format($r['prev_sessions']) ?></td>
                                            <td class="num" data-order="<?= $r['sessions'] - $r['prev_sessions'] ?>"><?= $change($r['sessions'], $r['prev_sessions']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="vlTabCity" role="tabpanel">
                            <table class="table table-sm table-hover vl-table w-100" id="vlTableCity">
                                <thead><tr><th>City</th><th>State / region</th><th>Country</th><th class="num">Sessions</th><th class="num">Visitors</th><th class="num">% of sessions</th><th class="num">Previous period</th><th class="num">Change</th></tr></thead>
                                <tbody>
                                    <?php foreach ($by_city as $r) : ?>
                                        <tr>
                                            <td><?= html_escape($r['city']) ?></td>
                                            <td><?= html_escape($r['region']) ?></td>
                                            <td><?= html_escape($r['country']) ?></td>
                                            <td class="num" data-order="<?= $r['sessions'] ?>"><?= number_format($r['sessions']) ?></td>
                                            <td class="num" data-order="<?= $r['visitors'] ?>"><?= number_format($r['visitors']) ?></td>
                                            <td class="num" data-order="<?= $pct($r['sessions'], $scopedTotal) ?>"><?= $pct($r['sessions'], $scopedTotal) ?>%</td>
                                            <td class="num" data-order="<?= $r['prev_sessions'] ?>"><?= number_format($r['prev_sessions']) ?></td>
                                            <td class="num" data-order="<?= $r['sessions'] - $r['prev_sessions'] ?>"><?= $change($r['sessions'], $r['prev_sessions']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-muted small">
                Location is estimated from the visitor's IP address, so cities are approximate (mobile networks often show the carrier's hub city).
                A session is one browser on one day. <a href="https://db-ip.com" target="_blank" rel="noopener">IP Geolocation by DB-IP</a>.
            </p>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/admin/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') ?>"></script>
<script src="<?= base_url('assets/admin/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js') ?>"></script>
<script src="<?= base_url('assets/admin/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-in-mill-en.js') ?>"></script>
<script>
    window.VL = <?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    // ApexCharts and DataTables load in the footer, after this script.
    window.addEventListener('load', function () {
        var BLUE = '#2a78d6', ORANGE = '#d95926', PREV = '#8a94a6', INK = '#495057', GRID = '#eef2f7';
        var fmt = function (n) { return Number(n).toLocaleString('en-IN'); };
        var dayLabel = function (d) { return new Date(d + 'T00:00:00').toLocaleDateString('en-IN', { day: 'numeric', month: 'short' }); };
        var dates = VL.daily.map(function (r) { return r.date; });

        // Date preset picker: presets apply at once, custom shows the inputs.
        $('#vlRange').on('change', function () {
            if (this.value === 'custom') {
                $('.vl-custom').show();
            } else {
                $('#vlRangeForm').find('[name=date_from],[name=date_to]').prop('disabled', true);
                $('#vlRangeForm').submit();
            }
        });

        // KPI sparklines: one series each, crosshair tooltip with the day.
        ['sessions', 'visitors', 'countries', 'cities'].forEach(function (key) {
            new ApexCharts(document.getElementById('vlSpark-' + key), {
                chart: { type: 'area', height: 56, sparkline: { enabled: true }, animations: { enabled: false } },
                series: [{ name: key.charAt(0).toUpperCase() + key.slice(1), data: VL.daily.map(function (r) { return r[key]; }) }],
                labels: dates.map(dayLabel),
                stroke: { width: 2, curve: 'straight' },
                colors: [BLUE],
                fill: { type: 'solid', opacity: 0.12 },
                tooltip: { x: { show: true }, y: { formatter: fmt }, marker: { show: false } }
            }).render();
        });

        // Sessions over time vs previous period (same hue family: previous is grey dashed).
        new ApexCharts(document.getElementById('vlTrend'), {
            chart: { type: 'line', height: 280, toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: false }, fontFamily: 'inherit' },
            series: [
                { name: 'This period', data: VL.daily.map(function (r) { return r.sessions; }) },
                { name: 'Previous period', data: VL.prevDaily.map(function (r) { return r.sessions; }) }
            ],
            labels: dates.map(dayLabel),
            colors: [BLUE, PREV],
            stroke: { width: [2, 2], dashArray: [0, 5], curve: 'straight' },
            markers: { size: 0, hover: { size: 5 } },
            grid: { borderColor: GRID, strokeDashArray: 0, xaxis: { lines: { show: false } } },
            xaxis: { tickAmount: Math.min(dates.length, 10), labels: { rotate: 0, style: { colors: INK } }, axisBorder: { show: false }, axisTicks: { show: false }, tooltip: { enabled: false } },
            yaxis: { min: 0, forceNiceScale: true, labels: { formatter: function (v) { return fmt(Math.round(v)); }, style: { colors: INK } } },
            legend: { position: 'top', horizontalAlign: 'left', fontSize: '13px', markers: { width: 14, height: 3, radius: 2 } },
            tooltip: {
                shared: true, intersect: false,
                x: { formatter: function (v, o) { var i = o.dataPointIndex; return dayLabel(VL.daily[i].date) + '  vs  ' + dayLabel(VL.prevDaily[i].date); } },
                y: { formatter: function (v) { return fmt(v) + ' sessions'; } }
            }
        }).render();

        // Maps: blue light->dark by sessions, orange city bubbles sized by sessions.
        var maps = {};
        function buildMap(which) {
            if (maps[which]) { return; }
            var india = which === 'india';
            var values = india ? VL.indiaStates : VL.countries;
            var markers = VL.markers.filter(function (m) { return !india || m.cc === 'IN'; });
            var sizes = markers.map(function (m) { return m.sessions; });
            var el = $(india ? '#vlMapIndia' : '#vlMapWorld');
            el.vectorMap({
                map: india ? 'in_mill_en' : 'world_mill_en',
                backgroundColor: 'transparent',
                zoomOnScroll: false,
                regionStyle: {
                    initial: { fill: '#e9ecef', 'fill-opacity': 1, stroke: '#ffffff', 'stroke-width': 0.6, 'stroke-opacity': 1 },
                    hover: { 'fill-opacity': 0.85, cursor: india ? 'default' : 'pointer' }
                },
                markerStyle: {
                    initial: { fill: ORANGE, 'fill-opacity': 0.85, stroke: '#ffffff', 'stroke-width': 1.5, 'stroke-opacity': 1, r: 5 },
                    hover: { 'fill-opacity': 1, stroke: '#212529', 'stroke-width': 1.5, cursor: 'default' }
                },
                markers: markers.map(function (m) { return { latLng: m.latLng, name: m.name }; }),
                series: {
                    regions: [{ values: values, scale: ['#cde2fb', '#104281'], normalizeFunction: 'polynomial', attribute: 'fill' }],
                    markers: markers.length ? [{ attribute: 'r', scale: [4, 14], values: sizes, normalizeFunction: 'polynomial' }] : []
                },
                onRegionLabelShow: function (e, label, code) {
                    var n = values[code] || 0;
                    label.html('<strong>' + label.html() + '</strong><br>' + fmt(n) + ' session' + (n === 1 ? '' : 's'));
                },
                onMarkerLabelShow: function (e, label, i) {
                    label.html('<strong>' + $('<div>').text(markers[i].name).html() + '</strong><br>' + fmt(markers[i].sessions) + ' session' + (markers[i].sessions === 1 ? '' : 's'));
                },
                onRegionClick: function (e, code) {
                    if (!india && values[code]) {
                        window.location = VL.filterUrl.replace('__CC__', encodeURIComponent(code));
                    }
                }
            });
            maps[which] = el.vectorMap('get', 'mapObject');
        }
        function showMap(which) {
            $('#vlMapWorld').toggle(which === 'world');
            $('#vlMapIndia').toggle(which === 'india');
            $('.vl-map-btn').removeClass('active').filter('[data-map="' + which + '"]').addClass('active');
            buildMap(which); // built on first show - a hidden container has no size
        }
        $('.vl-map-btn').on('click', function () { showMap($(this).data('map')); });
        showMap(VL.defaultMap);

        // Full tables: search, sort, paging, CSV export.
        var dtOpts = function (col) {
            return {
                order: [[col, 'desc']], pageLength: 25, lengthMenu: [10, 25, 50, 100, 500],
                dom: "<'d-flex flex-wrap justify-content-between gap-2 mb-2'Bf>t<'d-flex flex-wrap justify-content-between mt-2'ip>",
                // Explicit button class: the default btn-secondary + theme btn-light renders text in the background colour.
                buttons: {
                    dom: { button: { className: 'btn btn-sm btn-outline-primary' } },
                    buttons: [{ extend: 'csv', text: '<i class="bx bx-download"></i> Export CSV', title: 'visitor-locations' }]
                },
                language: { search: '', searchPlaceholder: 'Search locations' }
            };
        };
        var tables = {
            country: $('#vlTableCountry').DataTable(dtOpts(1)),
            region: $('#vlTableRegion').DataTable(dtOpts(2)),
            city: $('#vlTableCity').DataTable(dtOpts(3))
        };
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
        $('[data-vl-tab]').on('click', function () {
            var tab = { country: '#vlTabCountry', region: '#vlTabRegion', city: '#vlTabCity' }[$(this).data('vl-tab')];
            var link = document.querySelector('a[href="' + tab + '"]');
            if (link && window.bootstrap) { bootstrap.Tab.getOrCreateInstance(link).show(); }
        });
    });
</script>

<?php $this->load->view('admin/template/footer'); ?>
