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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Configuration</h5>
                            <table class="table table-bordered w-100">
                                <tbody>
                                    <tr>
                                        <th style="width: 260px;">API URL</th>
                                        <td><?= htmlspecialchars($api_url) ?></td>
                                    </tr>
                                    <tr>
                                        <th>API Key</th>
                                        <td><?= $api_key != '' ? htmlspecialchars($api_key) . ' <span class="badge bg-success">Set</span>' : '<span class="badge bg-danger">Not set</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Phone Number ID</th>
                                        <td><?= $phone_number_id != '' ? htmlspecialchars($phone_number_id) . ' <span class="badge bg-success">Set</span>' : '<span class="badge bg-danger">Not set</span> No WhatsApp message is sent until WHATSAPP_PHONE_NUMBER_ID is filled in config/constants.php.' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Admin Alert Number</th>
                                        <td><?= $admin_number != '' ? htmlspecialchars($admin_number) . ' <span class="badge bg-success">Set</span>' : '<span class="badge bg-warning">Not set</span> Admin alerts are not sent until WHATSAPP_ADMIN_NUMBER is filled in config/constants.php.' ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" id="whatsappPingBtn" class="btn btn-primary w-md">Check API Connection</button>
                            <div id="whatsappPingResult" class="mt-3"></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Send Test Templates</h5>
                            <p class="text-muted">Every selected template is sent to this number as a real WhatsApp message, filled with sample values. The reply shown for each one is exactly what the API returned.</p>
                            <div class="row">
                                <div class="col-lg-4">
                                    <input class="form-control" type="text" id="whatsappTestTo" value="<?= htmlspecialchars($admin_number) ?>" placeholder="Mobile number to send to">
                                </div>
                                <div class="col-lg-3">
                                    <button type="button" id="whatsappSendBtn" class="btn btn-success w-100">Send Selected Templates</button>
                                </div>
                            </div>
                            <div id="whatsappSendSummary" class="mt-3"></div>
                            <table class="table table-bordered w-100 mt-3">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="whatsappCheckAll" checked></th>
                                        <th>Event</th>
                                        <th>Template</th>
                                        <th>Variables</th>
                                        <th style="width: 110px;">Result</th>
                                        <th style="width: 40%;">API Response</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($templates as $eventKey => $template) { ?>
                                        <tr data-event="<?= htmlspecialchars($eventKey) ?>">
                                            <td><input type="checkbox" class="whatsapp-check" checked></td>
                                            <td><?= ucwords(str_replace('_', ' ', $eventKey)) ?></td>
                                            <td><?= htmlspecialchars($template['name']) ?></td>
                                            <td><?= htmlspecialchars(implode(', ', $template['params'])) ?></td>
                                            <td class="whatsapp-status"></td>
                                            <td class="whatsapp-response"></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Shows a raw API reply as-is, indented when it is JSON.
    function whatsappPre(text) {
        var pre = document.createElement('pre');
        pre.className = '';
        pre.style.whiteSpace = 'pre-wrap';
        pre.style.wordBreak = 'break-all';
        try {
            text = JSON.stringify(JSON.parse(text), null, 2);
        } catch (e) {}
        pre.textContent = text;
        return pre;
    }

    function whatsappBadge(type, text) {
        return '<span class="badge bg-' + type + '">' + text + '</span>';
    }

    function whatsappPost(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body
        }).then(function(res) {
            return res.json();
        });
    }

    // Fills one table row (or the ping box) from a result of whatsappTestSend / whatsappTestPing.
    function whatsappShowResult(data, statusEl, responseEl) {
        responseEl.innerHTML = '';
        if (data.http_code === null) {
            statusEl.innerHTML = whatsappBadge('warning', 'Not sent');
            responseEl.textContent = data.skipped;
            return;
        }
        statusEl.innerHTML = whatsappBadge(data.success ? 'success' : 'danger', (data.success ? 'Accepted' : 'Failed') + ' - HTTP ' + data.http_code);
        responseEl.appendChild(whatsappPre(data.curl_error ? 'Connection error: ' + data.curl_error : (data.response || '(empty response)')));
        if (data.request) {
            var details = document.createElement('details');
            details.className = 'mt-2';
            details.innerHTML = '<summary>Request sent</summary>';
            details.appendChild(whatsappPre(JSON.stringify(data.request)));
            responseEl.appendChild(details);
        }
    }

    document.getElementById('whatsappPingBtn').addEventListener('click', function() {
        var btn = this;
        var resultDiv = document.getElementById('whatsappPingResult');
        btn.disabled = true;
        btn.innerText = 'Checking...';
        resultDiv.innerHTML = '<div class="whatsapp-status mb-2"></div><div class="whatsapp-response"></div>';
        whatsappPost('<?= base_url('whatsappTestPing') ?>', '')
            .then(function(data) {
                whatsappShowResult(data, resultDiv.querySelector('.whatsapp-status'), resultDiv.querySelector('.whatsapp-response'));
            })
            .catch(function() {
                resultDiv.innerHTML = '<div class="alert alert-danger">Something went wrong.</div>';
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerText = 'Check API Connection';
            });
    });

    document.getElementById('whatsappCheckAll').addEventListener('change', function() {
        var checked = this.checked;
        document.querySelectorAll('.whatsapp-check').forEach(function(box) {
            box.checked = checked;
        });
    });

    document.getElementById('whatsappSendBtn').addEventListener('click', function() {
        var btn = this;
        var to = document.getElementById('whatsappTestTo').value.trim();
        var summary = document.getElementById('whatsappSendSummary');
        var rows = [];
        document.querySelectorAll('.whatsapp-check:checked').forEach(function(box) {
            rows.push(box.closest('tr'));
        });
        if (to == '') {
            summary.innerHTML = '<div class="alert alert-danger">Please enter a mobile number.</div>';
            return;
        }
        if (rows.length == 0) {
            summary.innerHTML = '<div class="alert alert-danger">Please select at least one template.</div>';
            return;
        }
        if (!confirm('Send ' + rows.length + ' test WhatsApp message(s) to ' + to + '?')) {
            return;
        }
        btn.disabled = true;
        var accepted = 0;
        var done = 0;
        // One template per request, one after another, so each row fills in as
        // its reply arrives and a slow API cannot time the whole run out.
        var sendNext = function() {
            if (done == rows.length) {
                btn.disabled = false;
                btn.innerText = 'Send Selected Templates';
                summary.innerHTML = '<div class="alert ' + (accepted == rows.length ? 'alert-success' : 'alert-danger') + '">' + accepted + ' of ' + rows.length + ' accepted by the API. Accepted means the API took the message - check the phone to confirm it was delivered.</div>';
                return;
            }
            var row = rows[done];
            var statusEl = row.querySelector('.whatsapp-status');
            var responseEl = row.querySelector('.whatsapp-response');
            btn.innerText = 'Sending ' + (done + 1) + ' of ' + rows.length + '...';
            statusEl.innerHTML = whatsappBadge('secondary', 'Sending...');
            responseEl.innerHTML = '';
            whatsappPost('<?= base_url('whatsappTestSend') ?>', 'event_key=' + encodeURIComponent(row.getAttribute('data-event')) + '&to=' + encodeURIComponent(to))
                .then(function(data) {
                    if (data.success) {
                        accepted++;
                    }
                    whatsappShowResult(data, statusEl, responseEl);
                })
                .catch(function() {
                    statusEl.innerHTML = whatsappBadge('danger', 'Error');
                    responseEl.textContent = 'The test request itself failed - the server did not return a result.';
                })
                .finally(function() {
                    done++;
                    sendNext();
                });
        };
        sendNext();
    });
</script>

<?php $this->load->view('admin/template/footer'); ?>
