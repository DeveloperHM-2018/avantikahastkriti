import { submitForm } from "./formHandlers.js";
import { chunkLoader } from "./loaders.js";
import { initOtpForm } from "./otpFlow.js";

const loginForm = document.querySelector("#login-form");

if (loginForm) {
	const redirectUrl = loginForm.getAttribute("data-redirect") || "profile";
	const passwordStep = loginForm.querySelector(".password-step");
	const passwordInput = passwordStep.querySelector('[name="password"]');
	const modeButton = loginForm.querySelector("[data-login-mode]");
	const submitButton = loginForm.querySelector(".button-main");
	const channelHint = loginForm.querySelector("[data-channel-hint]");
	// OTP is the default way in; the password is the alternative.
	let mode = "otp";

	const otpFlow = initOtpForm(loginForm, {
		action: `Auth/otpLogin/`,
		verifyLabel: "Verify & Login",
		isActive: () => mode === "otp",
		onSuccess: () => redirect(redirectUrl),
	});

	modeButton.addEventListener("click", () => {
		mode = mode === "otp" ? "password" : "otp";
		const usePassword = mode === "password";
		otpFlow.reset();
		loginForm.querySelector(".formMsg").replaceChildren();
		passwordStep.hidden = !usePassword;
		passwordInput.disabled = !usePassword;
		channelHint.hidden = usePassword;
		submitButton.textContent = usePassword ? "Login" : "Get OTP";
		modeButton.textContent = usePassword ? "Login with OTP instead" : "Login with password instead";
	});

	loginForm.addEventListener("submit", async (e) => {
		if (mode !== "password") return;
		chunkLoader("add", `#${e.target.id}`);
		const action = `Auth/login/`;
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
