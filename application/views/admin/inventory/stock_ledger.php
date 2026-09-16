<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <a href="<?= base_url('stockAdjust?id=' . $this->input->get('id')) ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Adjust Stock</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Change Type</th>
                                        <th>Delta</th>
                                        <th>Balance After</th>
                                        <th>Reference</th>
                                        <th>Changed By</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($entries)) : ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No stock movements recorded yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php foreach ($entries as $entry) : ?>
                                        <tr>
                                            <td><?= dateConvertToView($entry['create_date'], 3) ?></td>
                                            <td><?= ucwords(str_replace('_', ' ', $entry['change_type'])) ?></td>
                                            <td class="<?= $entry['delta'] > 0 ? 'text-success' : ($entry['delta'] < 0 ? 'text-danger' : '') ?>">
                                                <?= $entry['delta'] > 0 ? '+' : '' ?><?= $entry['delta'] ?>
                                            </td>
                                            <td><?= $entry['balance_after'] ?></td>
                                            <td><?= $entry['reference_type'] ? ucwords(str_replace('_', ' ', $entry['reference_type'])) . ' #' . $entry['reference_id'] : '-' ?></td>
                                            <td>
                                                <?php
                                                if ($entry['changed_by_type'] == ACTOR_TYPE_ADMIN) {
                                                    echo 'Admin #' . $entry['changed_by_id'];
                                                } elseif ($entry['changed_by_type'] == ACTOR_TYPE_VENDOR) {
                                                    echo 'Vendor #' . $entry['changed_by_id'];
                                                } else {
                                                    echo 'System (order flow)';
                                                }
                                                ?>
                                            </td>
                                            <td><?= $entry['note'] ?: '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
