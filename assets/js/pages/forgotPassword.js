import { submitForm } from "./formHandlers.js";
import { chunkLoader } from "./loaders.js";

const forgotPassForm = document.querySelector("#forgot-password-form");

forgotPassForm.addEventListener("submit", async (e) => {
    e.preventDefault(); // Prevent default form submission
    chunkLoader("add", `#${e.target.id}`);

    const action = `Auth/forgotPassword/`; // First step: Request OTP
    const redirectUrl = e.target.getAttribute("data-redirect") || "reset-password";

    const formData = new FormData(forgotPassForm);
    const email = formData.get("email");
    const otpFieldExists = document.querySelector("#otp");

    if (!otpFieldExists) {
        // First submission: Request OTP
        await submitForm(e, action).then((isSuccess) => {
            if (isSuccess) {
                // Show OTP input field dynamically
                const otpField = document.createElement("div");
                otpField.innerHTML = `
                    <div class="otp mt-4">
                        <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="otp" name="otp" type="text" placeholder="Enter OTP *" required />
                    </div>
                `;
                document.querySelector("#email").value =  email;
                document.querySelector(".otpField").appendChild(otpField);
            } else {
                console.log("Failed to send OTP!");
            }
        });
    } else {
        const verifyAction = `Auth/forgotPassword/`;
        await submitForm(e, verifyAction).then((isSuccess) => {
            if (isSuccess) {
                window.location.href = `${redirectUrl}?email=${email}`;
            } else {
                console.log("OTP verification failed!");
            }
        });
    }

    chunkLoader("remove", `#${e.target.id}`);
});
