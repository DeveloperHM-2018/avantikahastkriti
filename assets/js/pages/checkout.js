import { chunkLoader } from "./loaders.js";
import { validateMobile } from "./formHandlers.js";

const checkoutForm = document.querySelector("#checkout-form");

const showMessage = (element, message, type = "false") => {
  element.innerHTML = `<div class="alert-${type}"><i class="icon top-0 ph ph-${type === 'success' ? 'check' : 'warning'} text-white"></i>${message}</div>`;
};

const isCartNotEmpty = () => {
  const cartData = JSON.parse(localStorage.getItem("cartStore"));
  return cartData && cartData.length > 0;
};

// === Show error message below relevent input field ===
function showErrors(messages) {
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
function clearErrors() {
  const errorElements = document.querySelectorAll(".error-message");
  errorElements.forEach((element) => {
    element.innerText = "";
  });
}

// === Build the order form data shared by COD and online payment ===
async function buildOrderFormData(e, paymentValue) {
  const formData = new FormData(e.target);

  // 1. Append Cart Data
  const cartData = JSON.parse(localStorage.getItem("cartStore"));
  if (cartData) {
    cartData.forEach((product, index) => {
      formData.append(`products[${index}][id]`, product.id);
      formData.append(`products[${index}][quantity]`, product.quantityPurchase);
      if (product.variant_id) {
        formData.append(`products[${index}][variant_id]`, product.variant_id);
      }
    });
  }

  // 2. Build the full address from the manually entered address fields
  const houseNo = formData.get("house_no") || "";
  const landmark = formData.get("landmark") || "";
  const address = formData.get("address") || "";
  const city = formData.get("city") || "";
  const state = formData.get("state") || "";
  const postalCode = formData.get("postal_code") || "";
  const fullAddressParts = [houseNo, landmark, address, city, state, postalCode].filter(Boolean);
  formData.set("full_address", fullAddressParts.join(", "));

  formData.set("payment", paymentValue);

  // 3. Append the coupon code actually applied on this checkout, so the
  // discount the user was shown is the one the server prices and charges.
  const appliedPromoCode = localStorage.getItem("appliedPromoCode");
  if (appliedPromoCode) {
    formData.set("coupon_code", appliedPromoCode);
  }

  return formData;
}

// === Handle Cash On Delivery directly (Bypass Razorpay) ===
async function processCOD(e) {
  clearErrors();
  const formMsgElement = e.target.querySelector(".formMsg");
  const formData = await buildOrderFormData(e, "1");

  try {
    const response = await fetch("Web/place_order", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error("Network response was not ok");
    }

    const data = await response.json();

    if (!data.success) {
      if (data.validation === false) {
        showErrors(data.message);
      } else {
        showMessage(formMsgElement, data.message, "danger");
      }
      return false;
    }

    // Success! Clear the cart and applied coupon (so it doesn't silently
    // carry over and get re-applied to a future, unrelated checkout) and
    // redirect to Thank You page
    localStorage.removeItem("cartStore");
    localStorage.removeItem("appliedPromoCode");
    localStorage.removeItem("appliedPromoDiscount");
    localStorage.removeItem("appliedPromoSubtotal");
    window.location.href = `${BASE_URL}thankyou?checkout_id=${data.o_id}`;
    return true;

  } catch (error) {
    console.error("Error:", error);
    showMessage(formMsgElement, "An error occurred while placing the order.", "danger");
    return false;
  }
}

// === Handle online payment via Razorpay's standard Checkout modal ===
async function processOnlinePayment(e) {
  clearErrors();
  const formMsgElement = e.target.querySelector(".formMsg");
  const formData = await buildOrderFormData(e, "0");

  let data;
  try {
    const response = await fetch("Web/place_order", {
      method: "POST",
      body: formData,
    });
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    data = await response.json();
  } catch (error) {
    console.error("Error:", error);
    showMessage(
      formMsgElement,
      "An error occurred while submitting the form. Please try again later.",
      "danger"
    );
    return false;
  }

  if (!data.success) {
    if (data.validation === false) {
      showErrors(data.message);
    } else {
      showMessage(formMsgElement, data.message, "danger");
    }
    return false;
  }

  return new Promise((resolve) => {
    const options = {
      key: data.st_razorpay_api_key,
      amount: data.amount,
      currency: data.currency,
      name: data.formData?.name,
      order_id: data.razorpay_order_id,
      prefill: {
        name: data.formData?.name,
        email: data.formData?.email,
        contact: data.formData?.contact_no,
      },
      handler: async function (response) {
        const verifyResponse = await fetch('Web/handle_payment_response', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(response),
        });
        const verifyData = await verifyResponse.json();
        if (!verifyData.success) {
          showMessage(formMsgElement, verifyData.message, "danger");
          resolve(false);
          return;
        }
        localStorage.removeItem("cartStore");
        localStorage.removeItem("appliedPromoCode");
        localStorage.removeItem("appliedPromoDiscount");
        localStorage.removeItem("appliedPromoSubtotal");
        window.location.href = `${BASE_URL}thankyou?checkout_id=${data.o_id}`;
        resolve(true);
      },
      modal: {
        ondismiss: function () {
          resolve(false);
        },
      },
      theme: { color: '#b7081b' },
    };

    const razorpay = new Razorpay(options);
    razorpay.open();
  });
}

