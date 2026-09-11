<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<div class="breadcrumb-block style-img">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center"><?= str_replace('-', ' ', $title) ?></div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize"><?= str_replace('-', ' ', $title) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="faqs-block md:py-20 py-10">
    <div class="container">
        <?= $details['description'] ?>
    </div>
</div>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>