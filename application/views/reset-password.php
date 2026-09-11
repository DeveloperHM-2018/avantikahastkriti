<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<style>
    .valid{
    color: green;
}
.invalid{
    color: crimson;
}
</style>

<div class="login-block md:py-20 py-10">
    <div class="container">
        <div class="content-main flex gap-y-8 max-md:flex-col justify-center">
            <div class=" md:w-1/2 w-full lg:pr-[60px] md:pr-[40px]">
                <div class="heading4">Reset Password</div>
                <form class="md:mt-7 mt-4" id="reset-password-form" method="POST">
                    <div class="formMsg"></div>
                    <input type="hidden" name="email" id="email" value="<?= sessionId('forgotContact') ?>">
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
                    <div class="block-button md:mt-7 mt-4">
                        <button class="button-main">Reset</button>
                    </div>
                </form>
            </div>


        </div>
    </div>
</div>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>