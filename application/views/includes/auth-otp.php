<?php
// The OTP step of an otpFlow.js form - hidden (and its input disabled, so it
// is not submitted) until a code has actually been sent.
?>
<div class="otp-step mt-5" hidden>
    <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg otp-input" name="otp" type="text" inputmode="numeric" maxlength="6" autocomplete="one-time-code" placeholder="Enter 6-digit OTP *" disabled />
    <div class="error-message text-red text-sm mt-1"></div>
    <div class="flex items-center justify-between mt-3 auth-hint">
        <button type="button" class="auth-link" data-otp-change>Change</button>
        <button type="button" class="auth-link" data-otp-resend disabled>Resend OTP</button>
    </div>
</div>
