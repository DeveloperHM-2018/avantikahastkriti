<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<div class="breadcrumb-block style-shared">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center">Login</div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize">Login</div>
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
                <div class="heading4">Login</div>
                <div class="mt-2 text-secondary">Use your mobile number or email - we'll send you a one-time code, no password needed.</div>
                <form class="auth-form md:mt-7 mt-4" id="login-form" method="POST">
                    <div class="formMsg"></div>
                    <?php include('includes/auth-identifier.php'); ?>
                    <?php include('includes/auth-otp.php'); ?>
                    <!-- Password login: the alternative to OTP, switched on from login.js -->
                    <div class="password-step" hidden>
                        <div class="pass mt-5 relative">
                            <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="password" name="password" type="password" autocomplete="current-password" placeholder="Password *" disabled />
                            <i class="ph ph-eye password-eye-btn" id="loginEye" onclick="showPassword('password', 'loginEye')"></i>
                        </div>
                        <div class="flex items-center justify-end mt-5">
                            <a href="<?= base_url('forgot-password') ?>" class="font-semibold hover:underline">Forgot Your Password? </a>
                        </div>
                    </div>
                    <div class="block-button md:mt-7 mt-4">
                        <button class="button-main">Get OTP</button>
                    </div>
                    <div class="mt-5">
                        <button type="button" class="auth-link" data-login-mode>Login with password instead</button>
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