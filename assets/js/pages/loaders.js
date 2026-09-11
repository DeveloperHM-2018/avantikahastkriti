export function chunkLoader(addOrRemove, targetElement) {
	const parentElement = document.querySelector(targetElement);

	if (addOrRemove === "add") {
		parentElement.classList.add("chunk-loading");

		const loadingDiv = document.createElement("div");
		loadingDiv.classList.add("progress");

		parentElement.appendChild(loadingDiv);
	} else if (addOrRemove === "remove") {
		parentElement.classList.remove("chunk-loading");

		const loadingDiv = parentElement.querySelector(".progress");
		if (loadingDiv) {
			parentElement.removeChild(loadingDiv);
		}
	}
}
