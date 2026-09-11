<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                        <a href="<?= base_url('returnList') ?>" class="btn btn-primary">View All Returns</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                $cards = [
                    ['label' => 'Requested', 'count' => $counts['requested'], 'color' => 'warning', 'icon' => 'bx-time-five'],
                    ['label' => 'Under Review', 'count' => $counts['under_review'], 'color' => 'info', 'icon' => 'bx-search-alt'],
                    ['label' => 'Approved', 'count' => $counts['approved'], 'color' => 'primary', 'icon' => 'bx-check-circle'],
                    ['label' => 'Rejected', 'count' => $counts['rejected'], 'color' => 'danger', 'icon' => 'bx-x-circle'],
                    ['label' => 'Pickup Scheduled', 'count' => $counts['pickup_scheduled'], 'color' => 'secondary', 'icon' => 'bx-calendar'],
                    ['label' => 'Picked Up', 'count' => $counts['picked_up'], 'color' => 'secondary', 'icon' => 'bx-package'],
                    ['label' => 'Refund Pending', 'count' => $counts['refund_pending'], 'color' => 'warning', 'icon' => 'bx-wallet'],
                    ['label' => 'Refund Completed', 'count' => $counts['refund_completed'], 'color' => 'success', 'icon' => 'bx-check-double'],
                ];
                foreach ($cards as $card):
                ?>
                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1"><?= $card['label'] ?></p>
                                        <h4 class="mb-0"><?= $card['count'] ?></h4>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-soft-<?= $card['color'] ?> text-<?= $card['color'] ?> rounded-circle fs-3">
                                            <i class="bx <?= $card['icon'] ?>"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
