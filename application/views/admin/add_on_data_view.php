<?php $this->load->view('admin/template/header', $title); ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <a href="addOnData" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Go Back</a>
            <div class="row mt-2 card">
                <div class="col-xs-12">
                    <div class="box box-primary box-solid borderless">
                        <div class="box-header cursor">
                            <h3 class="box-title"><?= $data['title'] ?></h3>
                        </div>
                        <div class="box-body imgset2">
                            <div class="row">
                                <div class="col-lg-12">
                                    <?= $data['description'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/template/footer'); ?>