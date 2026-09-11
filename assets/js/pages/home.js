// Modal Newsletter
const modalNewsletter = document.querySelector(".modal-newsletter");
const modalNewsletterMain = document.querySelector(
	".modal-newsletter .modal-newsletter-main"
);
const closeBtnModalNewsletter = document.querySelector(
	".modal-newsletter .close-newsletter-btn"
);

if (modalNewsletter) {
	// setTimeout(() => {
	// 	modalNewsletterMain.classList.add("open");
	// }, 3000);

	modalNewsletter.addEventListener("click", () => {
		modalNewsletterMain.classList.remove("open");
	});

	closeBtnModalNewsletter.addEventListener("click", () => {
		modalNewsletterMain.classList.remove("open");
	});

	modalNewsletterMain.addEventListener("click", (e) => {
		e.stopPropagation();
	});
}
