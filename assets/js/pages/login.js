import { submitForm } from "./formHandlers.js";
import { chunkLoader } from "./loaders.js";

const loginForm = document.querySelector("#login-form");

if (loginForm) {
	loginForm.addEventListener("submit", async (e) => {
		chunkLoader("add", `#${e.target.id}`);
		const action = `Auth/login/`;
		const redirectUrl = e.target.getAttribute("data-redirect") || "profile";
		await submitForm(e, action).then((isSuccess) => {
			if (isSuccess) {
				redirect(redirectUrl);
			} else {
				console.log("Login failed!");
			}
		});
		chunkLoader("remove", `#${e.target.id}`);
	});
}