// === Re-validate the applied coupon (expiry / minimum order / eligibility)
// against the live cart right before submitting, so the discount the
// customer is charged always matches what was validated. Cart contents can
// change after a coupon was applied, so trusting the last-known result would
// let a now-invalid coupon silently ride through to place_order().
async function revalidateAppliedCoupon() {
  const appliedPromoCode = localStorage.getItem("appliedPromoCode");
  if (!appliedPromoCode) {
    return true;
  }

  const cartData = JSON.parse(localStorage.getItem("cartStore")) || [];
  const subtotal = cartData.reduce((sum, product) => sum + (product.price * product.quantityPurchase), 0);

  try {
    const response = await fetch(`${BASE_URL}Web/applyPromo`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ promoCode: appliedPromoCode, subtotal }),
    });
    const data = await response.json();

    if (data.status) {
      localStorage.setItem("appliedPromoDiscount", data.discount_amount);
      localStorage.setItem("appliedPromoSubtotal", subtotal);
      return true;
    }

    // No longer eligible - drop it so a stale coupon can't silently ride
    // through into the charged amount, and stop the submission so the
    // customer sees why before anything is charged.
    localStorage.removeItem("appliedPromoCode");
    localStorage.removeItem("appliedPromoDiscount");
    localStorage.removeItem("appliedPromoSubtotal");
    return data.message || "Your coupon is no longer valid and has been removed.";
  } catch (error) {
    console.error("Error re-validating coupon:", error);
    // Fail open on a network hiccup - place_order() independently
    // re-validates the coupon server-side regardless, so this check is a
    // UX convenience, not the sole gate.
    return true;
  }
}

// === Re-check the cart against live stock data right before submitting - a
// cart item can go from in-stock to flagged out-of-stock after being added
// (main.js only guards at add-to-cart/buy-now time). Removes any such items
// from the cart so their price is never charged, and stops the submission so
// the customer sees the updated cart before anything happens, instead of
// silently round-tripping through place_order()'s server-side rejection.
async function revalidateCartStock() {
  const cartData = JSON.parse(localStorage.getItem("cartStore")) || [];
  if (cartData.length === 0) {
    return true;
  }

  try {
    const response = await fetch(`${BASE_URL}Test/products`);
    const products = await response.json();
    const outOfStockMap = {};
    products.forEach((p) => {
      outOfStockMap[p.id] = !!p.outOfStock;
    });

    const outOfStockItems = cartData.filter((item) => outOfStockMap[item.id]);
    if (outOfStockItems.length === 0) {
      return true;
    }

    const remaining = cartData.filter((item) => !outOfStockMap[item.id]);
    localStorage.setItem("cartStore", JSON.stringify(remaining));

    const names = outOfStockItems.map((item) => item.name).join(", ");
    const plural = outOfStockItems.length > 1;
    return `${names} ${plural ? "are" : "is"} out of stock and ${plural ? "have" : "has"} been removed from your cart.`;
  } catch (error) {
    console.error("Error re-validating stock:", error);
    // Fail open on a network hiccup - place_order() independently re-checks
    // stock server-side regardless, so this check is a UX convenience.
    return true;
  }
}

