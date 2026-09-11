<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<div class="breadcrumb-block style-shared">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center">Forgot Password</div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize">Forgot Password</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="login-block md:py-20 py-10">
    <div class="container">
        <div class="content-main flex gap-y-8 max-md:flex-col">
            <div class="left md:w-1/2 w-full lg:pr-[60px] md:pr-[40px] md:border-r border-line">
                <div class="heading4">Forgot Password</div>
                <form class="md:mt-7 mt-4" id="forgot-password-form" method="POST">
                    <div class="formMsg"></div>
                    <div class="email">
                        <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="email" name="email" type="email" placeholder="Email Address *" />
                    </div>
                    <div class="otpField">
                        <!-- OTP field rendered from JavaScript -->
                    </div>
                    <div class="block-button md:mt-7 mt-4">
                        <button class="button-main">Send OTP</button>
                    </div>
                    <div class="flex items-center justify-end mt-5">
                        <a href="<?= base_url('login') ?>" class="font-semibold hover:underline">Remember password? Login here </a>
                    </div>
                </form>
            </div>

            <div class="right md:w-1/2 w-full lg:pl-[60px] md:pl-[40px] flex items-center">
                <div class="text-content">
                    <div class="heading4">New Customer</div>
                    <div class="mt-2 text-secondary">Be part of our growing family of new customers! Join us today and unlock a world of exclusive benefits, offers, and personalized experiences.</div>
                    <div class="block-button md:mt-7 mt-4">
                        <a href="<?= base_url('register') ?>" class="button-main">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>