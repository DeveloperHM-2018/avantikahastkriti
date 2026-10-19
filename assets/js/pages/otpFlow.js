// otpFlow.js
//
// Drives every "verify by OTP" form (login, register, forgot password, profile
// mobile/email change). They all talk to a two-phase Auth endpoint: submitted
// without an OTP it sends one to the form's `identifier` (mobile or email),
// submitted again with the OTP it completes.
//
// Expected markup inside the form: includes/auth-identifier.php (or any
// [name="identifier"] input), includes/auth-otp.php, a .formMsg and a submit
// button.

import { showErrors, clearErrors } from "./formHandlers.js";
import { chunkLoader } from "./loaders.js";

const CHANNELS = {
	mobile: {
		attrs: { type: "tel", inputmode: "numeric", maxlength: "10", autocomplete: "tel-national", placeholder: "Mobile Number *" },
		hint: "We'll send a one-time code to this number on WhatsApp.",
	},
	email: {
		attrs: { type: "email", inputmode: "email", maxlength: "200", autocomplete: "email", placeholder: "Email Address *" },
		hint: "We'll email you a one-time code.",
	},
};

export function initOtpForm(form, { action, verifyLabel = "Verify OTP", isActive = () => true, onSuccess = () => {} }) {
	const identifier = form.querySelector('[name="identifier"]');
	const switchButtons = form.querySelectorAll("[data-channel]");
	const hint = form.querySelector("[data-channel-hint]");
	const otpStep = form.querySelector(".otp-step");
	const otpInput = otpStep.querySelector('[name="otp"]');
	const resendButton = otpStep.querySelector("[data-otp-resend]");
	const changeButton = otpStep.querySelector("[data-otp-change]");
	const submitButton = form.querySelector(".button-main");
	const formMsg = form.querySelector(".formMsg");
	const sendLabel = submitButton.textContent;
	let channel = "mobile";
	let resendTimer = null;

	function setChannel(next, keepValue = false) {
		channel = next;
		Object.entries(CHANNELS[channel].attrs).forEach(([name, value]) => identifier.setAttribute(name, value));
		if (!keepValue) identifier.value = "";
		if (hint) hint.textContent = CHANNELS[channel].hint;
		switchButtons.forEach((button) => button.classList.toggle("active", button.dataset.channel === channel));
	}

	function showMessage(message, isSuccess) {
		const box = document.createElement("div");
		box.className = isSuccess ? "alert-success" : "alert-false";
		const icon = document.createElement("i");
		icon.className = `icon ph ph-${isSuccess ? "check" : "warning"} text-white`;
		box.append(icon, message);
		formMsg.replaceChildren(box);
		return box;
	}

	function startResendCountdown(seconds) {
		clearInterval(resendTimer);
		let left = seconds;
		const tick = () => {
			resendButton.disabled = left > 0;
			resendButton.textContent = left > 0 ? `Resend OTP in ${left}s` : "Resend OTP";
			if (left-- <= 0) clearInterval(resendTimer);
		};
		tick();
		resendTimer = setInterval(tick, 1000);
	}

	// Back to "enter your mobile/email" - also how the caller clears the form.
	function reset() {
		clearInterval(resendTimer);
		otpStep.hidden = true;
		otpInput.disabled = true;
		otpInput.value = "";
		identifier.readOnly = false;
		switchButtons.forEach((button) => (button.disabled = false));
		submitButton.textContent = sendLabel;
	}

	function showOtpStep(resendIn) {
		otpStep.hidden = false;
		otpInput.disabled = false;
		identifier.readOnly = true;
		switchButtons.forEach((button) => (button.disabled = true));
		submitButton.textContent = verifyLabel;
		startResendCountdown(resendIn || 30);
		otpInput.focus();
	}

	async function request(withOtp) {
		clearErrors();
		const formData = new FormData(form);
		if (!withOtp) formData.delete("otp");

		chunkLoader("add", `#${form.id}`);
		let data;
		try {
			const response = await fetch(action, { method: "POST", body: formData });
			if (!response.ok) throw new Error(response.statusText);
			data = await response.json();
		} catch (error) {
			console.error("Error:", error);
			data = { success: false, message: "Something went wrong. Please try again." };
		}
		chunkLoader("remove", `#${form.id}`);

		if (!data.success) {
			if (data.validation === false) {
				formMsg.replaceChildren();
				showErrors(data.message, form);
			} else {
				const box = showMessage(data.message, false);
				if (data.register) {
					const link = document.createElement("a");
					link.href = `register?identifier=${encodeURIComponent(identifier.value)}`;
					link.className = "underline pl-1";
					link.textContent = "Create an account";
					box.append(" ", link);
				}
				if (data.resend_in) startResendCountdown(data.resend_in);
			}
			return;
		}

		showMessage(data.message, true);
		if (data.otp_sent) {
			otpInput.value = "";
			showOtpStep(data.resend_in);
		} else {
			reset();
			onSuccess(data);
		}
	}

	switchButtons.forEach((button) => button.addEventListener("click", () => setChannel(button.dataset.channel)));
	identifier.addEventListener("input", () => {
		if (channel === "mobile") identifier.value = identifier.value.replace(/[^0-9]/g, "").slice(0, 10);
	});
	otpInput.addEventListener("input", () => {
		otpInput.value = otpInput.value.replace(/[^0-9]/g, "").slice(0, 6);
	});
	changeButton.addEventListener("click", () => {
		reset();
		formMsg.replaceChildren();
		identifier.focus();
	});
	resendButton.addEventListener("click", () => request(false));
	form.addEventListener("submit", (e) => {
		if (!isActive()) return;
		e.preventDefault();
		request(!otpStep.hidden);
	});

	// A value pre-filled by the server (e.g. sent over from the login page)
	// decides which side of the switch the form opens on.
	setChannel(identifier.value.includes("@") ? "email" : "mobile", true);

	return { reset, setChannel };
}