function showEmptyCartIfNeeded() {
  if (isCartNotEmpty()) {
    return;
  }
  if (document.querySelector("#checkout-main-content")) {
    document.querySelector("#checkout-main-content").classList.add("hidden");
    document.querySelector("#checkout-main-content").classList.remove("flex");
  }
  if (document.querySelector(".empty-cart-block")) {
    document.querySelector(".empty-cart-block").classList.remove("hidden");
  }
}

// Guards against a duplicate/overlapping place_order() request - chunkLoader()
// only shows a loading overlay, it doesn't itself stop the button from being
// clicked (or the Enter key from resubmitting) again while the first request
// is still in flight.
let isSubmitting = false;

checkoutForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formMsgElement = e.target.querySelector(".formMsg");

  if (isSubmitting) {
    return;
  }

  if (!isCartNotEmpty()) {
    showMessage(
      formMsgElement,
      `Your cart is empty! Please add a product.&nbsp;<a href="${BASE_URL}products" class="font-semibold underline">Shop now</a>`
    );
    return;
  }

  isSubmitting = true;
  const submitButton = document.getElementById("checkoutbutton");
  if (submitButton) submitButton.disabled = true;
  chunkLoader("add", `#${e.target.id}`);

  try {
    const stockCheck = await revalidateCartStock();
    if (stockCheck !== true) {
      showMessage(formMsgElement, stockCheck, "danger");
      if (typeof handleCartRendering === "function" && typeof listProductCheckout !== "undefined" && listProductCheckout) {
        handleCartRendering(listProductCheckout);
      }
      showEmptyCartIfNeeded();
      return;
    }

    const couponCheck = await revalidateAppliedCoupon();
    if (couponCheck !== true) {
      showMessage(formMsgElement, couponCheck, "danger");
      if (typeof handleCartRendering === "function" && typeof listProductCheckout !== "undefined" && listProductCheckout) {
        handleCartRendering(listProductCheckout);
      }
      return;
    }

    // Check which payment method radio button is selected
    const selectedPayment = document.querySelector('input[name="payment"]:checked');
    let isSuccess = false;

    if (selectedPayment && selectedPayment.value === "1") {
      // Value 1 is COD -> Run native checkout
      isSuccess = await processCOD(e);
    } else {
      // Value 0 is Online -> Open Razorpay's standard Checkout modal
      isSuccess = await processOnlinePayment(e);
    }

    if (!isSuccess) {
      console.log("Checkout process stopped or failed.");
    }
  } finally {
    chunkLoader("remove", `#${e.target.id}`);
    if (submitButton) submitButton.disabled = false;
    isSubmitting = false;
  }
});

document.addEventListener("DOMContentLoaded", () => {
  showEmptyCartIfNeeded();

  // Update the submit button label to match the selected payment method.
  const paymentButton = document.querySelector("#checkoutbutton");
  const paymentInputs = document.querySelectorAll('input[name="payment"]');
  const updatePaymentButtonLabel = () => {
    if (!paymentButton) return;
    const selected = document.querySelector('input[name="payment"]:checked');
    paymentButton.textContent = selected && selected.value === "1" ? "Place Order" : "Proceed to Pay";
  };
  paymentInputs.forEach((input) => {
    input.addEventListener("change", updatePaymentButtonLabel);
  });
  updatePaymentButtonLabel();

  const phoneField = document.getElementById("number");
  if (phoneField) {
    phoneField.addEventListener("input", validateMobile);
  }
});

