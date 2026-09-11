<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<div class="breadcrumb-block style-img">
    <div class="breadcrumb-main bg-linear overflow-hidden">
         <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center">FAQs</div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize">FAQs</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="faqs-block md:py-20 py-10">
    <div class="container">
        <div class="flex max-md:flex-wrap justify-between gap-y-8">
           

            <div class="right list-question md:w-2/3 mx-auto">
                <div class="tab-question flex flex-col gap-5 active" data-item="how to buy">
                    <?php foreach ($faqs as $faq): ?>
                        <div class="question-item px-7 py-5 rounded-[20px] overflow-hidden border border-line cursor-pointer">
                            <div class="heading flex items-center justify-between gap-6">
                                <div class="heading6"><?= $faq['question'] ?>
                                </div>
                                <i class="ph ph-caret-right text-2xl"></i>
                            </div>
                            <div class="content body1 text-secondary"><?= $faq['answer'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>