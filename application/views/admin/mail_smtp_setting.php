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
                <div class="col-8 offset-2">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <h5>SMTP Configuration</h5>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="col-form-label">SMTP Host *</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="smtp_host" value="<?= $smtp_host ?>" placeholder="e.g. smtp.gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="col-form-label">SMTP Port *</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="smtp_port" value="<?= $smtp_port ?>" placeholder="e.g. 465">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">SMTP Username *</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="smtp_user" value="<?= $smtp_user ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">SMTP Password</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="password" name="smtp_pass" value="" placeholder="Leave blank to keep unchanged">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">Encryption</label>
                                        <div class="col-md-12">
                                            <select class="form-control" name="smtp_crypto">
                                                <option value="">None</option>
                                                <option value="tls" <?= $smtp_crypto == 'tls' ? 'selected' : '' ?>>TLS</option>
                                                <option value="ssl" <?= $smtp_crypto == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr class="mt-4">
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">From Email *</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="from_email" value="<?= $from_email ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3">
                                        <label class="col-form-label">From Name</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="from_name" value="<?= $from_name ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-3">
                                        <label class="col-form-label">Admin Notification Email *</label>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="notify_email" value="<?= $notify_email ?>" placeholder="Receives new-registration / new-order alerts">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-md">Save</button>
                                </div>
                            </form>

                            <hr class="mt-4">
                            <h5>Send Test Mail</h5>
                            <div class="row">
                                <div class="col-lg-8">
                                    <input class="form-control" type="text" id="testMailTo" placeholder="Enter email address">
                                </div>
                                <div class="col-lg-4">
                                    <button type="button" id="sendTestMailBtn" class="btn btn-success w-100">Send Test Mail</button>
                                </div>
                            </div>
                            <div id="testMailResult" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('sendTestMailBtn').addEventListener('click', function() {
        var toEmail = document.getElementById('testMailTo').value;
        var resultDiv = document.getElementById('testMailResult');
        var btn = this;
        btn.disabled = true;
        btn.innerText = 'Sending...';
        fetch('<?= base_url('testMail') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'to_email=' + encodeURIComponent(toEmail)
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                resultDiv.innerHTML = '<div class="alert ' + (data.success ? 'alert-success' : 'alert-danger') + '">' + data.message + '</div>';
            })
            .catch(function() {
                resultDiv.innerHTML = '<div class="alert alert-danger">Something went wrong.</div>';
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerText = 'Send Test Mail';
            });
    });
</script>

<?php $this->load->view('admin/template/footer'); ?>
