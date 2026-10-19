import { initOtpForm } from "./otpFlow.js";

const forgotPassForm = document.querySelector("#forgot-password-form");

// Verify the account's mobile number or email by OTP, then move on to the
// reset page - which only opens once the server has seen that OTP.
initOtpForm(forgotPassForm, {
    action: `Auth/forgotPassword/`,
    verifyLabel: "Verify OTP",
    onSuccess: () => redirect(forgotPassForm.getAttribute("data-redirect") || "reset-password"),
});
