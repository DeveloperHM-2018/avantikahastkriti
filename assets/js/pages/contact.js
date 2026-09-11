import { submitForm } from "./formHandlers.js";
import { chunkLoader } from "./loaders.js";

const contactForm = document.querySelector("#contact-form");

contactForm.addEventListener("submit", async (e) => {
    chunkLoader("add", `#${e.target.id}`);
    const action = `Web/contactQuery/`;
    await submitForm(e, action).then((isSuccess) => {
        if (isSuccess) {
            contactForm.querySelector("#charCount").textContent = `0/500 character`;
        } else {
            console.log("failed!");
        }
    });
    chunkLoader("remove", `#${e.target.id}`);
});

const messageField = document.getElementById("message");
const charCount = document.getElementById("charCount");
const maxLength = 500;

messageField.addEventListener("input", () => {
    const currentLength = messageField.value.length;

    if (currentLength > maxLength) {
        messageField.value = messageField.value.substring(0, maxLength);
    }

    const newLength = messageField.value.length;
    const remaining = maxLength - newLength;

    charCount.textContent = `${newLength}/${maxLength} characters used`;
    if (remaining <= 50) {
        charCount.classList.add("warning");
    } else {
        charCount.classList.remove("warning");
    }
});