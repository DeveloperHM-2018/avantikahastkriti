import { submitForm } from "./formHandlers.js";
import { chunkLoader } from "./loaders.js";
import { initOtpForm } from "./otpFlow.js";

const profileForm = document.querySelector("#profile-form");


profileForm.addEventListener("submit", async (e) => {
    chunkLoader("add", `#${e.target.id}`);
    const action = `Web/profile_update/`;
    const redirectUrl = e.target.getAttribute("data-redirect") || "profile";
    await submitForm(e, action).then((isSuccess) => {
        if (isSuccess) {
            // redirect(redirectUrl);
        } else {
            console.log("Login failed!");
        }
    });
    chunkLoader("remove", `#${e.target.id}`);
});

const changePasswordForm = document.querySelector("#change-password-form");

changePasswordForm?.addEventListener("submit", async (e) => {
    chunkLoader("add", `#${e.target.id}`);
    const action = `Auth/changePassword/`;
    const isSuccess = await submitForm(e, action);
    chunkLoader("remove", `#${e.target.id}`);
    if (isSuccess && !e.target.querySelector('[name="current_password"]')) {
        // A password was just set for the first time - reload so the form
        // asks for the current password from now on.
        window.location.reload();
    }
});

// === Add / change the mobile number or email (verified by OTP) ===
const contactForm = document.querySelector("#contact-form");
const contactFormLabel = contactForm.querySelector("[data-contact-form-label]");

const contactFlow = initOtpForm(contactForm, {
    action: `Auth/updateContact/`,
    verifyLabel: "Verify & Save",
    onSuccess: (data) => {
        document.querySelector(`[data-contact-value="${data.type}"]`).textContent = data.value;
        document.querySelector(`[data-contact-edit="${data.type}"]`).textContent = "Change";
        contactForm.querySelector('[name="identifier"]').value = "";
    },
});

document.querySelectorAll("[data-contact-edit]").forEach((button) => {
    button.addEventListener("click", () => {
        contactFlow.reset();
        contactFlow.setChannel(button.dataset.contactEdit);
        contactForm.querySelector(".formMsg").replaceChildren();
        contactFormLabel.textContent = `New ${button.dataset.contactLabel}`;
        contactForm.hidden = false;
        contactForm.querySelector('[name="identifier"]').focus();
    });
});

contactForm.querySelector("[data-contact-cancel]").addEventListener("click", () => {
    contactFlow.reset();
    contactForm.hidden = true;
});
