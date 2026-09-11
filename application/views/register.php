<?php
include('includes/header-link.php');
include('includes/header.php');
?>



<div class="breadcrumb-block style-shared">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center">Register</div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize">Register</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="register-block md:py-20 py-10">
    <div class="container">
        <div class="content-main flex gap-y-8 max-md:flex-col">
            <div class="left md:w-1/2 w-full lg:pr-[60px] md:pr-[40px] md:border-r border-line">
                <div class="heading4">Register</div>
                <form class="md:mt-7 mt-4" id="registration-form" method="POST">
                    <div class="formMsg">
                    </div>
                    <div class="name">
                        <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="name" name="name" type="text" placeholder="Name *" />
                        <div class="error-message text-red text-sm mt-1"></div>
                    </div>
                    <div class="email mt-5">
                        <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="email" name="email" type="text" placeholder="Email address *" />
                        <div class="error-message text-red text-sm mt-1"></div>
                    </div>
                    <div class="number mt-5">
                        <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="number" name="number" type="tel" placeholder="Mobile Number (optional)" />
                        <div class="error-message text-red text-sm mt-1"></div>
                    </div>
                    <div class="pass mt-5 ">
                        <div class="relative text-right">
                            <a href="javascript: void(0);" class="text-black hover:underline pl-1" onclick="showPassword('password', 'loginEye')"><i class="ph ph-eye password-eye-btn relative text-black hover:underline" id="loginEye"></i>Show</a>
                            <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="password" name="password" type="password" placeholder="Password *" />
                        </div>
                        <div class="error-message text-red text-sm mt-1"></div>
                        <div id="passwordCriteria" class="flex flex-wrap gap-2" style="display: none;">
                            <h2 class="w-full">Password Must Contain:</h2>
                            <label><span id="minLengthIcon" class=""></span> <span class="checkbox-label" id="minLengthLabel">At least 6 characters</span></label><br>
                            <label><span id="uppercaseIcon" class=""></span> <span class="checkbox-label" id="uppercaseLabel">One uppercase letter</span></label><br>
                            <label><span id="lowercaseIcon" class=""></span> <span class="checkbox-label" id="lowercaseLabel">One lowercase letter</span></label><br>
                            <label><span id="digitIcon" class=""></span> <span class="checkbox-label" id="digitLabel">One number</span></label><br>
                            <label><span id="specialCharIcon" class=""></span> <span class="checkbox-label" id="specialCharLabel">One special character (e.g., @, $, !, %, *, ?, &)</span></label>
                        </div>

                    </div>
                    <div class="confirm-pass mt-5">
                        <div class="relative text-right">

                            <a href="javascript: void(0);" class="text-black hover:underline pl-1" onclick="showPassword('confirmPassword', 'confirmEye')"><i class="ph ph-eye password-eye-btn relative text-black hover:underline" id="confirmEye"></i>Show</a>
                            <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="confirmPassword" name="confirm-password" type="password" placeholder="Confirm Password *" />
                        </div>
                        <div class="error-message text-red text-sm mt-1"></div>
                    </div>
                    <div class="flex items-center mt-5">
                        <label for="remember" class="block-input">
                            <input type="checkbox" name="terms_condition" id="remember" />
                            <i class="ph-fill ph-check-square icon-checkbox text-2xl"></i>
                        </label>
                        <label for="remember" class="pl-2 cursor-pointer text-secondary2">I agree to the
                            <a href="#!" class="text-black hover:underline pl-1">Terms of User</a>
                        </label>
                    </div>
                    <div class="block-button md:mt-7 mt-4">
                        <button class="button-main" id="regiserButton">Register</button>
                    </div>
                </form>
            </div>
            <div class="right md:w-1/2 w-full lg:pl-[60px] md:pl-[40px] flex items-center">
                <div class="text-content">
                    <div class="heading4">Already have an account?</div>
                    <div class="mt-2 text-secondary">Welcome back. Sign in to access your personalized experience, saved preferences, and more. We're thrilled to have you with us again!</div>
                    <div class="block-button md:mt-7 mt-4">
                        <a href="<?= base_url('login') ?>" class="button-main">Login</a>
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