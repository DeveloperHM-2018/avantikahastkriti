import { initOtpForm } from "./otpFlow.js";

const registrationForm = document.querySelector("#registration-form");

// The whole form is posted twice: first to send the OTP to the mobile/email
// being registered, then again with the OTP to create the account.
initOtpForm(registrationForm, {
	action: `Auth/registration/`,
	verifyLabel: "Verify & Create Account",
	onSuccess: () => redirect("profile"),
});

//  === Password validation function ===
const passwordInput = document.getElementById("password"),
	criteriaContainer = document.getElementById("passwordCriteria");
const validations = [
	{
		regex: /.{6,}/,
		checkbox: "minLength",
		label: "minLengthLabel",
		icon: "minLengthIcon",
	},
	{
		regex: /[A-Z]/,
		checkbox: "uppercase",
		label: "uppercaseLabel",
		icon: "uppercaseIcon",
	},
	{
		regex: /[a-z]/,
		checkbox: "lowercase",
		label: "lowercaseLabel",
		icon: "lowercaseIcon",
	},
	{ regex: /\d/, checkbox: "digit", label: "digitLabel", icon: "digitIcon" },
	{
		regex: /[@$!%*?&]/,
		checkbox: "specialChar",
		label: "specialCharLabel",
		icon: "specialCharIcon",
	},
];

function validatePassword(password) {
	// Show or hide the criteria container based on input presence
	criteriaContainer.style.display = password ? "flex" : "none";

	validations.forEach(({ regex, checkbox, label, icon }) => {
		const isValid = regex.test(password);
		// document.getElementById(checkbox).checked = isValid;
		document.getElementById(label).classList.toggle("valid", isValid);
		document.getElementById(label).classList.toggle("invalid", !isValid);

		// Toggle icon between check and cross with color
		const iconElement = document.getElementById(icon);
		iconElement.textContent = isValid ? "✔" : "✖";
		iconElement.classList.toggle("valid", isValid);
		iconElement.classList.toggle("invalid", !isValid);
	});
}

// Add event listener on keyup for real-time feedback
passwordInput.addEventListener("keyup", () =>
	validatePassword(passwordInput.value)
);
