import { submitForm } from "./formHandlers.js";
import { chunkLoader } from "./loaders.js";

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
    await submitForm(e, action).then((isSuccess) => {
        if (!isSuccess) {
            console.log("Change password failed!");
        }
    });
    chunkLoader("remove", `#${e.target.id}`);
});
