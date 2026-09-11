// formHandlers.js

// Function to handle form submission
export async function submitForm(e, action, panel = "web") {
	e.preventDefault();
	clearErrors();

	const formMsgElement = e.target.querySelector(".formMsg");
	const formData = new FormData(e.target);

	return fetch(action, {
		method: "POST",
		body: formData,
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok " + response.statusText);
			}
			return response.json();
		})
		.then((data) => {
			if (data.success === false) {
				if (data.validation === false) {
					showErrors(data.message);
					return false; // Return false for validation errors
				} else {
					if (panel === "web") {
						formMsgElement.innerHTML = `<div class="alert-${data.success ? "success" : "false"
							}"><i class="icon ph ph-warning text-white"></i>${data.message}</div>`;
					} else {
						formMsgElement.innerHTML = `<div class="alert alert-${data.success ? "success" : "danger"
							}"><i class="icon ph ph-warning text-white"></i>${data.message}</div>`;
					}

					return false;
				}
			} else {
				e.target.reset();
				if (panel === "web") {
					formMsgElement.innerHTML = `<div class="alert-${data.success ? "success" : "false"
						}"><i class="icon ph ph-check text-white"></i>${data.message}</div>`;
				} else {
					formMsgElement.innerHTML = `<div class="alert alert-${data.success ? "success" : "danger"
						}"><i class="icon ph ph-check text-white"></i>${data.message}</div>`;
				}

				return true;
			}
		})
		.catch((error) => {
			console.error("Error:", error);
			return false;
		});
}

// === Show error message below relevent input field ===
export function showErrors(messages) {
	for (const [key, message] of Object.entries(messages)) {
		let errorElement = document.querySelector(
			`[name="${key}"] + .error-message`
		);

		if (!errorElement) {
			errorElement = document.createElement("div");
			errorElement.className = "error-message text-red text-sm mt-1";

			const inputElement = document.querySelector(`[name="${key}"]`);
			inputElement.parentNode.insertBefore(
				errorElement,
				inputElement.nextSibling
			);
		}

		errorElement.innerText = message;
	}
}

// === clear error message below relevent input field ===
export function clearErrors() {
	const errorElements = document.querySelectorAll(".error-message");
	errorElements.forEach((element) => {
		element.innerText = "";
	});
}


export function validateMobile(event) {
	const input = event.target;
	input.value = input.value.replace(/[^0-9]/g, '');
	if (input.value.length > 10) {
		input.value = input.value.slice(0, 10);
	}
}