<?php
// Mobile | Email switch + the one "identifier" input, shared by the login,
// register and forgot-password forms. assets/js/pages/otpFlow.js swaps the
// input between the two; the server accepts either in the same field.
// $identifierValue (optional) pre-fills it.
$identifierValue = isset($identifierValue) ? (string) $identifierValue : '';
?>
<div class="auth-switch">
    <button type="button" class="active" data-channel="mobile">Mobile</button>
    <button type="button" data-channel="email">Email</button>
</div>
<div class="identifier mt-5">
    <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" name="identifier" type="tel" inputmode="numeric" maxlength="10" autocomplete="tel-national" placeholder="Mobile Number *" value="<?= htmlspecialchars($identifierValue) ?>" />
    <div class="error-message text-red text-sm mt-1"></div>
    <div class="auth-hint mt-2" data-channel-hint></div>
</div>
