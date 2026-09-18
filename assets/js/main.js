
// === Function to get the site's base URL ===
// Uses a dedicated <meta name="base-url"> tag rather than the canonical link -
// canonical must reflect each page's own URL (for SEO/Meta Pixel/crawlers), so
// it can no longer double as a "site root" holder for JS.

const getBaseUrl = () =>
  document.querySelector('meta[name="base-url"]')?.getAttribute("content") || null;

const BASE_URL = getBaseUrl();

function redirect(url) {
  window.location.href = `${BASE_URL}${url}`;
}

// === Prevent Form submission on page load ===
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}

const chooseType = document.querySelectorAll(".top-nav .choose-type");
const optionItems = document.querySelectorAll(
  ".top-nav .choose-type .list-option li"
);

const Toast = (() => {
  const toastContainer = document.createElement("div");
  toastContainer.id = "toast-container";
  toastContainer.style.position = "fixed";
  toastContainer.style.top = "50px";
  toastContainer.style.left = "50%";
  toastContainer.style.transform = "translate(-50%, -50%)";
  toastContainer.style.zIndex = "9999";
  toastContainer.style.display = "flex";
  toastContainer.style.flexDirection = "column";
  toastContainer.style.gap = "10px";
  toastContainer.classList.add();
  document.body.appendChild(toastContainer);

  function showToast(message, options = {}) {
    const {
      duration = 5000,
      type = "info",
      closeable = true,
      withProgressBar = true,
      icon = true,
    } = options;

    const toast = document.createElement("div");
    toast.style.display = "flex";
    toast.style.alignItems = "center";
    toast.style.justifyContent = "space-between";
    toast.style.position = "relative";
    toast.style.padding = "15px 20px";
    toast.style.marginBottom = "10px";
    toast.style.color = "#fff";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.1)";
    toast.style.fontSize = "14px";
    toast.style.animation = "fadeIn 0.5s ease";
    toast.style.width = "max-content";

    // Background color based on type
    const colors = {
      info: "#2196f3",
      success: "#4caf50",
      error: "#f44336",
      warning: "#ff9800",
    };
    const classes = {
      info: "toast-info",
      success: "toast-success",
      error: "toast-error",
      warning: "toast-warning",
    };
    toast.style.backgroundColor = colors[type] || colors.info;
    toast.classList.add(classes[type] || classes.info);

    // Add optional icon
    if (icon) {
      const iconElement = document.createElement("span");
      iconElement.style.marginRight = "10px";
      iconElement.innerHTML =
        {
          info: "ℹ️",
          success: "✅",
          error: "❌",
          warning: "⚠️",
        }[type] || "ℹ️";
      toast.appendChild(iconElement);
    }

    // Add message
    const messageElement = document.createElement("span");
    messageElement.textContent = message;
    messageElement.style.flex = "1";
    toast.appendChild(messageElement);

    // Add close button
    if (closeable) {
      const closeButton = document.createElement("button");
      closeButton.textContent = "✖";
      closeButton.style.background = "transparent";
      closeButton.style.border = "none";
      closeButton.style.color = "#fff";
      closeButton.style.fontSize = "16px";
      closeButton.style.cursor = "pointer";
      closeButton.style.marginLeft = "10px";
      closeButton.addEventListener("click", () => {
        toastContainer.removeChild(toast);
      });
      toast.appendChild(closeButton);
    }

    // Add progress bar
    if (withProgressBar) {
      const progressBar = document.createElement("div");
      progressBar.style.position = "absolute";
      progressBar.style.bottom = "0";
      progressBar.style.left = "0";
      progressBar.style.width = "100%";
      progressBar.style.height = "4px";
      progressBar.style.backgroundColor = "rgba(255, 255, 255, 0.7)";
      progressBar.style.transition = `width ${duration}ms linear`;
      toast.appendChild(progressBar);

      setTimeout(() => {
        progressBar.style.width = "0";
      }, 0);
    }

    // Add toast to container
    toastContainer.appendChild(toast);

    // Auto-remove toast after the duration
    setTimeout(() => {
      if (toast.parentElement) {
        toast.style.opacity = "0";
        setTimeout(() => {
          if (toast.parentElement) {
            toastContainer.removeChild(toast);
          }
        }, 500);
      }
    }, duration);
  }

  // Example Usage
  // Toast.show("This is an informational message.", {
  // 	type: "info",
  // 	duration: 3000,
  // });
  // Toast.show("Operation successful!", {
  // 	type: "success",
  // 	icon: true,
  // 	duration: 4000,
  // });
  // Toast.show("An error occurred.", { type: "error", closeable: true });
  // Toast.show("Warning! Check your input.", {
  // 	type: "warning",
  // 	withProgressBar: false,
  // });

  // Add CSS animations
  const style = document.createElement("style");
  style.innerHTML = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
  document.head.appendChild(style);

  return {
    show: showToast,
  };
})();
if (chooseType) {
  chooseType.forEach((item) => {
    item.addEventListener("click", () => {
      item.querySelector(".list-option").classList.toggle("open");
    });
  });

  optionItems.forEach((item) => {
    item.addEventListener("click", () => {
      item.parentElement.querySelector(".active").classList.remove("active");
      item.classList.add("active");

      let dataItem = item.getAttribute("data-item");
      item.parentElement.parentElement.querySelector(".selected").innerHTML =
        dataItem;
      item.closest("choose-type").classList.remove("open");
    });
  });
}

// Add fixed header
const headerMain = document.querySelector(".header-menu");
const headerLogo = document.querySelector(".header-logo");

window.addEventListener("scroll", () => {
  if (window.scrollY > 100) {
    headerMain.classList.add("fixed");
    headerLogo.classList.add("fixed")
  } else {
    headerMain.classList.remove("fixed");
    headerLogo.classList.remove("fixed")
  }
});

// Marquee banner top
let SwiperTop = new Swiper(".marquee-block", {
  spaceBetween: 0,
  centeredSlides: true,
  speed: 5000,
  autoplay: {
    delay: 1,
  },
  loop: true,
  slidesPerView: "auto",
  allowTouchMove: false,
  disableOnInteraction: true,
});

// Menu mobile
const menuMobileIcon = document.querySelector(".menu-mobile-icon");
const menuMobileBlock = document.querySelector("#menu-mobile");
const closeMenuMobileIcon = document.querySelector(
  "#menu-mobile .close-menu-mobile-btn"
);

const openMenuMobile = () => {
  menuMobileBlock.classList.add("open");
};

const closeMenuMobile = () => {
  menuMobileBlock.classList.remove("open");
};

if (menuMobileIcon) {
  menuMobileIcon.addEventListener("click", openMenuMobile);
  closeMenuMobileIcon.addEventListener("click", closeMenuMobile);
}

const mobileNavItems = document.querySelectorAll(
  "#menu-mobile .list-nav>ul>li"
);

mobileNavItems.forEach((item) => {
  item.addEventListener("click", () => {
    if (!item.classList.contains("open")) {
      item.classList.add("open");
    }
  });
});

const backMenuBtns = document.querySelectorAll(
  "#menu-mobile .list-nav>ul>li .back-btn"
);

backMenuBtns.forEach((btn) => {
  btn.addEventListener("click", (e) => {
    e.stopPropagation();
    const subNavParent = btn.parentElement.parentElement;
    subNavParent.classList.remove("open");
  });
});

// Modal Search
const searchIcon = document.querySelector(".search-icon");
const modalSearch = document.querySelector(".modal-search-block");
const modalSearchMain = document.querySelector(
  ".modal-search-block .modal-search-main"
);

if (searchIcon) {
  searchIcon.addEventListener("click", () => {
    modalSearchMain.classList.add("open");
  });

  modalSearch.addEventListener("click", () => {
    modalSearchMain.classList.remove("open");
  });

  modalSearchMain.addEventListener("click", (e) => {
    e.stopPropagation();
  });
}

// Redirect to search-results when enter or click form search
const formSearch = document.querySelectorAll(".form-search");

if (formSearch) {
  formSearch.forEach((form) => {
    const formInput = form.querySelector("input");
    const searchIcon = form.querySelector("i.ph-magnifying-glass");
    const searchBtn = form.querySelector("button");

    formInput.addEventListener("keyup", (e) => {
      if (e.key === "Enter") {
        if (e.target.classList.contains("locationInput")) {
          // Handle location input Enter key press
          // Add your location-specific logic here
        } else if (e.target.classList.contains("search-input")) {
          // Handle search input Enter key press
          //   window.location.href = `search-result.html?query=${formInput.value}`;
        }
      }
    });

    if (searchIcon) {
      searchIcon.addEventListener("click", (e) => {
        if (formInput.classList.contains("search-input")) {
          //   window.location.href = `search-result.html?query=${formInput.value}`;
        }
      });
    }

    if (searchBtn) {
      searchBtn.addEventListener("click", (e) => {
        if (formInput.classList.contains("search-input")) {
          //   window.location.href = `search-result.html?query=${formInput.value}`;
        }
      });
    }
  });
}
const keywordSearch = document.querySelectorAll(".list-keyword .item");

if (keywordSearch) {
  keywordSearch.forEach((item) => {
    item.addEventListener("click", (e) => {
      const query = item.innerHTML.toLowerCase().replace(/\s+/g, "");
      //   window.location.href = `search-result.html?query=${query}`;
    });
  });
}

// Filter product in search-results
const listSearchResults = document.querySelector(".search-result-block");

if (listSearchResults) {
  // get curent URL
  const urlParams = new URLSearchParams(window.location.search);

  // get value 'query'
  const queryValue = urlParams.get("query");

  const listProductResult = document.querySelector(".list-product-result");
  if (queryValue) {
    fetch(`${BASE_URL}Test/products`)
      .then((response) => response.json())
      .then((products) => {
        const filterPrd = products.filter(
          (product) =>
            product.type.includes(queryValue) ||
            product.category.includes(queryValue) ||
            product.name.includes(queryValue)
        );
        const result = listSearchResults.querySelectorAll(".result");
        const resultQuantity =
          listSearchResults.querySelector(".result-quantity");

        // Set number of results
        resultQuantity.innerHTML = filterPrd.length;

        // Set text result
        result.forEach((item) => {
          item.innerHTML = queryValue;
        });

        // Show product results
        filterPrd.forEach((product) => {
          const productElement = createProductItem(product);
          listProductResult.appendChild(productElement);
        });

        if (filterPrd.length === 0) {
          listProductResult.innerHTML = `<p>No product found.</p>`;
        }
      })
      .catch((error) => console.error("Error loading products:", error));
  } else {
    listProductResult.innerHTML = `<p>No product searched.</p>`;
  }
}

// Modal login
const loginIcon = document.querySelector(".user-icon i");
const loginPopup = document.querySelector(".login-popup");

loginIcon?.addEventListener("click", () => {
  loginPopup.classList.toggle("open");
});

// initialize the variable(cart, wishlist, compare) in local storage
let cartStore = localStorage.getItem("cartStore");
if (cartStore === null) {
  localStorage.setItem("cartStore", JSON.stringify([]));
}

let wishlistStore = localStorage.getItem("wishlistStore");
if (wishlistStore === null) {
  localStorage.setItem("wishlistStore", JSON.stringify([]));
}

// The wishlist is only ever stored in this browser's localStorage, so it
// must be gated here on login state - guests get sent to log in instead of
// being able to view/add/remove items.
function requireLoginForWishlist() {
  if (!window.isUserLoggedIn) {
    redirect("login");
    return false;
  }
  return true;
}

let compareStore = localStorage.getItem("compareStore");
if (compareStore === null) {
  localStorage.setItem("compareStore", JSON.stringify([]));
}

let quickViewStore = localStorage.getItem("quickViewStore");
if (quickViewStore === null) {
  localStorage.setItem("quickViewStore", JSON.stringify([]));
}

// Modal Wishlist
const wishlistIcon = document.querySelector(".wishlist-icon");
const modalWishlist = document.querySelector(".modal-wishlist-block");
const modalWishlistMain = document.querySelector(
  ".modal-wishlist-block .modal-wishlist-main"
);
const closeWishlistIcon = document.querySelector(
  ".modal-wishlist-main .close-btn"
);
const continueWishlistIcon = document.querySelector(
  ".modal-wishlist-main .continue"
);
const addWishlistBtns = document.querySelectorAll(".add-wishlist-btn");

const openModalWishlist = () => {
  modalWishlistMain.classList.add("open");
};

const closeModalWishlist = () => {
  modalWishlistMain.classList.remove("open");
};

addWishlistBtns.forEach((addWishlistBtn) => {
  addWishlistBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    if (!requireLoginForWishlist()) return;
    addWishlistBtn.classList.toggle("active");

    if (addWishlistBtn.classList.contains("active")) {
      addWishlistBtn.querySelector("i").classList.remove("ph");
      addWishlistBtn.querySelector("i").classList.add("ph-fill");
      openModalWishlist();
    } else {
      addWishlistBtn.querySelector("i").classList.add("ph");
      addWishlistBtn.querySelector("i").classList.remove("ph-fill");
    }
  });
});

wishlistIcon.addEventListener("click", () => {
  if (!requireLoginForWishlist()) return;
  openModalWishlist();
});
modalWishlist.addEventListener("click", closeModalWishlist);
closeWishlistIcon.addEventListener("click", closeModalWishlist);
continueWishlistIcon.addEventListener("click", closeModalWishlist);

modalWishlistMain.addEventListener("click", (e) => {
  e.stopPropagation();
});

// Modal Similar Product
const modalSimilarProduct = document.querySelector(".modal-similar-product-block");
const modalSimilarProductMain = document.querySelector(
  ".modal-similar-product-block .modal-similar-product-main"
);
const closeSimilarProductIcon = document.querySelector(
  ".modal-similar-product-main .close-btn"
);
const continueSimilarProductIcon = document.querySelector(
  ".modal-similar-product-main .continue"
);

const openModalSimilarProduct = async (category) => {
  const listItemSimilar = modalSimilarProduct.querySelector(".list-product");
  listItemSimilar.innerHTML = `<div class="flex justify-center py-10"><div class="progress"></div></div>`;
  modalSimilarProductMain.classList.add("open");

  try {
    const response = await fetch(`${BASE_URL}Test/products`);
    const products = await response.json();
    const similarProducts = products.filter(p => p.category === category);

    listItemSimilar.innerHTML = "";
    if (similarProducts.length === 0) {
      listItemSimilar.innerHTML = `<p class='mt-1'>No similar products found</p>`;
    } else {
      similarProducts.forEach((item) => {
        const prdItem = document.createElement("div");
        prdItem.setAttribute("data-item", item.id);
        prdItem.classList.add(
          "item", "py-4", "flex", "items-center", "justify-between", "gap-3", "border-b", "border-line", "cursor-pointer", "md:border-none", "md:bg-surface", "md:rounded-xl", "md:p-3", "md:hover:bg-line", "duration-300"
        );
        prdItem.innerHTML = `
                  <div class="infor flex items-center gap-3">
                      <div class="bg-img">
                          <img src=${item.thumbImage[0]} alt='product'
                              class='w-[60px] md:w-[50px] aspect-square flex-shrink-0 rounded-lg object-cover' />
                      </div>
                      <div class=''>
                          <div class="name text-button text-sm">${item.name}</div>
                          <div class="flex items-center gap-2 mt-1">
                              <div class="product-price text-title text-sm">₹${parseFloat(item.price).toFixed(2)}</div>
                              <div class="product-origin-price text-title text-xs text-secondary2">
                                  <del>₹${parseFloat(item.originPrice).toFixed(2)}</del>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="view-btn caption1 font-semibold text-black underline">View</div>
              `;
        prdItem.addEventListener('click', () => {
          window.location.href = `${BASE_URL}product/${item.slug}`;
        });
        listItemSimilar.appendChild(prdItem);
      });
    }
  } catch (error) {
    console.error("Error fetching similar products:", error);
    listItemSimilar.innerHTML = `<p class='mt-1 text-red'>Error loading products</p>`;
  }
};

const closeModalSimilarProduct = () => {
  modalSimilarProductMain.classList.remove("open");
};

modalSimilarProductMain.addEventListener("click", (e) => {
  e.stopPropagation();
});

modalSimilarProduct.addEventListener("click", closeModalSimilarProduct);
closeSimilarProductIcon.addEventListener("click", closeModalSimilarProduct);
continueSimilarProductIcon.addEventListener("click", closeModalSimilarProduct);

// Set wishlist length
const handleItemModalWishlist = () => {
  wishlistStore = localStorage.getItem("wishlistStore");

  if (wishlistStore) {
    wishlistIcon.querySelector("span").innerHTML =
      JSON.parse(wishlistStore).length;
  }

  // Set wishlist item
  const listItemWishlist = document.querySelector(
    ".modal-wishlist-block .list-product"
  );

  listItemWishlist.innerHTML = "";

  if (JSON.parse(wishlistStore).length === 0) {
    listItemWishlist.innerHTML = `<p class='mt-1'>No product in wishlist</p>`;
  } else {
    JSON.parse(wishlistStore).forEach((item) => {
      const prdItem = document.createElement("div");
      prdItem.setAttribute("data-item", item.id);
      prdItem.classList.add(
        "item",
        "py-5",
        "flex",
        "items-center",
        "justify-between",
        "gap-3",
        "border-b",
        "border-line"
      );
      prdItem.innerHTML = `
                <div class="infor flex items-center gap-5">
                    <div class="bg-img">
                        <img src=${item.thumbImage[0]} alt='product'
                            class='w-[100px] aspect-square flex-shrink-0 rounded-lg' />
                    </div>
                    <div class=''>
                        <div class="name text-button">${item.name}</div>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="product-price text-title">₹${parseFloat(item.price).toFixed(2)}</div>
                            <div class="product-origin-price text-title text-secondary2">
                                <del>₹${parseFloat(item.originPrice).toFixed(2)}</del>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="remove-wishlist-btn remove-btn caption1 font-semibold text-red underline cursor-pointer">
                    Remove
                </div>
            `;

      listItemWishlist.appendChild(prdItem);
    });
  }

  const prdItems = listItemWishlist.querySelectorAll(".item");
  prdItems.forEach((prd) => {
    const removeWishlistBtn = prd.querySelector(".remove-wishlist-btn");
    removeWishlistBtn.addEventListener("click", () => {
      const prdId = removeWishlistBtn
        .closest(".item")
        .getAttribute("data-item");
      // JSON.parse(wishlistStore)
      const newArray = JSON.parse(wishlistStore).filter(
        (item) => item.id !== prdId
      );
      localStorage.setItem("wishlistStore", JSON.stringify(newArray));
      handleItemModalWishlist();
      updateWishlistIcons();
    });
  });
};

const updateWishlistIcons = () => {
  const wishlistIcons = document.querySelectorAll(".add-wishlist-btn");
  wishlistIcons.forEach((wishlistIcon) => {
    const productId = wishlistIcon
      .closest(".product-item")
      ?.getAttribute("data-item");
    const wishlistStore = localStorage.getItem("wishlistStore")
      ? JSON.parse(localStorage.getItem("wishlistStore"))
      : [];
    const isProductInWishlist = wishlistStore.some(
      (item) => item.id === productId
    );
    if (isProductInWishlist) {
      wishlistIcon.classList.add("active");
      wishlistIcon.querySelector("i").classList.remove("ph");
      wishlistIcon.querySelector("i").classList.add("ph-fill");
    } else {
      wishlistIcon.classList.remove("active");
      wishlistIcon.querySelector("i").classList.add("ph");
      wishlistIcon.querySelector("i").classList.remove("ph-fill");
    }
  });
};

handleItemModalWishlist();

// Modal Cart
const cartIcon = document.querySelector(".cart-icon");
const modalCart = document.querySelector(".modal-cart-block");
const modalCartMain = document.querySelector(
  ".modal-cart-block .modal-cart-main"
);
const closeCartIcon = document.querySelector(".modal-cart-main .close-btn");
const continueCartIcon = document.querySelector(".modal-cart-main .continue");
const addCartBtns = document.querySelectorAll(".add-cart-btn");

const openModalCart = () => {
  modalCartMain.classList.add("open");
};

const closeModalCart = () => {
  modalCartMain.classList.remove("open");
};

// addCartBtns.forEach((item) => {
//   item.addEventListener("click", () => {
//     openModalCart();
//   });
// });

cartIcon.addEventListener("click", openModalCart);
modalCart.addEventListener("click", closeModalCart);
closeCartIcon.addEventListener("click", closeModalCart);
continueCartIcon.addEventListener("click", closeModalCart);

modalCartMain.addEventListener("click", (e) => {
  e.stopPropagation();
});

// Set cart length
const handleItemModalCart = () => {
  cartStore = localStorage.getItem("cartStore");
  cartStore = cartStore ? JSON.parse(cartStore) : [];

  if (cartStore) {
    cartIcon.querySelector("span").innerHTML = cartStore.length;
  }

  const listItemCart = document.querySelector(
    ".modal-cart-block .list-product"
  );

  listItemCart.innerHTML = "";

  if (cartStore.length === 0) {
    listItemCart.innerHTML = `<p class='mt-1'>No product in cart</p>`;
  } else {
    // Initial money to freeship in cart
    let moneyForFreeship = 199;
    let totalCart = 0;

    cartStore.forEach((product) => {
      let variantText = '';
      if (product.variantSize || product.variantColor) {
        const parts = [];
        if (product.variantSize) parts.push(`Size: ${product.variantSize}`);
        if (product.variantColor) parts.push(`Color: ${product.variantColor}`);
        variantText = `<div class="caption1 text-secondary mt-1">${parts.join(' | ')}</div>`;
      }

      const prdItem = document.createElement("div");
      prdItem.classList.add(
        "item",
        "py-5",
        "flex",
        "items-center",
        "justify-between",
        "gap-3",
        "border-b",
        "border-line"
      );
      prdItem.setAttribute("data-item", product.id);
      prdItem.innerHTML = `
                    <div class="infor flex items-center gap-5 w-full cursor-pointer">
                        <div class="bg-img w-[100px] aspect-square flex-shrink-0 rounded-lg overflow-hidden">
                            <img src='${product.thumbImage[0]}' alt='${product.name}' class='w-full h-full object-cover' />
                        </div>
                        <div class='w-full'>
                            <div class="flex items-center justify-between w-full">
                                <div class="name text-button">${product.name}</div>
                                <div 
                                    class="remove-cart-btn caption1 font-semibold text-red underline cursor-pointer"
                                    data-product-id="${product.id}"
                                    data-variant-id="${product.variant_id || ''}"
                                >
                                    Remove
                                </div>
                            </div>
                            ${variantText}
                            <div class="flex items-center justify-between gap-2 mt-3 w-full">
                                <div class="flex items-center text-secondary2 capitalize">Qty: ${product.quantityPurchase}</div>
                                <div class="product-price text-title">₹${(product.price * product.quantityPurchase).toFixed(2)}</div>
                            </div>
                        </div>
                    </div>
                `;
      listItemCart.appendChild(prdItem);
    });
    cartStore.forEach((product) => {
      totalCart += product.price * product.quantityPurchase;
    });
    // Set money to freeship in cart
    modalCart.querySelector(".total-cart").innerHTML = "₹" + totalCart.toFixed(2);
  }
  const prdItems = listItemCart.querySelectorAll(".item");
  prdItems.forEach((prd) => {
    const removeBtn = prd.querySelector(".remove-cart-btn");
    removeBtn.addEventListener("click", function () {
      const prdId = this.dataset.productId;
      const variantId = this.dataset.variantId || null;

      // Remove from cartStore - match by both product ID and variant ID
      const newArray = cartStore.filter((item) => {
        const itemVariantId = item.variant_id || null;
        return !(item.id === prdId && itemVariantId === variantId);
      });

      localStorage.setItem("cartStore", JSON.stringify(newArray));
      handleItemModalCart(); // Call handleItemModalCart to update the display

      if (newArray.length === 0) { // Check newArray length, not cartStore
        modalCart.querySelector(".more-price").innerHTML = 0;
        modalCart.querySelector(".tow-bar-block .progress-line").style.width =
          "0";
        modalCart.querySelector(".total-cart").innerHTML = "₹0.00";
      }
    });
  });
};

handleItemModalCart();

// Countdown cart
let timeLeft = 600;
const countDownCart = setInterval(function () {
  let minutes = Math.floor(timeLeft / 60);
  if (minutes / 10 < 1) {
    minutes = `0${minutes}`;
  }

  let seconds = timeLeft % 60;
  if (seconds / 10 < 1) {
    seconds = `0${seconds}`;
  }

  const minuteTime = document.querySelector(".countdown-cart .minute");
  const secondTime = document.querySelector(".countdown-cart .second");

  if (minuteTime) {
    minuteTime.innerHTML = minutes;
  }
  if (secondTime) {
    secondTime.innerHTML = seconds;
  }

  timeLeft--;

  if (timeLeft < 0) {
    timeLeft = 600;
  }
}, 1000);

// Open note, shipping, coupon popup
const noteBtn = modalCart.querySelector(".note-btn");
const shippingBtn = modalCart.querySelector(".shipping-btn");
const couponBtn = modalCart.querySelector(".coupon-btn");
const notePopup = modalCart.querySelector(".note-block");
const shippingPopup = modalCart.querySelector(".shipping-block");
const couponPopup = modalCart.querySelector(".coupon-block");

if (modalCart) {
  // note block
  noteBtn?.addEventListener("click", () => {
    notePopup.classList.toggle("active");
  });

  notePopup.querySelector(".button-main").addEventListener("click", () => {
    notePopup.classList.remove("active");
  });

  notePopup.querySelector(".cancel-btn").addEventListener("click", () => {
    notePopup.classList.remove("active");
  });

  // shipping block
  shippingBtn?.addEventListener("click", () => {
    shippingPopup.classList.toggle("active");
  });

  shippingPopup.querySelector(".button-main").addEventListener("click", () => {
    shippingPopup.classList.remove("active");
  });

  shippingPopup.querySelector(".cancel-btn").addEventListener("click", () => {
    shippingPopup.classList.remove("active");
  });

  // coupon block
  couponBtn?.addEventListener("click", () => {
    couponPopup.classList.toggle("active");
  });

  couponPopup.querySelector(".button-main").addEventListener("click", () => {
    couponPopup.classList.remove("active");
  });

  couponPopup.querySelector(".cancel-btn").addEventListener("click", () => {
    couponPopup.classList.remove("active");
  });
}

// sub-menu-department
const menuDepartmentBtn = document.querySelector(".menu-department-btn");
const subMenuDepartment = document.querySelector(".sub-menu-department");

if (menuDepartmentBtn) {
  menuDepartmentBtn.addEventListener("click", () => {
    subMenuDepartment.classList.toggle("open");
  });
}

// sub-menu-category
const menuCategoryBtn = document.querySelector(".category-block .category-btn");
const subMenuCategory = document.querySelector(
  ".category-block .sub-menu-category"
);

if (menuCategoryBtn) {
  menuCategoryBtn.addEventListener("click", () => {
    subMenuCategory.classList.toggle("open");
  });
}

// Banner top
var swiperBannerTop = new Swiper(".swiper-banner-top", {
  spaceBetween: 0,
  slidesPerView: 1,
  navigation: {
    prevEl: ".swiper-button-custom-prev",
    nextEl: ".swiper-button-custom-next",
  },
  loop: true,
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
  },
});

// Slider
var swiperSlider = new Swiper(".swiper-slider", {
  spaceBetween: 0,
  slidesPerView: 1,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  loop: true,
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
});

// Slider Toys kid
if (document.querySelector(".slider-toys-kid")) {
  $(".slider-toys-kid").slick({
    dots: false,
    arrows: false,
    infinite: true,
    speed: 300,
    autoplay: false,
    autoplaySpeed: 4000,
    slidesToShow: 1,
    slidesToScroll: 1,
    touchThreshold: 100,
    draggable: true,
    useTransform: false,
  });
}

// Change active menu tab
const tabItems = document.querySelectorAll(".menu-tab .tab-item");
const itemActive = document.querySelectorAll(".menu-tab .tab-item.active");

itemActive.forEach((item) => {
  let indicator = item.parentElement.querySelector(".indicator");
  if (indicator) {
    indicator.style.width = item.getBoundingClientRect().width + "px";
    indicator.style.left =
      item.getBoundingClientRect().left -
      item.parentElement.getBoundingClientRect().left +
      "px";
  }
});

tabItems.forEach((item) => {
  item.addEventListener("click", () => {
    let indicator = item.parentElement.querySelector(".indicator");
    if (indicator) {
      indicator.style.width = item.getBoundingClientRect().width + "px";
      indicator.style.left =
        item.getBoundingClientRect().left -
        item.parentElement.getBoundingClientRect().left +
        "px";
    }

    if (item.parentElement.querySelector(".active")) {
      item.parentElement.querySelector(".active").classList.remove("active");
    }
    item.classList.add("active");
  });
});

// Countdown time
const countDown = new Date("October 30, 2024 00:00:00").getTime();
const setCountDown = setInterval(function () {
  let now = new Date().getTime();
  let distance = countDown - now;

  let days = Math.floor(distance / (1000 * 60 * 60 * 24));
  if (days / 10 < 1) {
    days = `0${days}`;
  }

  let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  if (hours / 10 < 1) {
    hours = `0${hours}`;
  }

  let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  if (minutes / 10 < 1) {
    minutes = `0${minutes}`;
  }

  let seconds = Math.floor((distance % (1000 * 60)) / 1000);
  if (seconds / 10 < 1) {
    seconds = `0${seconds}`;
  }

  const dayTime = document.querySelectorAll(".countdown-day");
  const hourTime = document.querySelectorAll(".countdown-hour");
  const minuteTime = document.querySelectorAll(".countdown-minute");
  const secondTime = document.querySelectorAll(".countdown-second");

  if (dayTime) {
    dayTime.forEach((time) => {
      time.innerHTML = days;
    });
  }
  if (hourTime) {
    hourTime.forEach((time) => {
      time.innerHTML = hours;
    });
  }
  if (minuteTime) {
    minuteTime.forEach((time) => {
      time.innerHTML = minutes;
    });
  }
  if (secondTime) {
    secondTime.forEach((time) => {
      time.innerHTML = seconds;
    });
  }

  if (distance < 0) {
    // clearInterval(x);
    if (dayTime) {
      dayTime.forEach((time) => {
        time.innerHTML = "00";
      });
    }
    if (hourTime) {
      hourTime.forEach((time) => {
        time.innerHTML = "00";
      });
    }
    if (minuteTime) {
      minuteTime.forEach((time) => {
        time.innerHTML = "00";
      });
    }
    if (secondTime) {
      secondTime.forEach((time) => {
        time.innerHTML = "00";
      });
    }
  }
}, 1000);

// Collection
if (document.querySelector(".swiper-collection")) {
  var swiperCollection = new Swiper(".swiper-collection", {
    navigation: {
      prevEl: ".swiper-button-prev",
      nextEl: ".swiper-button-next",
    },
    loop: true,
    autoplay: {
      delay: 3500,
      disableOnInteraction: false,
    },
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: {
        slidesPerView: 3,
        spaceBetween: 16,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1280: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
  });
}

// Collection 6
if (document.querySelector(".swiper-collection-scroll")) {
  var swiperCollection = new Swiper(".swiper-collection-scroll", {
    scrollbar: {
      el: ".swiper-scrollbar",
      hide: true,
    },
    loop: true,
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: {
        slidesPerView: 3,
        spaceBetween: 16,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1280: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
  });
}

// Popular product 6
var swiperScrollImg = new Swiper(".swiper-img-scroll", {
  scrollbar: {
    el: ".swiper-scrollbar",
    hide: true,
  },
  loop: false,
  slidesPerView: 2,
  spaceBetween: 16,
  breakpoints: {
    640: {
      slidesPerView: 3,
      spaceBetween: 16,
    },
    1024: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
  },
});

// Trending 7
if (document.querySelector(".swiper-list-trending")) {
  var trendingSlideCount = document.querySelectorAll(
    ".swiper-list-trending .swiper-slide"
  ).length;
  var capSlidesPerView = function (desired) {
    return Math.min(desired, trendingSlideCount || desired);
  };
  var swiperCollection = new Swiper(".swiper-list-trending", {
    navigation: {
      prevEl: ".swiper-button-prev",
      nextEl: ".swiper-button-next",
    },
    loop: true,
    autoplay: {
      delay: 3500,
      disableOnInteraction: false,
    },
    slidesPerView: capSlidesPerView(2),
    spaceBetween: 16,
    breakpoints: {
      640: {
        slidesPerView: capSlidesPerView(3),
        spaceBetween: 16,
      },
      768: {
        slidesPerView: capSlidesPerView(4),
        spaceBetween: 20,
      },
      1024: {
        slidesPerView: capSlidesPerView(5),
        spaceBetween: 20,
      },
      1280: {
        slidesPerView: capSlidesPerView(6),
        spaceBetween: 30,
      },
    },
  });
}

// Collection 8
if (document.querySelector(".swiper-collection-eight")) {
  var swiperCollection = new Swiper(".swiper-collection-eight", {
    navigation: {
      prevEl: ".swiper-button-prev",
      nextEl: ".swiper-button-next",
    },
    loop: true,
    autoplay: {
      delay: 3500,
      disableOnInteraction: false,
    },
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: {
        slidesPerView: 3,
        spaceBetween: 16,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1024: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
      1280: {
        slidesPerView: 5,
        spaceBetween: 30,
      },
    },
  });
}

// list-product
if (document.querySelector(".swiper-list-product")) {
  var swiperListProduct = new Swiper(".swiper-list-product", {
    navigation: {
      prevEl: ".swiper-button-prev2",
      nextEl: ".swiper-button-next2",
    },
    loop: true,
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: {
        slidesPerView: 3,
        spaceBetween: 16,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
      1280: {
        slidesPerView: 4,
        spaceBetween: 30,
      },
    },
  });
}

// list-three-product
if (document.querySelector(".swiper-list-three-product")) {
  var swiperListProduct = new Swiper(".swiper-list-three-product", {
    navigation: {
      prevEl: ".swiper-button-prev2",
      nextEl: ".swiper-button-next2",
    },
    loop: true,
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: {
        slidesPerView: 3,
        spaceBetween: 16,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
      1280: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
    },
  });
}

// Lookbook Underwear
const lookbookUnderwear = document.querySelector(".lookbook-underwear");

if (lookbookUnderwear) {
  fetch(`${BASE_URL}Test/products`)
    .then((response) => response.json())
    .then((products) => {
      const itemDot = lookbookUnderwear.querySelector(".list-img .item .dots");
      const itemDots = lookbookUnderwear.querySelectorAll(
        ".list-img .item .dots"
      );
      const listPrd = lookbookUnderwear.querySelector(".list-product");
      const prdId = itemDot.getAttribute("data-item");

      // Display products
      products
        .filter((product) => product.id === prdId)
        .forEach((product) => {
          const productElement = createProductItem(product);
          listPrd.appendChild(productElement);
        });

      itemDots.forEach((item) => {
        item.addEventListener("click", () => {
          const prdId = item.getAttribute("data-item");

          // Display products
          listPrd.innerHTML = "";

          products
            .filter((product) => product.id === prdId)
            .forEach((product) => {
              const productElement = createProductItem(product);
              listPrd.appendChild(productElement);
              addEventToProductItem();
            });
        });
      });
    })
    .catch((error) => console.error("Error loading products:", error));
}

// list-feature-product Underwear
var swiperUnderwear = new Swiper(".mySwiper", {
  spaceBetween: 10,
  slidesPerView: 4,
  freeMode: true,
  watchSlidesProgress: true,
  breakpoints: {
    320: {
      slidesPerView: 4,
      spaceBetween: 10,
    },
    768: {
      slidesPerView: 5,
      spaceBetween: 20,
    }
  }
});
var swiper2 = new Swiper(".mySwiper2", {
  spaceBetween: 0,
  thumbs: {
    swiper: swiperUnderwear,
  },
  on: {
    slideChange: function () {
      // Get index of current slide in swiper 1
      let activeIndex = this.activeIndex;

      // Remove class 'swiper-slide-thumb-active' from all slide in swiper 2
      document.querySelectorAll(".mySwiper .swiper-slide").forEach((slide) => {
        slide.classList.remove("swiper-slide-thumb-active");
      });

      // Add class 'swiper-slide-thumb-active' to slide in swiper 2
      document
        .querySelectorAll(".mySwiper .swiper-slide")
      [activeIndex].classList.add("swiper-slide-thumb-active");
    },
  },
});

// Modal Compare
const modalCompareMain = document.querySelector(
  ".modal-compare-block .modal-compare-main"
);
const closeCompareIcon = document.querySelector(
  ".modal-compare-main .close-btn"
);
const clearCompareIcon = document.querySelector(".modal-compare-main .clear");

const openModalCompare = () => {
  modalCompareMain.classList.add("open");
};

const closeModalCompare = () => {
  modalCompareMain.classList.remove("open");
};

closeCompareIcon.addEventListener("click", closeModalCompare);
clearCompareIcon.addEventListener("click", closeModalCompare);

// Set compare length
const handleItemModalCompare = () => {
  compareStore = localStorage.getItem("compareStore");
  compareStore = compareStore ? JSON.parse(compareStore) : [];

  // Set compare item
  const listItemCompare = document.querySelector(
    ".modal-compare-block .list-product"
  );

  listItemCompare.innerHTML = "";

  if (compareStore.length === 0) {
    listItemCompare.innerHTML = `<p class='mt-1'>No product in compare</p>`;
  } else {
    compareStore.forEach((item) => {
      const prdItem = document.createElement("div");
      prdItem.setAttribute("data-item", item.id);
      prdItem.classList.add(
        "item",
        "p-3",
        "border",
        "border-line",
        "rounded-xl",
        "relative"
      );
      prdItem.innerHTML = `
                <div class="infor flex items-center gap-4">
                    <div class="bg-img w-[100px] h-[100px] flex-shrink-0 rounded-lg overflow-hidden">
                        <img src=${item.thumbImage[0]} alt='img'
                            class='w-full h-full' />
                    </div>
                    <div class=''>
                        <div class="name text-title">${item.name}</div>
                        <div class="product-price text-title mt-2">₹${parseFloat(item.price).toFixed(2)}</div>
                    </div>
                </div>
                <div
                    class="remove-btn close-btn absolute w-8 h-8 rounded-full bg-red text-white flex items-center justify-center duration-300 cursor-pointer hover:bg-black"
                    style="top: -16px; right: -16px;"
                    >
                    <i class="ph ph-x text-sm"></i>
                </div>
            `;

      listItemCompare.appendChild(prdItem);
    });
  }

  const prdItems = listItemCompare.querySelectorAll(".item");
  prdItems.forEach((prd) => {
    const removeCompareBtn = prd.querySelector(".remove-btn");
    removeCompareBtn.addEventListener("click", () => {
      const prdId = removeCompareBtn.closest(".item").getAttribute("data-item");
      // compareStore
      const newArray = compareStore.filter((item) => item.id !== prdId);
      localStorage.setItem("compareStore", JSON.stringify(newArray));
      handleItemModalCompare();
      updateCompareIcons();
    });
  });

  const clearCompareBtn = document.querySelector(
    ".modal-compare-block .block-button .clear"
  );
  clearCompareBtn.addEventListener("click", () => {
    localStorage.setItem("compareStore", []);
    updateCompareIcons();
  });
};

const updateCompareIcons = () => {
  const compareIcons = document.querySelectorAll(".compare-btn");
  compareIcons.forEach((compareIcon) => {
    const productId = compareIcon
      .closest(".product-item")
      ?.getAttribute("data-item");
    const compareStore = localStorage.getItem("compareStore")
      ? JSON.parse(localStorage.getItem("compareStore"))
      : [];
    const isProductInCompare = compareStore.some(
      (item) => item.id === productId
    );
    if (isProductInCompare) {
      compareIcon.classList.add("active");
    } else {
      compareIcon.classList.remove("active");
    }
  });
};

handleItemModalCompare();

// Modal Quickview
const modalQuickview = document.querySelector(".modal-quickview-block");
const modalQuickviewMain = document.querySelector(
  ".modal-quickview-block .modal-quickview-main"
);
const closeQuickviewIcon = document.querySelector(
  ".modal-quickview-main .close-btn"
);

const openModalQuickview = () => {
  modalQuickviewMain.classList.add("open");
};

const closeModalQuickview = () => {
  modalQuickviewMain.classList.remove("open");
};

modalQuickview?.addEventListener("click", closeModalQuickview);
closeQuickviewIcon?.addEventListener("click", closeModalQuickview);

modalQuickviewMain?.addEventListener("click", (e) => {
  e.stopPropagation();
});

// show information about product in modalQuickview
const handleItemModalQuickview = () => {
  quickViewStore = localStorage.getItem("quickViewStore");
  quickViewStore = quickViewStore ? JSON.parse(quickViewStore) : [];

  // Set quickView item
  quickViewStore.forEach((item) => {
    modalQuickviewMain.setAttribute("data-item", item.id);

    const listImg = modalQuickviewMain.querySelector(".list-img");
    listImg.innerHTML = `
      ${item.images
        .map(
          (img) =>
            `
        <div class="bg-img w-full aspect-[3/4] max-md:w-[150px] max-md:flex-shrink-0 rounded-[20px] overflow-hidden md:mt-6">
          <img
            src=${img}
            alt="item"
            class="w-full h-full object-cover"
          />
        </div>
        `
        )
        .join("")}
    `;

    modalQuickviewMain.querySelector(".product-infor .category").innerHTML =
      item.category;
    modalQuickviewMain.querySelector(".product-infor .name").innerHTML =
      item.name;

    let arrOfStar = "";
    for (let i = 0; i < 5; i++) {
      if (item.rate) {
        if (i >= item.rate) {
          arrOfStar += '<i class="ph-fill ph-star text-sm text-secondary"></i>';
        } else {
          arrOfStar += '<i class="ph-fill ph-star text-sm text-yellow"></i>';
        }
      }
    }
    modalQuickviewMain.querySelector(".product-infor .rate").innerHTML =
      arrOfStar;
    modalQuickviewMain.querySelector(
      ".product-infor .product-price"
    ).innerHTML = "₹" + parseFloat(item.price).toFixed(2);
    modalQuickviewMain.querySelector(
      ".product-infor .product-origin-price del"
    ).innerHTML = "₹" + parseFloat(item.originPrice).toFixed(2);
    modalQuickviewMain.querySelector(".product-infor .product-sale").innerHTML =
      "-" + Math.floor(100 - (item.price / item.originPrice) * 100) + "%";
    modalQuickviewMain.querySelector(".product-infor .desc").innerHTML =
      item.description;

    const listColor = modalQuickviewMain.querySelector(".list-color");
    listColor.innerHTML = `
      ${item.variation
        ?.map(
          (color) =>
            `
        <div class="color-item w-12 h-12 rounded-xl duration-300 relative">
          <img
              src="${color.colorImage}"
              alt='color'
              class='rounded-xl w-full h-full object-cover'
          />
          <div class="tag-action bg-black text-white caption2 capitalize px-1.5 py-0.5 rounded-sm">${color.color}</div>
        </div>
        `
        )
        .join("")}
    `;

    const listCategory = modalQuickviewMain.querySelector(".list-category");

    listCategory.innerHTML = `
      <a href="shop-breadcrumb1.html" class="text-secondary">${item.category},</a>
      <a href="shop-breadcrumb1.html" class="text-secondary"> ${item.gender}</a>
    `;

    const listTag = modalQuickviewMain.querySelector(".list-tag");

    listTag.innerHTML = `
      <a href="shop-breadcrumb1.html" class="text-secondary">${item.type}</a>
    `;

    let wishlistStore = localStorage.getItem("wishlistStore");
    wishlistStore = wishlistStore ? JSON.parse(wishlistStore) : [];
    const addWishlistIcon =
      modalQuickviewMain.querySelector(".add-wishlist-btn");

    const existingIndex = wishlistStore.findIndex((prd) => prd.id === item.id);

    if (existingIndex > -1) {
      // If prd existed in wishlist, remove it from wishlist
      addWishlistIcon.classList.add("active");
      addWishlistIcon.querySelector("i").classList.remove("ph");
      addWishlistIcon.querySelector("i").classList.add("ph-fill");
    } else {
      // If prd not exist in wishlist, add it to wishlist
      addWishlistIcon.classList.remove("active");
      addWishlistIcon.querySelector("i").classList.add("ph");
      addWishlistIcon.querySelector("i").classList.remove("ph-fill");
    }

    addWishlistIcon.addEventListener("click", (e) => {
      if (!requireLoginForWishlist()) return;
      const existingIndex = wishlistStore.findIndex(
        (prd) => prd.id === item.id
      );

      if (existingIndex > -1) {
        // If prd existed in wishlist, remove it from wishlist
        wishlistStore.splice(existingIndex, 1);
        addWishlistIcon.classList.remove("active");
        addWishlistIcon.querySelector("i").classList.add("ph");
        addWishlistIcon.querySelector("i").classList.remove("ph-fill");
      } else {
        // If prd not exist in wishlist, add it to wishlist
        wishlistStore.push(item);
        addWishlistIcon.classList.add("active");
        addWishlistIcon.querySelector("i").classList.remove("ph");
        addWishlistIcon.querySelector("i").classList.add("ph-fill");
      }

      // Save wishlist to localStorage
      localStorage.setItem("wishlistStore", JSON.stringify(wishlistStore));

      handleItemModalWishlist();
      openModalWishlist();
    });

    let cartStore = localStorage.getItem("cartStore");
    cartStore = cartStore ? JSON.parse(cartStore) : [];
    const addCartIcon = modalQuickviewMain.querySelector(".add-cart-btn");

    addCartIcon.addEventListener("click", (e) => {
      e.stopPropagation();

      const existingIndex = cartStore.findIndex((prd) => prd.id === item.id);

      if (existingIndex > -1) {
        openModalCart();
      } else {
        cartStore.push(item);
        openModalCart();
      }

      localStorage.setItem("cartStore", JSON.stringify(cartStore));
      handleItemModalCart();
    });

    handleActiveSizeChange();
    handleActiveColorChange();
  });
};

// Modal SizeGuide
const openModalSizeGuideBtn = document.querySelectorAll(".size-guide");
const modalSizeGuide = document.querySelector(".modal-sizeguide-block");
const modalSizeGuideMain = document.querySelector(
  ".modal-sizeguide-block .modal-sizeguide-main"
);
const closeSizeGuideIcon = document.querySelector(
  ".modal-sizeguide-main .close-btn"
);

if (modalSizeGuide) {
  const openModalSizeGuide = () => {
    modalSizeGuideMain.classList.add("open");
  };

  const closeModalSizeGuide = () => {
    modalSizeGuideMain.classList.remove("open");
  };

  openModalSizeGuideBtn.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      openModalSizeGuide();
    });
  });

  modalSizeGuide.addEventListener("click", closeModalSizeGuide);
  closeSizeGuideIcon.addEventListener("click", closeModalSizeGuide);

  modalSizeGuideMain.addEventListener("click", (e) => {
    e.stopPropagation();
  });

  // Tow bar filter weight height
  const rangeInputSizeguide = document.querySelectorAll(
    ".modal-sizeguide-block .range-input input"
  );
  const progressHeight = document.querySelector(
    ".filter-height .tow-bar-block .progress"
  );
  const progressWeight = document.querySelector(
    ".filter-weight .tow-bar-block .progress"
  );
  const height = document.querySelector(".modal-sizeguide-block .height");
  const weight = document.querySelector(".modal-sizeguide-block .weight");

  rangeInputSizeguide.forEach((input) => {
    input.addEventListener("input", (e) => {
      // set weight, height
      let heightValue = parseInt(rangeInputSizeguide[0].value);
      let weightValue = parseInt(rangeInputSizeguide[1].value);

      height.innerHTML = heightValue;
      weight.innerHTML = weightValue;

      progressHeight.style.right = 100 - (heightValue / 200) * 100 + "%";
      progressWeight.style.right = 100 - (weightValue / 90) * 100 + "%";

      // Change active weight, height
      let sizeItems = document.querySelectorAll(
        ".modal-sizeguide-block .list-size-block .size-item"
      );

      sizeItems.forEach((size) => {
        if (heightValue > 180 || weightValue > 70) {
          if (size.innerHTML.replace(/\s+/g, "") === "2XL") {
            size.classList.add("active");
          } else {
            size.classList.remove("active");
          }
        } else if (heightValue > 170 || weightValue > 60) {
          if (size.innerHTML.replace(/\s+/g, "") === "XL") {
            size.classList.add("active");
          } else {
            size.classList.remove("active");
          }
        } else if (heightValue > 160 || weightValue > 50) {
          if (size.innerHTML.replace(/\s+/g, "") === "L") {
            size.classList.add("active");
          } else {
            size.classList.remove("active");
          }
        } else if (heightValue > 155 || weightValue > 45) {
          if (size.innerHTML.replace(/\s+/g, "") === "M") {
            size.classList.add("active");
          } else {
            size.classList.remove("active");
          }
        } else if (heightValue > 150 || weightValue > 40) {
          if (size.innerHTML.replace(/\s+/g, "") === "S") {
            size.classList.add("active");
          } else {
            size.classList.remove("active");
          }
        } else {
          if (size.innerHTML.replace(/\s+/g, "") === "XS") {
            size.classList.add("active");
          } else {
            size.classList.remove("active");
          }
        }
      });
    });
  });
}

// Create product item
const createProductItem = (product) => {
  const productItem = document.createElement("div");
  productItem.classList.add("product-item", "grid-type");
  productItem.setAttribute("data-item", product.id);
  productItem.setAttribute("data-slug", product.slug);

  let productTags = "";
  if (product.new) {
    productTags += `<div class="product-tag text-button-uppercase bg-green px-3 py-0.5 inline-block rounded-full absolute top-3 left-3 z-[1]">New</div>`;
  }
  if (product.sale) {
    productTags += `<div class="product-tag text-button-uppercase text-white bg-red px-3 py-0.5 inline-block rounded-full absolute top-3 left-3 z-[1]">Sale</div>`;
  }
  if (product.outOfStock) {
    productTags += `<div class="product-tag text-button-uppercase text-white bg-black px-3 py-0.5 inline-block rounded-full absolute top-3 left-3 z-[1]">Out of Stock</div>`;
  }

  let productImages = "";
  product.thumbImage.slice(0, 2).forEach((img, index) => {
    productImages += `<img key="${index}" class="w-full h-full object-cover duration-700" src="${img}" alt="img" loading="lazy">`;
  });

  productItem.innerHTML = `
        <div class="product-main cursor-pointer block" data-item="${product.id
    }">
            <div class="product-thumb bg-white relative overflow-hidden rounded-2xl">
                ${productTags}
                <div class="list-action-right absolute top-3 right-3 max-lg:hidden">
                    <div
                        class="add-wishlist-btn w-[32px] h-[32px] flex items-center justify-center rounded-full bg-white duration-300 relative">
                        <div class="tag-action bg-black text-white caption2 px-1.5 py-0.5 rounded-sm">
                            Add To Wishlist</div>
                        <i class="ph ph-heart text-lg"></i>
                    </div>
                    <div
                        class="similar-product-btn w-[32px] h-[32px] flex items-center justify-center rounded-full bg-white duration-300 relative mt-2"
                        data-category="${product.category}"
                    >
                        <div class="tag-action bg-black text-white caption2 px-1.5 py-0.5 rounded-sm" style="width: max-content;">
                            Similar Products</div>
                        <i class="ph ph-intersect text-lg"></i>
                    </div>
                    <div
                        class="hidden compare-btn w-[32px] h-[32px] flex items-center justify-center rounded-full bg-white duration-300 relative mt-2">
                        <div class="tag-action bg-black text-white caption2 px-1.5 py-0.5 rounded-sm">
                            Compare Product</div>
                        <i class="ph ph-arrow-counter-clockwise text-lg compare-icon"></i>
                        <i class="ph ph-check-circle text-lg checked-icon"></i>
                    </div>
                </div>
                <div class="product-img w-full h-full aspect-[3/4]">
                    ${productImages}
                </div>
                ${product.sale
      ? `
                  <div class="countdown-time-block py-1.5 flex items-center justify-center">
                    <div class="text-xs font-semibold uppercase text-red">
                      <span class='countdown-day'>24</span>
                      <span>D : </span>
                      <span class='countdown-hour'>14</span>
                      <span>H : </span>
                      <span class='countdown-minute'>36</span>
                      <span>M : </span>
                      <span class='countdown-second'>51</span>
                      <span>S</span>
                    </div>
                  </div>
                `
      : ""
    }
                <div class="list-action grid grid-cols-1 gap-3 px-5 absolute w-full bottom-5">
                    <div
                        class="hidden  w-full text-button-uppercase py-2 text-center rounded-full duration-300 bg-white hover:bg-black hover:text-white">
                        <span class="hidden">Quick View</span>
                        <i class="ph ph-eye hidden text-xl"></i>
                        </div>
                        ${product.action === "add to cart"
      ? `
                            <div
                                class="add-cart-btn w-full text-button-uppercase py-2 text-center rounded-full duration-300 bg-white hover:bg-black hover:text-white"
                                >
                                <span class="max-lg:hidden">Add To Cart</span>
                                <i class="ph ph-shopping-bag-open lg:hidden text-xl"></i>
                            </div>
                        `
      : `
                            <div
                                class="hidden text-button-uppercase py-2 text-center rounded-full duration-500 bg-white hover:bg-black hover:text-white max-lg:hidden">
                                Quick Shop</div>
                            <div
                                class="hidden w-full text-button-uppercase py-2 text-center rounded-full duration-300 bg-white hover:bg-black hover:text-white lg:hidden"
                                >
                                <span class="max-lg:hidden">Add To Cart</span>
                                <i class="ph ph-shopping-bag-open lg:hidden text-xl"></i>
                            </div>
                            <div class="max-lg:hidden left-5 right-5 bg-white p-5 rounded-[20px] cartathome">
                                <div class=" choose-size mt-5">
                        <div class="heading flex items-center justify-between">
                            <div class="text-title">Quantity: <span class="text-title size"></span></div>
                        </div>
                        <div class="flex gap-2 ">
                            <div class="quantity-block md:p-3 max-md:py-1.5 max-md:px-3 flex items-center justify-between rounded-lg border border-line sm:w-[140px] w-[120px] flex-shrink-0" product_id="${product.id}">
                                <i class="ph-bold ph-minus productCard cursor-pointer body1" onclick="changeQuantity(${product.id}, this, false);"></i>
                                <div id="quantityINput${product.id}" class="quantity body1 font-semibold">1</div>
                                <i class="ph-bold ph-plus productCard cursor-pointer body1" onclick="changeQuantity(${product.id}, this, true);"></i>
                            </div>
                        </div>
                        <div class="list-size flex items-center gap-2 flex-wrap mt-3 hidden">
                            <!-- size-item -->
                        </div>
                    </div>
                    <div class="choose-quantity flex items-center max-xl:flex-wrap lg:justify-between gap-5 mt-3">

                        <div class="add-cart-btn button-main whitespace-nowrap w-full text-center  ">Add To Cart</div>
                    </div></div> `
    }
                </div>
            </div>
            <div class="product-infor mt-4 lg:mb-7">
                <div class="product-sold sm:pb-4 pb-2">
                    <div class="progress bg-line h-1.5 w-full rounded-full overflow-hidden relative">
                        <div class='progress-sold bg-red absolute left-0 top-0 h-full' style="width: ${Math.floor(
      (product.sold / product.quantity) * 100
    )}%">
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-3 gap-y-1 flex-wrap mt-2">
                        <div class="text-button-uppercase">
                            <span class='text-secondary2 max-sm:text-xs'>Sold:
                            </span>
                            <span class='max-sm:text-xs'>${product.sold}</span>
                        </div>
                        <div class="text-button-uppercase">
                            <span class='text-secondary2 max-sm:text-xs'>Available:
                            </span>
                            <span class='max-sm:text-xs'>${product.quantity - product.sold
    }</span>
                        </div>
                    </div>
                </div>
                <div class="product-name text-title duration-300">${product.name
    }</div>
                ${product.variation?.length > 0 &&
      product.action === "add to cart"
      ? `
                        <div class="list-color py-2 max-md:hidden flex items-center gap-3 flex-wrap duration-500">
                            ${product.variation
        ?.map(
          (item, index) =>
            `<div
                                    key="${index}"
                                    class="color-item w-8 h-8 rounded-full duration-300 relative"
                                    style="background-color:${item.colorCode};"
                                >
                                    <div class="tag-action bg-black text-white caption2 capitalize px-1.5 py-0.5 rounded-sm">${item.color}</div>
                                </div>
                                `
        )
        .join("")}
                        </div>`
      : `
                    <div class="hidden list-color list-color-image max-md:hidden flex items-center gap-3 flex-wrap duration-500">
                        ${product.variation
        ?.map(
          (item, index) =>
            `
                            <div
                                class="color-item w-12 h-12 rounded-xl duration-300 relative"
                                key="${index}"
                            >
                                <img
                                    src="${item.colorImage}"
                                    alt='color'
                                    class='rounded-xl w-full h-full object-cover'
                                />
                                <div class="tag-action bg-black text-white caption2 capitalize px-1.5 py-0.5 rounded-sm">${item.color}</div>
                            </div>
                        `
        )
        .join("")}
                    </div>
                `
    }
        <div
        class="product-price-block flex items-center gap-2 flex-wrap mt-1 duration-300 relative z-[1]">
        <div class="product-price text-title">₹${parseFloat(product.price).toFixed(2)
    }</div>
        ${Math.floor(100 - (product.price / product.originPrice) * 100) > 0
      ? `
                <div class="product-origin-price caption1 text-secondary2">
                    <del>₹${parseFloat(product.originPrice).toFixed(2)}</del>
                </div>
                <div
                    class="product-sale caption1 font-medium bg-green px-3 py-0.5 inline-block rounded-full">
                    -${Math.floor(
        100 - (product.price / product.originPrice) * 100
      )}%
                </div>
        `
      : ""
    }
            </div>
        </div>
        </div>
    </div>
    `;

  if (product.outOfStock) {
    const actionsBlock = productItem.querySelector(".list-action");
    if (actionsBlock) {
      actionsBlock.classList.add("pointer-events-none", "opacity-50");
      actionsBlock.setAttribute("aria-disabled", "true");
      actionsBlock.querySelectorAll("button, input, select, [role='button']").forEach((el) => {
        el.disabled = true;
      });
    }
  }

  return productItem;
};

function addEventToProductItem(products) {
  // Product item
  const productItems = document.querySelectorAll(".product-item");

  if (productItems) {
    productItems.forEach((product) => {
      const productId = product.getAttribute("data-item");
      product.addEventListener("click", (e) => {
        if (product.classList.contains("productDetails")) {
        } else if (product.classList.contains("quantity-block")) {
        } else {
          if (
            e.target.closest(".quantity-block") ||
            e.target.classList.contains("quantity") ||
            e.target.closest(".similar-product-btn")
          ) {
            e.stopPropagation();
            return;
          }
          const productSlug = product.getAttribute("data-slug");
          window.location.href = `${BASE_URL}product/${productSlug}`;
        }
      });

      const compareIcon = product.querySelector(".compare-btn");
      const addWishlistIcon = product.querySelector(".add-wishlist-btn");
      const addCartIcon = product.querySelectorAll(".add-cart-btn");
      const quickviewIcon = product.querySelector(".quick-view-btn");
      const quickshopIcon = product.querySelector(".quick-shop-btn");
      const buyNowBtn = product.querySelector(".buy-now-btn");
      const modalQuickshop = product.querySelector(".quick-shop-block");
      const similarProductIcon = product.querySelector(".similar-product-btn");

      if (similarProductIcon) {
        similarProductIcon.addEventListener("click", (e) => {
          e.stopPropagation();
          const category = similarProductIcon.getAttribute("data-category");
          openModalSimilarProduct(category);
        });
      }

      if (addWishlistIcon) {
        let wishlistStore = localStorage.getItem("wishlistStore");
        wishlistStore = wishlistStore ? JSON.parse(wishlistStore) : [];
        wishlistStore.forEach((prd) => {
          if (prd.id === productId) {
            addWishlistIcon.classList.add("active");
            addWishlistIcon.querySelector("i").classList.remove("ph");
            addWishlistIcon.querySelector("i").classList.add("ph-fill");
          }
        });

        addWishlistIcon.addEventListener("click", (e) => {
          e.stopPropagation();
          if (!requireLoginForWishlist()) return;
          // save prd to wishlist in local storage
          const productId = addWishlistIcon
            .closest(".product-item")
            .getAttribute("data-item");
          let wishlistStore = localStorage.getItem("wishlistStore");
          wishlistStore = wishlistStore ? JSON.parse(wishlistStore) : [];

          const existingIndex = wishlistStore.findIndex(
            (item) => item.id === productId
          );

          if (existingIndex > -1) {
            // If prd existed in wishlist, remove it from wishlist
            wishlistStore.splice(existingIndex, 1);
            addWishlistIcon.classList.remove("active");
            addWishlistIcon.querySelector("i").classList.add("ph");
            addWishlistIcon.querySelector("i").classList.remove("ph-fill");
          } else {
            // If prd not exist in wishlist, add it to wishlist
            const productToAdd = products?.find(
              (item) => item.id === productId
            );
            if (productToAdd) {
              wishlistStore.push(productToAdd);
              addWishlistIcon.classList.add("active");
              addWishlistIcon.querySelector("i").classList.remove("ph");
              addWishlistIcon.querySelector("i").classList.add("ph-fill");
              openModalWishlist();
            }
          }

          // Save wishlist to localStorage
          localStorage.setItem("wishlistStore", JSON.stringify(wishlistStore));
          handleItemModalWishlist();
        });
      }

      if (compareIcon) {
        let compareStore = localStorage.getItem("compareStore");
        compareStore = compareStore ? JSON.parse(compareStore) : [];
        compareStore.forEach((prd) => {
          if (prd.id === productId) {
            compareIcon.classList.add("active");
          } else {
            compareIcon.classList.remove("active");
          }
        });

        compareIcon.addEventListener("click", (e) => {
          e.stopPropagation();

          // save prd to compare in local storage
          const productId = compareIcon
            .closest(".product-item")
            .getAttribute("data-item");
          let compareStore = localStorage.getItem("compareStore");
          compareStore = compareStore ? JSON.parse(compareStore) : [];

          const existingIndex = compareStore.findIndex(
            (item) => item.id === productId
          );

          if (existingIndex > -1) {
            // If prd existed in compare, remove it from compare
            compareStore.splice(existingIndex, 1);
            compareIcon.classList.remove("active");
          } else {
            if (compareStore.length < 3) {
              // If prd not exist in compare, add it to compare
              const productToAdd = products?.find(
                (item) => item.id === productId
              );
              if (productToAdd) {
                compareStore.push(productToAdd);
                compareIcon.classList.add("active");
              }
            } else {
              alert("List compare product must be <= 3");
            }
          }

          // Save compare to localStorage
          localStorage.setItem("compareStore", JSON.stringify(compareStore));
          handleItemModalCompare();
          openModalCompare();
        });
      }

      if (quickviewIcon) {
        quickviewIcon.addEventListener("click", (e) => {
          e.stopPropagation();
          // save prd to quick view in local storage
          const productItem = quickviewIcon.closest(".product-item");
          const productId = productItem.getAttribute("data-item");
          let quickViewStore = localStorage.getItem("quickViewStore");
          quickViewStore = quickViewStore && [];

          // add it to quick view
          const productToAdd = products?.find((item) => item.id === productId);
          if (productToAdd) {
            quickViewStore.push(productToAdd);
          }

          // Save quickView to localStorage
          localStorage.setItem(
            "quickViewStore",
            JSON.stringify(quickViewStore)
          );
          handleItemModalQuickview();
          closeModalCart();
          openModalQuickview();
        });
      }

      if (addCartIcon) {
        addCartIcon.forEach((icon) =>
          icon.addEventListener("click", (e) => {
            e.stopPropagation();
            // save prd to cart in local storage
            const productItem = icon.closest(".product-item");
            const productId = productItem.getAttribute("data-item");

            const outOfStockCheck = products?.find((item) => item.id === productId);
            if (outOfStockCheck?.outOfStock) {
              Toast.show("This product is out of stock", {
                type: "warning",
                icon: true,
                duration: 4000,
              });
              return;
            }

            const quantitySize = document.querySelector(
              "#quantityINput" + productId
            ).innerHTML;
            const activeSizeElement = icon
              ?.closest(".quick-shop-block")
              ?.querySelector(".list-size .active");

            const getProudctSize = activeSizeElement
              ? activeSizeElement.getAttribute("key")
              : 0;

            // Get selected variant if on product detail page
            let variantId = null;
            let variantData = null;

            if (window.variantManager) {
              variantId = window.variantManager.getSelectedVariantId();
              variantData = window.variantManager.getSelectedVariantData();

              // Validate variant selection if variants exist
              if (window.variantManager.hasVariants() && !window.variantManager.isVariantSelected()) {
                Toast.show("Please select product options (size/color)", {
                  type: "warning",
                  icon: true,
                  duration: 4000,
                });
                return;
              }
            }

            let cartStore = localStorage.getItem("cartStore");
            cartStore = cartStore ? JSON.parse(cartStore) : [];

            // Check if product with same variant exists
            const existingIndex = cartStore.findIndex(
              (item) => item.id === productId && (item.variant_id || null) === (variantId || null)
            );

            if (existingIndex > -1) {
              // If prd existed in cart
              Toast.show("Product Already In Cart!", {
                type: "warning",
                icon: true,
                duration: 4000,
              });
              openModalCart();
            } else {
              // If prd not exist in cart, add it to cart
              const productToAdd = products?.find(
                (item) => item.id === productId
              );
              if (productToAdd) {
                productToAdd.quantityPurchase = Number(quantitySize);
                productToAdd.product_size = getProudctSize;
                productToAdd.variant_id = variantId;
                productToAdd.variant_data = variantData;


                // Use variant price if available
                if (variantData && variantData.price) {
                  productToAdd.price = parseFloat(variantData.price);
                  productToAdd.variantSize = variantData.size;
                  productToAdd.variantColor = variantData.color;
                }

                cartStore.push(productToAdd);
                if (window.fbq) {
                  fbq('track', 'AddToCart', {
                    content_ids: [String(productToAdd.id)],
                    content_type: 'product',
                    content_name: productToAdd.name,
                    value: (productToAdd.price || 0) * productToAdd.quantityPurchase,
                    currency: 'INR',
                  });
                }
                Toast.show("Product Added To Cart!", {
                  type: "success",
                  icon: true,
                  duration: 4000,
                });
                openModalCart();
              }
            }

            // Save cart to localStorage
            localStorage.setItem("cartStore", JSON.stringify(cartStore));

            // Save cart to Database

            fetch(`${BASE_URL}Cart/add`, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ cartStore }), // wrap the cartStore in an object
            })
              .then((response) => response.json())
              .then((data) => { })
              .catch((error) => {
                console.error("Error:", error);
              });

            handleItemModalCart();
          })
        );
      }

      if (buyNowBtn) {
        buyNowBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          const productId = product.getAttribute("data-item");

          const outOfStockCheck = products?.find((item) => item.id === productId);
          if (outOfStockCheck?.outOfStock) {
            Toast.show("This product is out of stock", {
              type: "warning",
              icon: true,
              duration: 4000,
            });
            return;
          }

          const quantitySizeElement = document.querySelector("#quantityINput" + productId);
          const quantitySize = quantitySizeElement ? quantitySizeElement.innerHTML : 1;

          // Get selected variant if on product detail page
          let variantId = null;
          let variantData = null;

          if (window.variantManager) {
            variantId = window.variantManager.getSelectedVariantId();
            variantData = window.variantManager.getSelectedVariantData();

            // Validate variant selection if variants exist
            if (window.variantManager.hasVariants() && !window.variantManager.isVariantSelected()) {
              Toast.show("Please select product options (size/color)", {
                type: "warning",
                icon: true,
                duration: 4000,
              });
              return;
            }
          }

          let cartStore = localStorage.getItem("cartStore");
          cartStore = cartStore ? JSON.parse(cartStore) : [];

          // Check if product with same variant exists
          const existingIndex = cartStore.findIndex(
            (item) => item.id === productId && (item.variant_id || null) === (variantId || null)
          );

          if (existingIndex === -1) {
            // If prd not exist in cart, add it to cart
            const productToAdd = products?.find(
              (item) => item.id === productId
            );
            if (productToAdd) {
              productToAdd.quantityPurchase = Number(quantitySize);
              productToAdd.variant_id = variantId;
              productToAdd.variant_data = variantData;

              // Use variant price if available
              if (variantData && variantData.price) {
                productToAdd.price = parseFloat(variantData.price);
                productToAdd.variantSize = variantData.size;
                productToAdd.variantColor = variantData.color;
              }

              cartStore.push(productToAdd);
              if (window.fbq) {
                fbq('track', 'AddToCart', {
                  content_ids: [String(productToAdd.id)],
                  content_type: 'product',
                  content_name: productToAdd.name,
                  value: (productToAdd.price || 0) * productToAdd.quantityPurchase,
                  currency: 'INR',
                });
              }
              localStorage.setItem("cartStore", JSON.stringify(cartStore));

              // Save to DB (optional for buy now as they are going to checkout, but good for persistence)
              fetch(`${BASE_URL}Cart/add`, {
                method: "POST",
                headers: {
                  "Content-Type": "application/json",
                },
                body: JSON.stringify({ cartStore }),
              }).catch((error) => console.error("Error:", error));
            }
          }

          // Redirect to checkout
          window.location.href = `${BASE_URL}checkout`;
        });
      }

      if (quickshopIcon) {
        quickshopIcon.addEventListener("click", (e) => {
          e.stopPropagation();
          modalQuickshop.classList.add("open");
        });

        if (addCartIcon) {
          addCartIcon.forEach((icon) =>
            icon.addEventListener("click", (e) => {
              e.stopPropagation();
              if (modalQuickshop.classList.contains("open")) {
                modalQuickshop.classList.remove("open");
              }
              openModalCart();
            })
          );
        }
      }
    });
  }

  handleActiveSizeChange();
  handleActiveColorChange();
}

// Active size
const handleActiveSizeChange = () => {
  // List size
  const listSizes = document.querySelectorAll(".list-size");
  listSizes.forEach((list) => {
    const sizeItems = list.querySelectorAll(".size-item");

    sizeItems.forEach((size) => {
      size.addEventListener("click", () => {
        let parent = size.parentElement;
        if (!parent.querySelector(".active")) {
          size.classList.add("active");
        } else {
          parent.querySelector(".active").classList.remove("active");
          size.classList.add("active");
        }
      });
    });

    list.addEventListener("click", (e) => {
      e.stopPropagation();
      const chooseSizeBlock = list.parentElement;
      const sizeSelected = chooseSizeBlock.querySelector(".size");
      const activeSize = list.querySelector(".size-item.active");

      if (sizeSelected && activeSize) {
        sizeSelected.textContent = activeSize.textContent;
      }
    });
  });
};

// Active size
const handleActiveColorChange = () => {
  // List color
  const listColors = document.querySelectorAll(".list-color");

  listColors.forEach((list) => {
    const colorItems = list.querySelectorAll(".color-item");

    colorItems.forEach((color) => {
      color.addEventListener("click", () => {
        let parent = color.parentElement;
        if (!parent.querySelector(".active")) {
          color.classList.add("active");
        } else {
          parent.querySelector(".active").classList.remove("active");
          color.classList.add("active");
        }
      });
    });

    list.addEventListener("click", (e) => {
      e.stopPropagation();
      const chooseColorBlock = list.parentElement;
      const colorSelected = chooseColorBlock.querySelector(".color");
      const activeColor = list.querySelector(".color-item.active .tag-action");

      if (colorSelected && activeColor) {
        colorSelected.textContent = activeColor.textContent;
      }
    });
  });
};

// filter product img in home6, product detail
const filterProductImg = document.querySelector(".filter-product-img");

if (filterProductImg) {
  fetch(`${BASE_URL}Test/products`)
    .then((response) => response.json())
    .then((data) => {
      const prdId = filterProductImg
        .querySelector(".product-infor")
        .getAttribute("data-item");
      let productMain = data.find((product) => product.id === prdId);
      const colorItems = filterProductImg.querySelectorAll(".color-item");

      colorItems.forEach((colorItem) => {
        colorItem.addEventListener("click", () => {
          const selectedColor = colorItem
            .querySelector(".tag-action")
            .textContent.trim();
          const selectedVariation = productMain.variation.find(
            (variation) => variation.color === selectedColor
          );
          const selectedImage = selectedVariation.image;

          const swiperSlides =
            filterProductImg.querySelectorAll(".swiper-slide");
          let targetIndex = -1;

          swiperSlides.forEach((slide, index) => {
            const imgSrc = slide.querySelector("img").getAttribute("src");
            if (imgSrc === selectedImage) {
              targetIndex = index;
              if (document.querySelector(".product-detail")) {
                targetIndex = index - 4;
              }
              if (document.querySelector(".underwear")) {
                targetIndex = index - 4;
              }
              return; // stop loop when found index
            }
          });

          if (targetIndex !== -1) {
            if (document.querySelector(".swiper-img-scroll"))
              swiperScrollImg.slideTo(targetIndex); // scroll slide to index
            if (document.querySelector(".underwear .mySwiper2"))
              swiper2.slideTo(targetIndex); // scroll slide to index
            if (document.querySelector(".product-detail .mySwiper2"))
              swiper2.slideTo(targetIndex); // scroll slide to index
          } else {
            console.log("Can not find Image :", selectedImage);
          }
        });
      });
    })
    .catch((error) => console.error("Error fetching products:", error));
}

// Change product img when active color in list color
const handleActiveImgWhenColorChange = (products) => {
  const listColors = document.querySelectorAll(".list-color");

  listColors.forEach((list) => {
    const colorItems = list.querySelectorAll(".color-item");

    colorItems.forEach((color) => {
      color.addEventListener("click", () => {
        const activeColor = color.querySelector(".tag-action")?.textContent;
        const productMain = color.closest(".product-main");
        const dataItem = productMain?.getAttribute("data-item");
        const product = products.find((item) => item.id === dataItem);
        const imgActive = product?.variation.find(
          (item) => item.color === activeColor
        ).image;
        if (imgActive) {
          productMain.querySelector(".product-img img").remove();
          productMain.querySelector(".product-img").innerHTML = `
                            <img src="${imgActive}" alt="img" class="w-full h-full object-cover duration-700" />
                        `;
        }
      });
    });
  });
};

// Append child
const listFourProduct = document.querySelectorAll(".list-product.four-product");
const listSixProduct = document.querySelector(
  ".list-product.six-product .swiper .swiper-wrapper"
);
const listEightProduct = document.querySelector(".list-product.eight-product");
const listThreeProduct = document.querySelectorAll(
  ".list-product.three-product"
);

// === Home page ===

// Fetch products from JSON file (assuming products.json)
const getSkelitonItems = document.querySelectorAll('.skeleton-item');
function removeSkeliton() {
  if (getSkelitonItems) {
    getSkelitonItems.forEach(element => {
      element.style.display = "None"
    });
  }
}
fetch(`${BASE_URL}Test/products`)
  .then((response) => response.json())
  .then((products) => {
    if (listFourProduct) {
      listFourProduct.forEach((list) => {
        const parent = list.parentElement;
        if (parent.querySelector(".menu-tab .active")) {
          const menuItemActive = parent
            .querySelector(".menu-tab .active")
            .getAttribute("data-item");
          const menuItems = parent.querySelectorAll(".menu-tab .tab-item");

          products
            .filter((product) => product.type === menuItemActive)
            .slice(0, 5)
            .forEach((product) => {
              const productElement = createProductItem(product);
              list.appendChild(productElement);
              removeSkeliton()
            });

          if (list.getAttribute("data-type") === "underwear") {
            if (menuItemActive === "best sellers") {
              products
                .filter(
                  (product) =>
                    product.type === "underwear" || product.type === "swimwear"
                )
                .sort((a, b) => b.sold - a.sold)
                .slice(0, 5)
                .forEach((product) => {
                  const productElement = createProductItem(product);
                  list.appendChild(productElement);
                  removeSkeliton()
                });
            }
          }

          menuItems.forEach((item) => {
            item.addEventListener("click", () => {
              // remove old product
              const productItems = list.querySelectorAll(".product-item");
              productItems.forEach((prdItem) => {
                prdItem.remove();
              });

              if (list.getAttribute("data-type") === "underwear") {
                if (item.getAttribute("data-item") === "best sellers") {
                  products
                    .filter(
                      (product) =>
                        product.type === "underwear" ||
                        product.type === "swimwear"
                    )
                    .sort((a, b) => b.sold - a.sold)
                    .slice(0, 5)
                    .forEach((product) => {
                      const productElement = createProductItem(product);
                      list.appendChild(productElement);
                      removeSkeliton()
                    });
                }

                if (item.getAttribute("data-item") === "on sale") {
                  products
                    .filter(
                      (product) =>
                        product.sale &&
                        (product.type === "underwear" ||
                          product.type === "swimwear")
                    )
                    .slice(0, 5)
                    .forEach((product) => {
                      const productElement = createProductItem(product);
                      list.appendChild(productElement);
                      removeSkeliton()
                    });
                }

                if (item.getAttribute("data-item") === "new arrivals") {
                  products
                    .filter(
                      (product) =>
                        product.new &&
                        (product.type === "underwear" ||
                          product.type === "swimwear")
                    )
                    .slice(0, 5)
                    .forEach((product) => {
                      const productElement = createProductItem(product);
                      list.appendChild(productElement);
                      removeSkeliton()
                    });
                }
              } else {
                products
                  .filter(
                    (product) => product.type === item.getAttribute("data-item")
                  )
                  .slice(0, 5)
                  .forEach((product) => {
                    // create product
                    const productElement = createProductItem(product);
                    list.appendChild(productElement);
                    removeSkeliton()
                  });
              }

              handleActiveImgWhenColorChange(products);
              addEventToProductItem(products);
            });
          });
        } else {
          products.slice(0, 5).forEach((product) => {
            const productElement = createProductItem(product);
            list.appendChild(productElement);
            removeSkeliton()
          });
        }
      });
    }

    // Display the first 6 products
    if (listSixProduct) {
      const parent = listSixProduct.parentElement.parentElement.parentElement;
      if (parent.querySelector(".menu-tab .active")) {
        const menuItemActive = parent
          .querySelector(".menu-tab .active")
          .getAttribute("data-item");
        const menuItems = parent.querySelectorAll(".menu-tab .tab-item");

        if (menuItemActive === "best sellers") {
          if (listSixProduct.getAttribute("data-type")) {
            products
              .filter(
                (product) =>
                  product.category === listSixProduct.getAttribute("data-type")
              )
              .sort((a, b) => b.sold - a.sold)
              .slice(0, 6)
              .forEach((product) => {
                const swiperSlide = document.createElement("div");
                swiperSlide.classList.add("swiper-slide");
                swiperSlide.appendChild(createProductItem(product));
                listSixProduct.appendChild(swiperSlide);
              });
          } else {
            products
              .filter((product) => product.category === "fashion")
              .sort((a, b) => b.sold - a.sold)
              .slice(0, 6)
              .forEach((product) => {
                const swiperSlide = document.createElement("div");
                swiperSlide.classList.add("swiper-slide");
                swiperSlide.appendChild(createProductItem(product));
                listSixProduct.appendChild(swiperSlide);
              });
          }
        }

        menuItems.forEach((item) => {
          item.addEventListener("click", () => {
            const productItems =
              listSixProduct.querySelectorAll(".swiper-slide");

            if (listSixProduct.getAttribute("data-type")) {
              if (item.getAttribute("data-item") === "best sellers") {
                products
                  .filter(
                    (product) =>
                      product.category ===
                      listSixProduct.getAttribute("data-type")
                  )
                  .sort((a, b) => b.sold - a.sold)
                  .slice(0, 6)
                  .forEach((product) => {
                    const swiperSlide = document.createElement("div");
                    swiperSlide.classList.add("swiper-slide");
                    swiperSlide.appendChild(createProductItem(product));
                    listSixProduct.appendChild(swiperSlide);
                  });
              }
              if (item.getAttribute("data-item") === "on sale") {
                products
                  .filter(
                    (product) =>
                      product.sale &&
                      product.category ===
                      listSixProduct.getAttribute("data-type")
                  )
                  .slice(0, 6)
                  .forEach((product) => {
                    const swiperSlide = document.createElement("div");
                    swiperSlide.classList.add("swiper-slide");
                    swiperSlide.appendChild(createProductItem(product));
                    listSixProduct.appendChild(swiperSlide);
                  });
              }
              if (item.getAttribute("data-item") === "new arrivals") {
                products
                  .filter(
                    (product) =>
                      product.new &&
                      product.category ===
                      listSixProduct.getAttribute("data-type")
                  )
                  .slice(0, 6)
                  .forEach((product) => {
                    const swiperSlide = document.createElement("div");
                    swiperSlide.classList.add("swiper-slide");
                    swiperSlide.appendChild(createProductItem(product));
                    listSixProduct.appendChild(swiperSlide);
                  });
              }
            } else {
              if (item.getAttribute("data-item") === "best sellers") {
                products
                  .filter((product) => product.category === "fashion")
                  .sort((a, b) => b.sold - a.sold)
                  .slice(0, 6)
                  .forEach((product) => {
                    const swiperSlide = document.createElement("div");
                    swiperSlide.classList.add("swiper-slide");
                    swiperSlide.appendChild(createProductItem(product));
                    listSixProduct.appendChild(swiperSlide);
                  });
              }
              if (item.getAttribute("data-item") === "on sale") {
                products
                  .filter(
                    (product) => product.sale && product.category === "fashion"
                  )
                  .slice(0, 6)
                  .forEach((product) => {
                    const swiperSlide = document.createElement("div");
                    swiperSlide.classList.add("swiper-slide");
                    swiperSlide.appendChild(createProductItem(product));
                    listSixProduct.appendChild(swiperSlide);
                  });
              }
              if (item.getAttribute("data-item") === "new arrivals") {
                products
                  .filter(
                    (product) => product.new && product.category === "fashion"
                  )
                  .slice(0, 6)
                  .forEach((product) => {
                    const swiperSlide = document.createElement("div");
                    swiperSlide.classList.add("swiper-slide");
                    swiperSlide.appendChild(createProductItem(product));
                    listSixProduct.appendChild(swiperSlide);
                  });
              }
            }

            // remove old product
            productItems.forEach((prdItem) => {
              prdItem.remove();
            });

            handleActiveImgWhenColorChange(products);
            addEventToProductItem(products);
          });
        });
      } else {
        if (listSixProduct.getAttribute("data-type")) {
          products
            .filter(
              (product) =>
                product.category === listSixProduct.getAttribute("data-type")
            )
            .slice(0, 6)
            .forEach((product) => {
              const swiperSlide = document.createElement("div");
              swiperSlide.classList.add("swiper-slide");
              swiperSlide.appendChild(createProductItem(product));
              listSixProduct.appendChild(swiperSlide);
            });
        } else {
          products.slice(5, 11).forEach((product) => {
            const swiperSlide = document.createElement("div");
            swiperSlide.classList.add("swiper-slide");
            swiperSlide.appendChild(createProductItem(product));
            listSixProduct.appendChild(swiperSlide);
          });
        }
      }
    }

    // Display the first 8 products
    if (listEightProduct) {
      const parent = listEightProduct.parentElement;
      if (parent.querySelector(".menu-tab .active")) {
        const menuItemActive = parent
          .querySelector(".menu-tab .active")
          .getAttribute("data-item");
        const menuItems = parent.querySelectorAll(".menu-tab .tab-item");

        if (menuItemActive === "best sellers") {
          products
            .filter((product) => product.category === "fashion")
            .sort((a, b) => b.sold - a.sold)
            .slice(0, 8)
            .forEach((product) => {
              const productElement = createProductItem(product);
              listEightProduct.appendChild(productElement);
            });
        }
        menuItems.forEach((item) => {
          item.addEventListener("click", () => {
            // remove old product
            const productItems =
              listEightProduct.querySelectorAll(".product-item");
            productItems.forEach((prdItem) => {
              prdItem.remove();
            });

            if (item.getAttribute("data-item") === "best sellers") {
              products
                .filter((product) => product.category === "fashion")
                .sort((a, b) => b.sold - a.sold)
                .slice(0, 8)
                .forEach((product) => {
                  const productElement = createProductItem(product);
                  listEightProduct.appendChild(productElement);
                });
            }
            if (item.getAttribute("data-item") === "on sale") {
              products
                .filter(
                  (product) => product.sale && product.category === "fashion"
                )
                .slice(0, 8)
                .forEach((product) => {
                  const productElement = createProductItem(product);
                  listEightProduct.appendChild(productElement);
                });
            }
            if (item.getAttribute("data-item") === "new arrivals") {
              products
                .filter(
                  (product) => product.new && product.category === "fashion"
                )
                .slice(0, 8)
                .forEach((product) => {
                  const productElement = createProductItem(product);
                  listEightProduct.appendChild(productElement);
                });
            }

            handleActiveImgWhenColorChange(products);
            addEventToProductItem(products);
          });
        });
      } else {
        if (listEightProduct.getAttribute("data-type")) {
          products
            .filter(
              (product) =>
                product.category === listEightProduct.getAttribute("data-type")
            )
            .slice(0, 8)
            .forEach((product) => {
              const productElement = createProductItem(product);
              listEightProduct.appendChild(productElement);
            });
        } else {
          products.slice(11, 19).forEach((product) => {
            const productElement = createProductItem(product);
            listEightProduct.appendChild(productElement);
          });
        }
      }
    }

    // Display 3 products(Home 11)s
    if (listThreeProduct) {
      listThreeProduct.forEach((list) => {
        const parent = list.parentElement;
        const gender = list.getAttribute("data-gender");
        const menuItemActive = parent
          .querySelector(".menu-tab .active")
          .getAttribute("data-item");
        const menuItems = parent.querySelectorAll(".menu-tab .tab-item");

        products
          .filter(
            (product) =>
              product.gender === gender && product.type === menuItemActive
          )
          .slice(0, 3)
          .forEach((product) => {
            const productElement = createProductItem(product);
            list.appendChild(productElement);
          });

        menuItems.forEach((item) => {
          item.addEventListener("click", () => {
            // remove old product
            const productItems = list.querySelectorAll(".product-item");
            productItems.forEach((prdItem) => {
              prdItem.remove();
            });

            products
              .filter(
                (product) =>
                  product.gender === gender &&
                  product.type === item.getAttribute("data-item")
              )
              .slice(0, 3)
              .forEach((product) => {
                // create product
                const productElement = createProductItem(product);
                list.appendChild(productElement);
              });

            handleActiveImgWhenColorChange(products);
            addEventToProductItem(products);
          });
        });
      });
    }

    handleActiveImgWhenColorChange(products);
    addEventToProductItem(products);
  })
  .catch((error) => console.error("Error loading products:", error));

// create product marketplace
// Create product item
const createProductItemMarketplace = (product) => {
  const productItem = document.createElement("div");
  productItem.classList.add(
    "product-item",
    "style-marketplace",
    "p-4",
    "border",
    "border-line",
    "rounded-2xl"
  );
  productItem.setAttribute("data-item", product.id);

  let productTags = "";
  if (product.sale) {
    productTags += `<div class="product-tag text-button-uppercase text-white bg-red px-3 py-0.5 inline-block rounded-full absolute top-3 left-3 z-[1]">Sale</div>`;
  }

  let arrOfStar = "";
  for (let i = 0; i < 5; i++) {
    if (product.rate) {
      if (i >= product.rate) {
        arrOfStar += '<i class="ph-fill ph-star text-sm text-secondary"></i>';
      } else {
        arrOfStar += '<i class="ph-fill ph-star text-sm text-yellow"></i>';
      }
    }
  }

  productItem.innerHTML = `
        <div class="bg-img relative w-full aspect-1/1">
                        <img src=${product.thumbImage[0]} alt="">
                        <div class="list-action flex flex-col gap-1 absolute top-0 right-0">
                            <span
                                class="add-wishlist-btn w-8 h-8 bg-white flex items-center justify-center rounded-full box-shadow-sm duration-300">
                                <i class="ph ph-heart"></i>
                            </span>
                            <span
                                class="hidden compare-btn w-8 h-8 bg-white flex items-center justify-center rounded-full box-shadow-sm duration-300">
                                <i class="ph ph-repeat"></i>
                            </span>
                            <span
                                class="quick-view-btn w-8 h-8 bg-white flex items-center justify-center rounded-full box-shadow-sm duration-300">
                                <i class="ph ph-eye"></i>
                            </span>
                            <span
                                class="add-cart-btn w-8 h-8 bg-white flex items-center justify-center rounded-full box-shadow-sm duration-300">
                                <i class="ph ph-shopping-bag-open"></i>
                            </span>
                            <span
                                class="similar-product-btn w-8 h-8 bg-white flex items-center justify-center rounded-full box-shadow-sm duration-300"
                                data-category="${product.category}"
                            >
                                <i class="ph ph-intersect"></i>
                            </span>
                        </div>
                    </div>
                    <div class="product-infor mt-4">
                        <span class="text-title">${product.name}</span>
                        <div class="flex gap-0.5 mt-1">
                            ${arrOfStar}
                        </div>
                        <span class="text-title inline-block mt-1">₹${parseFloat(product.price).toFixed(2)}</span>
                    </div>
    `;

  return productItem;
};

// fetch product in marketplace
if (document.querySelector(".tab-features-block.style-marketplace")) {
  fetch(`${BASE_URL}Test/products`)
    .then((response) => response.json())
    .then((products) => {
      // Display the first 4 products
      const listProduct = document.querySelector(
        ".tab-features-block.style-marketplace .list-product"
      );

      if (listProduct) {
        const parent = listProduct.parentElement;
        if (parent.querySelector(".menu-tab .active")) {
          const menuItemActive = parent
            .querySelector(".menu-tab .active")
            .getAttribute("data-item");
          const menuItems = parent.querySelectorAll(".menu-tab .tab-item");

          products
            .filter((product) => product.category === menuItemActive)
            .slice(0, 5)
            .forEach((product) => {
              const productElement = createProductItemMarketplace(product);
              listProduct.appendChild(productElement);
            });

          menuItems.forEach((item) => {
            item.addEventListener("click", () => {
              // remove old product
              const productItems =
                listProduct.querySelectorAll(".product-item");
              productItems.forEach((prdItem) => {
                prdItem.remove();
              });

              products
                .filter(
                  (product) =>
                    product.category === item.getAttribute("data-item")
                )
                .slice(0, 5)
                .forEach((product) => {
                  // create product
                  const productElement = createProductItemMarketplace(product);
                  listProduct.appendChild(productElement);
                });

              addEventToProductItem(products);
            });
          });
        } else {
          products.slice(0, 5).forEach((product) => {
            const productElement = createProductItemMarketplace(product);
            listProduct.appendChild(productElement);
          });
        }
      }

      addEventToProductItem(products);
    })
    .catch((error) => console.error("Error loading products:", error));
}

// Featured product underwear
const handleQuantity = () => {
  const quantityBlock = document.querySelectorAll(".quantity-block");

  // Debounce utility function
  function debounce(func, wait) {
    let timeout;
    return function (...args) {
      const context = this;
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(context, args), wait);
    };
  }

  async function getProductPrice(product_id) {
    try {
      const response = await fetch(
        `${BASE_URL}Web/ProductPrice?product_id=${product_id}`,
        {
          method: "GET",
        }
      );
      const data = await response.json();
      return data;
    } catch (error) {
      console.error("Error:", error);
    }
  }

  function updateProductDetailPrice(pramount, prQuantity) {
    const validPrAmount = Number(pramount);
    const validPrQuantity = Number(prQuantity);

    if (isNaN(validPrAmount) || isNaN(validPrQuantity)) {
      console.error("Invalid number input:", validPrAmount, validPrQuantity);
      return;
    }

    document.querySelector(".product-price").innerHTML = `₹${(
      validPrAmount * validPrQuantity
    ).toFixed(2)}`;
    document.querySelector(
      "#selectedQuantityLabel"
    ).innerHTML = `/${validPrQuantity}`;
  }
  quantityBlock.forEach((item) => {
    const product_id = item.getAttribute("product_id");
    const minus = item.querySelector(".ph-minus");
    const plus = item.querySelector(".ph-plus");
    const quantity = item.querySelector(".quantity");
    let currentQuantity = Number(quantity.textContent);
    // Debounced functions for API and price updates
    const debouncedUpdatePrice = debounce(async () => {
      const prPrice = await getProductPrice(product_id);
      if (prPrice && !isNaN(prPrice.price)) {
        updateProductDetailPrice(prPrice.price, currentQuantity);
      }
    }, 300);

    minus.addEventListener("click", (e) => {
      e.stopPropagation();
      if (currentQuantity > 1) {
        currentQuantity -= 1;
        quantity.innerHTML = currentQuantity;

        if (currentQuantity <= 1) {
          minus.classList.add("disabled");
        }
        if (!e.target.classList.contains("productCard")) {
          debouncedUpdatePrice();
        }
      }
    });

    plus.addEventListener("click", (e) => {
      e.stopPropagation();
      currentQuantity += 1;
      quantity.innerHTML = currentQuantity;

      if (currentQuantity > 1) {
        minus.classList.remove("disabled");
      }
      if (!e.target.classList.contains("productCard")) {
        debouncedUpdatePrice();
      }
    });
  });
};

function changeQuantity(product_id, buttonElement, isIncrease) {
  const quantityElement = buttonElement.closest(".quantity-block").querySelector(".quantity");
  let currentQuantity = Number(quantityElement.textContent);

  if (isIncrease) {
    currentQuantity += 1;
  } else if (currentQuantity > 1) {
    currentQuantity -= 1;
  }

  quantityElement.innerHTML = currentQuantity;

  // Handle disable state for minus button
  const minusButton = buttonElement.closest(".quantity-block").querySelector(".ph-minus");
  if (currentQuantity <= 1) {
    minusButton.classList.add("disabled");
  } else {
    minusButton.classList.remove("disabled");
  }
}
document.addEventListener("DOMContentLoaded", () => {
  handleQuantity();
});

const blogItems = document.querySelectorAll(".blog-item");

blogItems.forEach((blog) => {
  // redirect to detail
  blog.addEventListener("click", () => {
    const blogId = blog.getAttribute("data-item");
    window.location.href = `blog-detail1.html?id=${blogId}`;
  });
});

// list-testimonial
if (document.querySelector(".swiper-list-testimonial")) {
  var swiperListTestimonial = new Swiper(".swiper-list-testimonial", {
    pagination: { clickable: true, el: ".swiper-pagination" },
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    touchEventsTarget: "wrapper",
    slidesPerView: 1,
    spaceBetween: 0,
    breakpoints: {
      640: {
        slidesPerView: 2,
        spaceBetween: 16,
      },
      768: {
        slidesPerView: 2,
        spaceBetween: 16,
      },
      1280: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
    },
  });
}

const handleSlideActive = () => {
  let activeItem = document.querySelector(
    ".list-testimonial .swiper .swiper-slide-active"
  );
  if (activeItem) {
    const dataItem = activeItem.getAttribute("data-item");

    const listAvatar = document.querySelector(".list-avatar");
    const avatars = document.querySelectorAll(".list-avatar .bg-img");

    avatars.forEach((item) => {
      if (item.getAttribute("data-item") === dataItem) {
        if (listAvatar.querySelector(".active")) {
          listAvatar.querySelector(".active").classList.remove("active");
        }
        item.classList.add("active");
      }
    });
  }
};

handleSlideActive();

// list-testimonial 4
var swiperListTestimonialFour = new Swiper(".swiper-testimonial-four", {
  navigation: {
    prevEl: ".swiper-button-prev",
    nextEl: ".swiper-button-next",
  },
  autoplay: {
    delay: 3000,
  },
  loop: true,
  slidesPerView: 1,
  spaceBetween: 0,
  on: {
    slideChange: () => {
      handleSlideActive();
    },
  },
});

// list-testimonial yoga
if (document.querySelector(".list-testimonial-yoga")) {
  $(".list-testimonial-yoga").slick({
    dots: false,
    arrows: false,
    infinite: true,
    centerMode: true,
    centerPadding: "220px",
    speed: 300,
    autoplay: true,
    autoplaySpeed: 5000,
    slidesToShow: 3,
    slidesToScroll: 3,
    touchThreshold: 100,
    swipe: true,
    swipeToSlide: true,
    draggable: true,
    useTransform: false,
    responsive: [
      {
        breakpoint: 1600,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          centerPadding: "120px",
        },
      },
      {
        breakpoint: 1400,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
          centerPadding: "160px",
        },
      },
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          centerPadding: "160px",
        },
      },
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          centerPadding: "16px",
        },
      },
    ],
  });
}

// list-instagram
var swiperListInstagram = new Swiper(".swiper-list-instagram", {
  pagination: { clickable: true, el: ".swiper-pagination" },
  loop: true,
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  slidesPerView: 2,
  spaceBetween: 12,
  breakpoints: {
    640: {
      slidesPerView: 3,
      spaceBetween: 12,
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 16,
    },
    1024: {
      slidesPerView: 4,
      spaceBetween: 16,
    },
    1280: {
      slidesPerView: 5,
      spaceBetween: 16,
    },
  },
});

// list-instagram 3
var swiperListInstagram = new Swiper(".swiper-instagram-three", {
  loop: true,
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  clickable: true,
  slidesPerView: 2,
  spaceBetween: 0,
  breakpoints: {
    640: {
      slidesPerView: 3,
    },
    768: {
      slidesPerView: 4,
    },
    1024: {
      slidesPerView: 5,
    },
    1280: {
      slidesPerView: 6,
    },
  },
});

// list-brand
var swiperListBrand = new Swiper(".swiper-list-brand", {
  pagination: { clickable: true, el: ".swiper-pagination" },
  loop: true,
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  slidesPerView: 2,
  spaceBetween: 12,
  breakpoints: {
    640: {
      slidesPerView: 3,
      spaceBetween: 12,
    },
    768: {
      slidesPerView: 4,
      spaceBetween: 16,
    },
    1024: {
      slidesPerView: 5,
      spaceBetween: 16,
    },
    1280: {
      slidesPerView: 6,
      spaceBetween: 16,
    },
  },
});

// list-five-brand
var swiperListBrand = new Swiper(".swiper-list-five-brand", {
  pagination: { clickable: true, el: ".swiper-pagination" },
  loop: true,
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  slidesPerView: 2,
  spaceBetween: 12,
  breakpoints: {
    640: {
      slidesPerView: 3,
      spaceBetween: 12,
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 16,
    },
    1024: {
      slidesPerView: 4,
      spaceBetween: 16,
    },
    1280: {
      slidesPerView: 5,
      spaceBetween: 16,
    },
  },
});

// Before After Cosmetic1
const imageComparisonSlider = document.querySelector(
  '[data-component="image-comparison-slider"]'
);

function setSliderstate(e, element) {
  const sliderRange = element.querySelector("[data-image-comparison-range]");

  if (e.type === "input") {
    sliderRange.classList.add("image-comparison__range--active");
    return;
  }

  sliderRange.classList.remove("image-comparison__range--active");
  element.removeEventListener("mousemove", moveSliderThumb);
}

function moveSliderThumb(e) {
  const sliderRange = document.querySelector("[data-image-comparison-range]");
  const thumb = document.querySelector("[data-image-comparison-thumb]");
  let position = e.layerY - 20;

  if (e.layerY <= sliderRange.offsetTop) {
    position = -20;
  }

  if (e.layerY >= sliderRange.offsetHeight) {
    position = sliderRange.offsetHeight - 20;
  }

  thumb.style.top = `${position}px`;
}

function moveSliderRange(e, element) {
  const value = e.target.value;
  const slider = element.querySelector("[data-image-comparison-slider]");
  const imageWrapperOverlay = element.querySelector(
    "[data-image-comparison-overlay]"
  );

  slider.style.left = `${value}%`;
  imageWrapperOverlay.style.width = `${value}%`;

  element.addEventListener("mousemove", moveSliderThumb);
  setSliderstate(e, element);
}

function init(element) {
  const sliderRange = element.querySelector("[data-image-comparison-range]");

  if (sliderRange) {
    if ("ontouchstart" in window === false) {
      sliderRange.addEventListener("mouseup", (e) =>
        setSliderstate(e, element)
      );
      sliderRange.addEventListener("mousedown", moveSliderThumb);
    }

    sliderRange.addEventListener("input", (e) => moveSliderRange(e, element));
    sliderRange.addEventListener("change", (e) => moveSliderRange(e, element));
  }
}

if (imageComparisonSlider) {
  init(imageComparisonSlider);
}

// Change active category
const categoryItems = document.querySelectorAll(
  ".list-category .category-item"
);
const filterItems = document.querySelectorAll(".list-filter .filter-item");

if (categoryItems) {
  categoryItems.forEach((category) => {
    category.addEventListener("click", () => {
      filterItems.forEach((item) => {
        if (
          item.getAttribute("data-item") === category.getAttribute("data-item")
        ) {
          category
            .closest(".list-category")
            .querySelector(".category-item.active")
            .classList.remove("active");
          category.classList.add("active");
          item
            .closest(".list-filter")
            .querySelector(".filter-item.active")
            .classList.remove("active");
          item.classList.add("active");
        }
      });
    });
  });
}

const tabBtn = document.querySelectorAll(".tab_btn");
const formAddress = document.querySelectorAll(".form_address");

if (tabBtn) {
  tabBtn.forEach((btn) => {
    btn.addEventListener("click", () => {
      formAddress.forEach((form) => {
        if (form.getAttribute("data-item") === btn.getAttribute("data-item")) {
          btn
            .closest(".tab_address")
            .querySelector(".tab_btn.active")
            .classList.remove("active");
          btn.classList.add("active");
          form
            .closest(".tab_address")
            .querySelector(".form_address.active")
            .classList.remove("active");
          form.classList.add("active");
        }
      });
    });
  });
}

// Modal Order detail
const btnOrderDetail = document.querySelectorAll(".btn_order_detail");
const modalOrderDetail = document.querySelector(".modal-order-detail-block");
const modalOrderDetailMain = document.querySelector(
  ".modal-order-detail-block .modal-order-detail-main"
);

if (btnOrderDetail && modalOrderDetail) {
  btnOrderDetail.forEach((btn) => {
    btn.addEventListener("click", () => {
      const orderId = btn.getAttribute("order-id");

      fetch(`${BASE_URL}/Web/user_order_details/${orderId}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            populateOrderDetailsModal(data.orderDetails);
            modalOrderDetailMain.classList.add("open");
          } else {
            alert("Failed to fetch order details.");
          }
        })
        .catch((error) => {
          console.error("Error fetching order details:", error);
          alert("An error occurred while fetching order details.");
        });

      modalOrderDetailMain.classList.add("open");
    });
  });

  function populateOrderDetailsModal(orderDetails) {
    // Populate the modal with order details
    document.querySelector(".order_name").textContent = orderDetails.name;
    document.querySelector(".order_phone").textContent =
      orderDetails.contact_no;
    document.querySelector(".order_email").textContent = orderDetails.email;
    document.querySelector(".order_payment").textContent =
      orderDetails.payment_method == "0" ? "Online" : "CASH";
    const statusMap = {
      '0': 'New Order',
      '1': 'Order Accepted',
      '2': 'Order Cancelled',
      '3': 'Dispatched',
      '4': 'Completed'
    };
    document.querySelector(".order_status").textContent = statusMap[orderDetails.order_status] || 'Unknown';
    document.querySelector(".order_shipping_address").textContent =
      orderDetails.full_address;
    document.querySelector(".order_subtotal").textContent =
      "₹" + parseFloat(orderDetails.subtotal || 0).toFixed(2);
    document.querySelector(".order_ship").textContent =
      orderDetails.shipping_charge == 0
        ? "Free"
        : "₹" + parseFloat(orderDetails.shipping_charge).toFixed(2);
    document.querySelector(".order_packaging").textContent =
      "₹" + parseFloat(orderDetails.packaging_charge || 0).toFixed(2);
    document.querySelector(".order_total").textContent =
      "₹" + parseFloat(orderDetails.total_price || 0).toFixed(2);

    const couponRow = document.querySelector(".order_coupon_row");
    const couponSection = document.querySelector(".order_coupon_section");
    if (orderDetails.coupon_discount > 0) {
      couponRow.classList.remove("hidden");
      document.querySelector(".order_discounts").textContent =
        "-₹" + parseFloat(orderDetails.coupon_discount).toFixed(2);
    } else {
      couponRow.classList.add("hidden");
    }
    if (orderDetails.coupon_code) {
      couponSection.classList.remove("hidden");
      document.querySelector(".order_coupon").textContent =
        orderDetails.coupon_code;
    } else {
      couponSection.classList.add("hidden");
    }

    const trackingSection = document.querySelector(".shiprocket_tracking_section");
    if (orderDetails.shiprocket_order_id) {
      trackingSection.classList.remove("hidden");
      document.querySelector(".shiprocket_order_id").textContent =
        orderDetails.shiprocket_order_id;
      document.querySelector(".shiprocket_awb_code").textContent =
        orderDetails.shiprocket_awb_code || "Processing...";

      const trackingUrl = document.querySelector(".shiprocket_tracking_url");
      if (
        orderDetails.shiprocket_status === "PICKUP_SCHEDULED" ||
        orderDetails.shiprocket_status === "AWB_GENERATED"
      ) {
        trackingUrl.href = `https://shiprocket.co/tracking/${orderDetails.shiprocket_awb_code}`;
        trackingUrl.style.display = "";
      } else {
        trackingUrl.style.display = "none";
      }
    } else {
      trackingSection.classList.add("hidden");
    }

    // Populate items
    const itemsContainer = document.querySelector(".modal_order_items");
    itemsContainer.innerHTML = "";
    orderDetails.items.forEach((item) => {
      const imagePath = item.product_image && item.product_image.image_path
        ? `${BASE_URL}upload/product/${item.product_image.image_path}`
        : "";
      const itemElement = document.createElement("div");
      itemElement.classList.add(
        "prd_item",
        "flex",
        "flex-wrap",
        "items-center",
        "justify-between",
        "gap-3",
        "py-5",
        "border-b",
        "border-line"
      );
      itemElement.innerHTML = `
        <a href="${BASE_URL}product?id=${item.product_id || ''}" class="flex items-center gap-5">
          <div class="bg-img flex-shrink-0 md:w-[100px] w-20 aspect-square rounded-lg overflow-hidden">
            <img src="${imagePath}" alt="${item.product_name}" class="w-full h-full object-cover" />
          </div>
          <div>
            <div class="prd_name text-title">${item.product_name}</div>
            <div class="caption1 text-secondary mt-1">Size: ${item.variant_size || '-'} | Color: ${item.variant_color || '-'}</div>
          </div>
        </a>
        <div class="text-title">
          <span class="prd_quantity">${item.quantity}</span>
          <span> X </span>
          <span class="prd_price">₹${parseFloat(item.product_price || 0).toFixed(2)}</span>
          <span> = </span>
          <span class="prd_price">₹${parseFloat(item.total_price || 0).toFixed(2)}</span>
        </div>
      `;
      itemsContainer.appendChild(itemElement);
    });
  }

  modalOrderDetail.addEventListener("click", () => {
    modalOrderDetailMain.classList.remove("open");
  });

  modalOrderDetailMain.addEventListener("click", (e) => {
    e.stopPropagation();
  });
}

// Return Product request modal
const modalReturnRequestBlock = document.querySelector("#modalReturnRequestBlock");
const modalReturnRequestMain = document.querySelector("#modalReturnRequestMain");
const returnMaxImages = 5;
const returnMaxImageSize = 5 * 1024 * 1024; // 5 MB, keep in sync with MAX_RETURN_IMAGE_SIZE server-side

if (modalReturnRequestBlock && modalReturnRequestMain) {
  document.querySelectorAll(".btn_return_product").forEach((btn) => {
    btn.addEventListener("click", () => {
      const form = document.querySelector("#returnRequestForm");
      form.reset();
      document.querySelector("#returnOtherReasonWrap").classList.add("hidden");

      document.querySelector("#returnBookItemId").value = btn.getAttribute("book-item-id");
      document.querySelector("#returnProductName").textContent = btn.getAttribute("data-product-name");
      document.querySelector("#returnOrderNumber").textContent = btn.getAttribute("data-order-number");
      document.querySelector("#returnQtyPurchased").textContent = btn.getAttribute("data-qty");
      document.querySelector("#returnProductImage").src = btn.getAttribute("data-image");

      const qtyInput = document.querySelector("#returnQuantity");
      qtyInput.max = btn.getAttribute("data-qty");
      qtyInput.value = btn.getAttribute("data-qty");

      modalReturnRequestMain.classList.add("open");
    });
  });

  document.querySelector("#returnReason").addEventListener("change", function() {
    document.querySelector("#returnOtherReasonWrap").classList.toggle("hidden", this.value !== "Other");
  });

  document.querySelector("#returnImages").addEventListener("change", function() {
    if (this.files.length > returnMaxImages) {
      alert(`You can upload a maximum of ${returnMaxImages} images.`);
      this.value = "";
      return;
    }
    for (const file of this.files) {
      if (file.size > returnMaxImageSize) {
        alert(`"${file.name}" is larger than 5 MB. Please choose smaller images.`);
        this.value = "";
        return;
      }
      if (!["image/jpeg", "image/png", "image/webp"].includes(file.type)) {
        alert(`"${file.name}" is not a JPG, PNG, or WEBP image.`);
        this.value = "";
        return;
      }
    }
  });

  document.querySelector("#returnCancelBtn").addEventListener("click", () => {
    modalReturnRequestMain.classList.remove("open");
  });
  modalReturnRequestBlock.addEventListener("click", () => {
    modalReturnRequestMain.classList.remove("open");
  });
  modalReturnRequestMain.addEventListener("click", (e) => e.stopPropagation());

  document.querySelector("#returnRequestForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const submitBtn = document.querySelector("#returnSubmitBtn");
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting...";

    const formData = new FormData(this);
    fetch(`${BASE_URL}Web/submitReturnRequest`, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(data.message || "Return Request Submitted Successfully.");
          modalReturnRequestMain.classList.remove("open");
          window.location.reload();
        } else {
          const msg = typeof data.message === "object" ? Object.values(data.message).join("\n") : data.message;
          alert(msg || "Failed to submit return request.");
        }
      })
      .catch(() => {
        alert("An error occurred while submitting your return request. Please try again.");
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      });
  });
}

// Track Return status/timeline modal
const modalTrackReturnBlock = document.querySelector("#modalTrackReturnBlock");
const modalTrackReturnMain = document.querySelector("#modalTrackReturnMain");

if (modalTrackReturnBlock && modalTrackReturnMain) {
  document.querySelectorAll(".btn_track_return").forEach((btn) => {
    btn.addEventListener("click", () => {
      const returnId = btn.getAttribute("return-id");
      fetch(`${BASE_URL}Web/returnStatus/${returnId}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.querySelector("#trackReturnCode").textContent = data.return.return_code;

            const refundEl = document.querySelector("#trackReturnRefund");
            if (data.return.refund_method) {
              refundEl.textContent = `Refund: ${data.return.refund_amount ? "₹" + data.return.refund_amount + " via " : ""}${data.return.refund_method} (${data.return.refund_status == 1 ? "Completed" : data.return.refund_status == 2 ? "Failed" : "Pending"})`;
              refundEl.classList.remove("hidden");
            } else {
              refundEl.classList.add("hidden");
            }

            const timelineEl = document.querySelector("#trackReturnTimeline");
            timelineEl.innerHTML = data.timeline
              .map(
                (t) => `
                  <div class="flex items-center justify-between gap-3 py-3 border-b border-line">
                    <span class="text-title">${t.status_label}</span>
                    <span class="caption1 text-secondary">${t.create_date}</span>
                  </div>
                `
              )
              .join("");
            modalTrackReturnMain.classList.add("open");
          } else {
            alert(data.message || "Failed to fetch return status.");
          }
        })
        .catch(() => {
          alert("An error occurred while fetching return status.");
        });
    });
  });

  document.querySelector("#trackReturnCloseBtn").addEventListener("click", () => {
    modalTrackReturnMain.classList.remove("open");
  });
  modalTrackReturnBlock.addEventListener("click", () => {
    modalTrackReturnMain.classList.remove("open");
  });
  modalTrackReturnMain.addEventListener("click", (e) => e.stopPropagation());
}

// Modal Video
const playIcons = document.querySelectorAll(".btn-play");
const modalVideo = document.querySelector(".modal-video-block");
const modalVideoMain = document.querySelector(
  ".modal-video-block .modal-video-main"
);

if (playIcons && modalVideo) {
  playIcons.forEach((playIcon) => {
    playIcon.addEventListener("click", () => {
      modalVideoMain.classList.add("open");
    });
  });

  modalVideo.addEventListener("click", () => {
    modalVideoMain.classList.remove("open");
  });

  modalVideoMain.addEventListener("click", (e) => {
    e.stopPropagation();
  });
}

// Scroll to top
const scrollTopBtn = document.querySelector(".scroll-to-top-btn");
window.addEventListener("scroll", () => {
  if (window.scrollY > 600) {
    scrollTopBtn.classList.add("active");
  } else {
    scrollTopBtn.classList.remove("active");
  }
});

// Handle layout cols in list product wishlist page, shop
const layoutProductList = document.querySelector(
  ".list-product-block .list-product"
);
const chooseLayoutItems = document.querySelectorAll(".choose-layout .item");

if (layoutProductList && chooseLayoutItems) {
  chooseLayoutItems.forEach((item) => {
    if (item.classList.contains("active")) {
      if (item.classList.contains("three-col")) {
        layoutProductList.classList.add("lg:grid-cols-3");
        layoutProductList.classList.remove("lg:grid-cols-4");
        layoutProductList.classList.remove("lg:grid-cols-5");
      } else if (item.classList.contains("four-col")) {
        layoutProductList.classList.add("lg:grid-cols-4");
        layoutProductList.classList.remove("lg:grid-cols-3");
        layoutProductList.classList.remove("lg:grid-cols-5");
      } else if (item.classList.contains("five-col")) {
        layoutProductList.classList.add("lg:grid-cols-5");
        layoutProductList.classList.remove("lg:grid-cols-3");
        layoutProductList.classList.remove("lg:grid-cols-4");
      }
    }

    item.addEventListener("click", () => {
      if (item.classList.contains("three-col")) {
        layoutProductList.classList.add("lg:grid-cols-3");
        layoutProductList.classList.remove("lg:grid-cols-4");
        layoutProductList.classList.remove("lg:grid-cols-5");
      } else if (item.classList.contains("four-col")) {
        layoutProductList.classList.add("lg:grid-cols-4");
        layoutProductList.classList.remove("lg:grid-cols-3");
        layoutProductList.classList.remove("lg:grid-cols-5");
      } else if (item.classList.contains("five-col")) {
        layoutProductList.classList.add("lg:grid-cols-5");
        layoutProductList.classList.remove("lg:grid-cols-3");
        layoutProductList.classList.remove("lg:grid-cols-4");
      }
    });
  });
}

// Display wishlist, cart, compare item from localStorage
const listProductWishlist = document.querySelector(
  ".wishlist-block .list-product"
);
const cartPage = document.querySelector(".cart-block");
const checkoutPage = document.querySelector(".checkout-block");
const listProductCheckout = document.querySelector(
  ".checkout-block .list-product-checkout"
);
const listProductCompare = document.querySelector(
  ".compare-block .content-main"
);

// Wishlist
if (listProductWishlist) {
  let wishlistStore = localStorage.getItem("wishlistStore");
  wishlistStore = wishlistStore ? JSON.parse(wishlistStore) : [];

  if (wishlistStore.length === 0) {
    // Show empty message
    const emptyMessage = document.createElement("p");
    emptyMessage.textContent = "Your wishlist is empty.";
    emptyMessage.classList.add("bg-red", "text-white", "px-2", "text-center");
    listProductWishlist.appendChild(emptyMessage);
  } else {
    // Populate wishlist with products
    wishlistStore.forEach((product) => {
      const productElement = createProductItem(product);
      listProductWishlist.appendChild(productElement);
    });
  }
}

// Compare page
if (listProductCompare) {
  let compareStore = localStorage.getItem("compareStore");
  compareStore = compareStore ? JSON.parse(compareStore) : [];

  const listImg = listProductCompare.querySelector(".list-product .right");
  const listRate = listProductCompare.querySelector(".list-rate-block");
  const listPrice = listProductCompare.querySelector(".list-price-block");
  const listType = listProductCompare.querySelector(".list-type-block");
  const listBrand = listProductCompare.querySelector(".list-brand-block");
  const listSize = listProductCompare.querySelector(".list-size-block");
  const listColor = listProductCompare.querySelector(".list-color-block");

  if (compareStore.length === 0) {
    listProductCompare.innerHTML = `
        <div class="flex items-center justify-between w-full">
        <div>
        <div class="text-title">No product in compare</div>
        </div>
        </div>
        `;
  } else {
    compareStore.forEach((product) => {
      // list img
      const productElement = document.createElement("div");
      productElement.setAttribute("data-item", product.id);
      productElement.classList.add(
        "product-item",
        "px-10",
        "pt-6",
        "pb-5",
        "border-r",
        "border-line",
        "cursor-pointer"
      );
      productElement.innerHTML = `
                <div class="bg-img w-full aspect-[3/4] rounded-lg overflow-hidden flex-shrink-0">
                    <img src=${product.thumbImage[0]} alt='img' class='w-full h-full object-cover' />
                </div>
                <div class="text-title text-center mt-4">${product.name}</div>
                <div class="caption2 font-semibold text-secondary2 uppercase text-center mt-1">
                    ${product.brand}
                </div>
                `;

      listImg.appendChild(productElement);

      // list star
      let arrOfStar = "";
      const rateElement = document.createElement("td");
      rateElement.classList.add(
        "w-full",
        "border",
        "border-line",
        "h-[60px]",
        "border-t-0",
        "border-r-0"
      );
      for (let i = 0; i < 5; i++) {
        if (product.rate) {
          if (i >= product.rate) {
            arrOfStar +=
              '<i class="ph-fill ph-star text-sm text-secondary"></i>';
          } else {
            arrOfStar += '<i class="ph-fill ph-star text-sm text-yellow"></i>';
          }
        }
      }

      rateElement.innerHTML = `
                <div class='h-full flex items-center justify-center'>
                    <div class="rate flex">
                        ${arrOfStar}
                    </div>
                    <p class='pl-1'>(1.234)</p>
                </div>
            `;

      listRate.appendChild(rateElement);

      // list price
      const priceElement = document.createElement("td");
      priceElement.classList.add(
        "w-full",
        "border",
        "border-line",
        "h-[60px]",
        "border-t-0",
        "border-r-0"
      );
      priceElement.innerHTML = `
                <div class='price-item h-full flex items-center justify-center'>
                    ₹${parseFloat(product.price).toFixed(2)}
                </div>
            `;

      listPrice.appendChild(priceElement);

      // list type
      const typeElement = document.createElement("td");
      typeElement.classList.add(
        "w-full",
        "border",
        "border-line",
        "h-[60px]",
        "border-t-0",
        "border-r-0"
      );
      typeElement.innerHTML = `
                <div class='type-item h-full flex items-center justify-center capitalize'>
                    ${product.type}
                </div>
            `;

      listType.appendChild(typeElement);

      // list brand
      const brandElement = document.createElement("td");
      brandElement.classList.add(
        "w-full",
        "border",
        "border-line",
        "h-[60px]",
        "border-t-0",
        "border-r-0"
      );
      brandElement.innerHTML = `
                <div class='brand-item h-full flex items-center justify-center capitalize'>
                    ${product.brand}
                </div>
            `;

      listBrand.appendChild(brandElement);

      // list size
      const sizeElement = document.createElement("td");
      sizeElement.classList.add(
        "w-full",
        "border",
        "border-line",
        "h-[60px]",
        "border-t-0",
        "border-r-0"
      );
      let size = "";

      if (product.sizes) {
        product.sizes.forEach((item, index) => {
          // if last size, don't add ',' in the end
          if (index === product.sizes.length - 1) {
            size += `<p>${item}</p>`;
          } else {
            size += `<p>${item}, </p>`;
          }
        });
      }

      sizeElement.innerHTML = `
                <div class='list-size h-full flex items-center justify-center capitalize gap-1'>
                    ${size}
                </div>
            `;

      listSize.appendChild(sizeElement);

      // list color
      const colorElement = document.createElement("td");
      colorElement.classList.add(
        "w-full",
        "border",
        "border-line",
        "h-[60px]",
        "border-t-0",
        "border-r-0"
      );
      let color = "";

      if (product.variation) {
        product.variation.forEach((item) => {
          color += `<span class='w-6 h-6 rounded-full' style="background-color: ${item.colorCode};"></span>`;
        });
      }

      colorElement.innerHTML = `
                <div class='list-color h-full flex items-center justify-center capitalize gap-2'>
                    ${color}
                </div>
            `;

      listColor.appendChild(colorElement);
    });
  }
}

// Cart
let listProductCart = document.querySelector(".cart-block .list-product-main");

// Cart contents are localStorage snapshots taken at add-to-cart time, so a
// product's stock flag can go stale (in-stock when added, flagged out of
// stock later by staff). Fetched once and cached - product listing/detail
// pages already hit Test/products separately, this just needs a fresh-enough
// read of the same feed for the cart/checkout pages specifically.
let outOfStockMapPromise = null;
function getOutOfStockMap() {
  if (!outOfStockMapPromise) {
    outOfStockMapPromise = fetch(`${BASE_URL}Test/products`)
      .then((res) => res.json())
      .then((data) => {
        const map = {};
        data.forEach((p) => {
          map[p.id] = !!p.outOfStock;
        });
        return map;
      })
      .catch(() => ({}));
  }
  return outOfStockMapPromise;
}

// A coupon's discount is only ever validated against the cart subtotal at
// the moment applyPromocode() last ran (page load, or the auto-trigger below
// when updateTotalCart()/applyPromoCode() notice the live total no longer
// matches localStorage's appliedPromoSubtotal). Re-running applyPromocode()
// (defined later, inline in footer-link.php) re-fetches the correct discount
// for the current cart and re-renders via refreshCouponAppliedViews().
function revalidateCartCoupon() {
  const code = localStorage.getItem("appliedPromoCode");
  if (code && typeof applyPromocode === "function") {
    applyPromocode(code);
  }
}

const handleInforCart = async () => {
  if (listProductCart) {
    let cartStore = localStorage.getItem("cartStore");
    cartStore = cartStore ? JSON.parse(cartStore) : [];

    const outOfStockMap = await getOutOfStockMap();

    // Initial value in cart page
    let moneyForFreeship = 999;
    let totalCart = 0;

    const moneyFreeshipProgress = cartPage.querySelector(
      ".tow-bar-block .progress-line"
    );

    listProductCart.innerHTML = "";

    cartStore.forEach((product) => {
      const isOutOfStock = !!outOfStockMap[product.id];

      // Build variant display text
      let variantText = '';
      if (product.variantSize || product.variantColor) {
        const parts = [];
        if (product.variantSize) parts.push(`Size: ${product.variantSize}`);
        if (product.variantColor) parts.push(`Color: ${product.variantColor}`);
        variantText = `<div class="caption1 text-secondary mt-1">${parts.join(' | ')}</div>`;
      }
      if (isOutOfStock) {
        variantText += `<div class="mt-2"><span class="caption1 font-semibold text-white bg-black px-3 py-0.5 rounded-full inline-block">Out of Stock</span></div>`;
      }

      // calculate for prd item - out-of-stock items don't count toward the total
      const calculateProductTotal = () => {
        if (isOutOfStock) return 0;
        const productTotal = product.price * product.quantityPurchase;
        return productTotal;
      };

      const productElement = document.createElement("div");
      productElement.setAttribute("data-item", product.id);
      productElement.classList.add(
        "item",
        "flex",
        "md:mt-7",
        "md:pb-7",
        "mt-5",
        "pb-5",
        "border-b",
        "border-line",
        "w-full"
      );
      productElement.innerHTML = `
                <div class="w-1/2">
                    <div class="flex items-center gap-6">
                        <div class="bg-img md:w-[100px] w-20 aspect-[3/4]">
                            <img src=${product.thumbImage[0]} alt='img'
                                class='w-full h-full object-cover rounded-lg' />
                        </div>
                        <div>
                            <div class="text-title">${product.name}</div>
                            ${variantText}
                            <div class="list-select mt-2 text-secondary mt-1">${product.quantityPurchase} </div>
                        </div>
                    </div>
                </div>
                <div class="w-1/12 price flex items-center justify-center">
                    <div class="text-title text-center">₹${parseFloat(product.price).toFixed(2)}<br></div>
                </div>
                <div class="w-1/6 flex items-center justify-center">
                    <div
                        class="quantity-block bg-surface md:p-3 p-2 flex items-center justify-between rounded-lg border border-line md:w-[100px] flex-shrink-0 w-20 ${isOutOfStock ? 'pointer-events-none opacity-50' : ''}">
                        <i class="ph-bold ph-minus cursor-pointer text-base max-md:text-sm"></i>
                        <div class="text-button quantity">${product.quantityPurchase}</div>
                        <i class="ph-bold ph-plus cursor-pointer text-base max-md:text-sm"></i>
                    </div>
                </div>
                <div class="w-1/6 flex total-price items-center justify-center">
                    <div class="text-title text-center">₹${(isOutOfStock ? 0 : product.price * product.quantityPurchase).toFixed(2)}
                    </div>
                </div>
                <div class="w-1/12 flex items-center justify-center">
                    <i
                        class="remove-btn ph ph-x-circle text-xl max-md:text-base text-red cursor-pointer hover:text-black duration-300"
                        data-product-id="${product.id}"
                        data-variant-id="${product.variant_id || ''}"></i>
                </div>
            `;

      const quantityBlock = productElement.querySelector(".quantity-block");
      const quantityProduct = quantityBlock.querySelector(".quantity");
      const totalPriceProduct = productElement.querySelector(
        ".total-price .text-title"
      );

      quantityBlock.querySelector(".ph-plus").addEventListener("click", () => {
        product.quantityPurchase = product.quantityPurchase + 1;
        quantityProduct.textContent = product.quantityPurchase;
        totalPriceProduct.textContent = `₹${(product.quantityPurchase * product.price).toFixed(2)}`;

        // Persist the new quantity before updateTotalCart() runs - it may
        // trigger a coupon revalidation that reads the cart straight from
        // localStorage (via getCartSubtotalForCoupon()), which must see this
        // change rather than the pre-update quantity.
        localStorage.setItem("cartStore", JSON.stringify(cartStore));
        updateTotalCart();
      });

      quantityBlock.querySelector(".ph-minus").addEventListener("click", () => {
        if (product.quantityPurchase > 1) {
          product.quantityPurchase = product.quantityPurchase - 1;
          quantityProduct.textContent = product.quantityPurchase;
          totalPriceProduct.textContent = `₹${(product.quantityPurchase * product.price).toFixed(2)}`;

          localStorage.setItem("cartStore", JSON.stringify(cartStore));
          updateTotalCart();
        }
      });

      listProductCart.appendChild(productElement);
      totalCart += calculateProductTotal();

      // Change value in cart page
      document.querySelector(".total-block .total-product").innerHTML =
        totalCart;
    });

    const updateTotalCart = async () => {
      totalCart = 0;

      // Calculate the total cart value - out-of-stock items are excluded
      cartStore.forEach((product) => {
        if (outOfStockMap[product.id]) return;
        totalCart += product.price * product.quantityPurchase;
      });

      // Get the applied promo code + its server-validated discount amount
      // (set by applyPromocode() in footer-link.php, which re-validates
      // against the current cart on every page load) from localStorage.
      const appliedPromoCode = localStorage.getItem("appliedPromoCode");
      const appliedPromoDiscount = localStorage.getItem("appliedPromoDiscount");
      const appliedPromoSubtotal = localStorage.getItem("appliedPromoSubtotal");

      // appliedPromoDiscount is only valid for the exact subtotal it was
      // computed against - if the cart changed since (quantity/item removed)
      // and revalidation hasn't caught up yet, Math.min()-clamping the old
      // discount to the new (smaller) total would misrepresent a coupon's
      // real rate as up to 100% off. Show it as pending instead until
      // revalidateCartCoupon() (called on every quantity/remove change, and
      // on page load) confirms the correct amount for this exact total.
      const isDiscountFresh = appliedPromoSubtotal !== null && Math.abs(parseFloat(appliedPromoSubtotal) - totalCart) < 0.01;

      let discount = 0;
      if (appliedPromoCode && appliedPromoDiscount !== null && isDiscountFresh) {
        discount = Math.min(parseFloat(appliedPromoDiscount) || 0, totalCart);
        document.querySelector(
          ".discount-block .text-title"
        ).innerHTML = `Discount <div class="appliedCouponSummary" onclick="removeCartCoupon()">(Coupon: ${appliedPromoCode})<i class="ph ph-x"></i></div>`;
        document.querySelector(".discount-block .discount").innerHTML =
          discount.toFixed(2);
      } else if (appliedPromoCode) {
        document.querySelector(
          ".discount-block .text-title"
        ).innerHTML = `Discount <div class="appliedCouponSummary" onclick="removeCartCoupon()">(Coupon: ${appliedPromoCode})<i class="ph ph-x"></i></div>`;
        document.querySelector(".discount-block .discount").innerHTML = (0).toFixed(2);
        revalidateCartCoupon();
      } else {
        document.querySelector(
          ".discount-block .text-title"
        ).innerHTML = `Discount`;
        document.querySelector(".discount-block .discount").innerHTML = (0).toFixed(2);
      }

      const discountedTotalCart = totalCart - discount;

      // Update total values in the cart page - Subtotal shows the raw
      // pre-discount cart value (the discount is broken out on its own line
      // below), not the post-discount remainder.
      document.querySelector(".total-block .total-product").innerHTML =
        totalCart.toFixed(2);

      const charges = await fetchPackagingCharge();
      moneyForFreeship = parseFloat(charges.min_amount) || moneyForFreeship;

      document.querySelector(".total-cart-block .total-cart").innerHTML = (
        discountedTotalCart <= moneyForFreeship
          ? discountedTotalCart + parseFloat(charges.amount)
          : discountedTotalCart
      ).toFixed(2);

      document.querySelector(".ship").innerHTML =
        discountedTotalCart <= moneyForFreeship ? "₹" + parseFloat(charges.amount).toFixed(2) : "FREE";

      document.querySelector(".heading.banner .more-price").innerHTML =
        discountedTotalCart <= moneyForFreeship
          ? (moneyForFreeship - discountedTotalCart).toFixed(2)
          : (0).toFixed(2);

      moneyFreeshipProgress.style.width =
        discountedTotalCart <= moneyForFreeship
          ? `${(discountedTotalCart / moneyForFreeship) * 100}%`
          : `100%`;
    };

    window.removeCartCoupon = function () {
      localStorage.removeItem("appliedPromoCode");
      localStorage.removeItem("appliedPromoDiscount");
      localStorage.removeItem("appliedPromoSubtotal");

      document.querySelectorAll(".item").forEach((div) => {
        div.classList.remove("bg-green"); // Remove bg-green class
        const applyButton = div.querySelector("[onclick^='applyPromocode']");
        if (applyButton) {
          applyButton.innerHTML = "Apply Code"; // Reset button text
        }
      });

      updateTotalCart();
    };

    updateTotalCart();

    const prdItems = listProductCart.querySelectorAll(".item");
    prdItems.forEach((prd) => {
      const removeCartBtn = prd.querySelector(".remove-btn");
      removeCartBtn.addEventListener("click", function () {
        const prdId = this.dataset.productId;
        const variantId = this.dataset.variantId || null;

        // Remove from cartStore - match by both product ID and variant ID
        const newArray = cartStore.filter((item) => {
          const itemVariantId = item.variant_id || null;
          return !(item.id === prdId && itemVariantId === variantId);
        });

        localStorage.setItem("cartStore", JSON.stringify(newArray));
        handleInforCart();

        if (newArray.length === 0) {
          location.reload();
        }
      });
    });
  }
};

handleInforCart();

// Checkout

// Function to get cart data from localStorage
function getCartData() {
  const cartData = localStorage.getItem("cartStore");
  return cartData ? JSON.parse(cartData) : [];
}

// Function to calculate total cart value - out-of-stock items are excluded
function calculateTotal(cartStore, outOfStockMap = {}) {
  return cartStore.reduce(
    (total, product) =>
      outOfStockMap[product.id] ? total : total + product.price * product.quantityPurchase,
    0
  );
}

// Function to render a single product
function renderProduct(product, isOutOfStock = false) {
  // Build variant display text
  let variantText = '';
  if (product.variantSize || product.variantColor) {
    const parts = [];
    if (product.variantSize) parts.push(`Size: ${product.variantSize}`);
    if (product.variantColor) parts.push(`Color: ${product.variantColor}`);
    variantText = `<div class="caption1 text-secondary mt-1">${parts.join(' | ')}</div>`;
  }
  if (isOutOfStock) {
    variantText += `<div class="mt-2"><span class="caption1 font-semibold text-white bg-black px-3 py-0.5 rounded-full inline-block">Out of Stock</span></div>`;
  }

  const productElement = document.createElement("div");
  productElement.classList.add(
    "item",
    "flex",
    "items-center",
    "justify-between",
    "w-full",
    "pb-5",
    "border-b",
    "border-line",
    "gap-6",
    "mt-5"
  );
  if (isOutOfStock) {
    productElement.classList.add("opacity-50");
  }
  productElement.innerHTML = `
    <div class="bg-img w-[100px] aspect-square flex-shrink-0 rounded-lg overflow-hidden">
      <img src="${product.thumbImage[0]}" alt="img" class="w-full h-full" />
    </div>
    <div class="flex items-center justify-between w-full">
      <div>
        <div class="name text-title">${product.name}</div>
        ${variantText}
        <div class="caption1 text-secondary mt-2">
          <span class="size capitalize"></span>
        </div>
      </div>
      <div class="text-title">
        <span class="quantity">${product.quantityPurchase}</span>
        <span class="px-1">x</span>
        <span>₹${(isOutOfStock ? 0 : parseFloat(product.price)).toFixed(2)}</span>
      </div>
    </div>
  `;
  return productElement;
}

// Function to render the cart products
function renderCartProducts(listProductCheckout, cartStore, outOfStockMap = {}) {
  listProductCheckout.innerHTML = ""; // Clear previous content
  cartStore.forEach((product) => {
    const productElement = renderProduct(product, !!outOfStockMap[product.id]);
    listProductCheckout.appendChild(productElement);
  });
}

// Function to apply promo code discount
function applyPromoCode(totalCart) {
  // Read the applied promo code + its server-validated discount amount (set
  // by applyPromocode() in footer-link.php, which re-validates against the
  // current cart on every page load) rather than recomputing the discount
  // here - only the server knows a coupon's real amount/eligibility.
  const appliedPromoCode = localStorage.getItem("appliedPromoCode");
  const appliedPromoDiscount = localStorage.getItem("appliedPromoDiscount");
  const appliedPromoSubtotal = localStorage.getItem("appliedPromoSubtotal");

  // appliedPromoDiscount is only valid for the exact subtotal it was
  // computed against (e.g. if the cart changed on a different page since it
  // was applied) - clamping a stale discount to a smaller totalCart via
  // Math.min() would misrepresent the coupon's real rate as up to 100% off.
  // Show it as pending instead until revalidateCartCoupon() confirms it.
  const isDiscountFresh = appliedPromoSubtotal !== null && Math.abs(parseFloat(appliedPromoSubtotal) - totalCart) < 0.01;

  let discount = 0;
  if (appliedPromoCode && appliedPromoDiscount !== null && isDiscountFresh) {
    discount = Math.min(parseFloat(appliedPromoDiscount) || 0, totalCart);
    document.querySelector(
      ".discount-block .text-title"
    ).innerHTML = `Discount <div class="appliedCouponSummary" onclick="removeCoupon()">(Coupon: ${appliedPromoCode})<i class="ph ph-x"></i></div>`;
    document.querySelector(".discount-block .discount").innerHTML = discount.toFixed(2);
  } else if (appliedPromoCode) {
    document.querySelector(
      ".discount-block .text-title"
    ).innerHTML = `Discount <div class="appliedCouponSummary" onclick="removeCoupon()">(Coupon: ${appliedPromoCode})<i class="ph ph-x"></i></div>`;
    document.querySelector(".discount-block .discount").innerHTML = (0).toFixed(2);
    revalidateCartCoupon();
  } else {
    document.querySelector(
      ".discount-block .text-title"
    ).innerHTML = `Discount`;
    document.querySelector(".discount-block .discount").innerHTML = (0).toFixed(2);
  }
  return discount;
}


async function fetchPackagingCharge() {
  try {
    const response = await fetch(`${BASE_URL}/Web/getCharges`);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Error fetching packaging charge:", error);
  }
}
fetchPackagingCharge();

// Function to update cart summary
async function updateCartSummary(totalCart, discount, moneyForFreeship) {
  const charges = await fetchPackagingCharge();
  moneyForFreeship = parseFloat(charges.min_amount) || moneyForFreeship;
  const packagingCharges = parseFloat(charges.packaging_charge);
  const shippingCharges = parseFloat(charges.amount);

  const discountedTotalCart = totalCart - discount;
  const finalTotal =
    discountedTotalCart <= moneyForFreeship
      ? discountedTotalCart + shippingCharges + packagingCharges
      : discountedTotalCart + packagingCharges;

  document.querySelector(".packaging").innerHTML = '₹' + parseFloat(packagingCharges).toFixed(2);

  // Subtotal shows the raw pre-discount cart value (the discount is broken
  // out on its own line above), not the post-discount remainder.
  document.querySelector(".total-block .total-product").innerHTML =
    totalCart.toFixed(2);
  document.querySelector(".total-cart-block .total-cart").innerHTML =
    finalTotal.toFixed(2);
  document.querySelector(".ship").innerHTML =
    discountedTotalCart <= moneyForFreeship ? "₹" + parseFloat(shippingCharges).toFixed(2) : "FREE";
  document.querySelector(".heading.banner .more-price").innerHTML =
    discountedTotalCart <= moneyForFreeship
      ? (moneyForFreeship - discountedTotalCart).toFixed(2)
      : (0).toFixed(2);

  return finalTotal;
}

// Main function to handle cart rendering and calculations
async function handleCartRendering(listProductCheckout) {
  const moneyForFreeship = 999;
  const cartStore = getCartData();
  const outOfStockMap = await getOutOfStockMap();

  // Render cart products
  renderCartProducts(listProductCheckout, cartStore, outOfStockMap);

  // Calculate total cart value - out-of-stock items are excluded
  let totalCart = calculateTotal(cartStore, outOfStockMap);

  // Apply promo code and calculate discount
  const discount = applyPromoCode(totalCart);

  // Update cart summary
  updateCartSummary(totalCart, discount, moneyForFreeship).then((finalTotal) => {
    if (window.fbq && cartStore.length > 0) {
      fbq('track', 'InitiateCheckout', {
        content_ids: cartStore.map((p) => String(p.id)),
        content_type: 'product',
        contents: cartStore.map((p) => ({ id: String(p.id), quantity: p.quantityPurchase })),
        num_items: cartStore.reduce((sum, p) => sum + p.quantityPurchase, 0),
        value: finalTotal,
        currency: 'INR',
      });
    }
  });
}
function removeCoupon() {
  localStorage.removeItem("appliedPromoCode");
  localStorage.removeItem("appliedPromoDiscount");
  localStorage.removeItem("appliedPromoSubtotal");

  document.querySelectorAll(".item").forEach((div) => {
    div.classList.remove("bg-green"); // Remove bg-green class
    const applyButton = div.querySelector("[onclick^='applyPromocode']");
    if (applyButton) {
      applyButton.innerHTML = "Apply Code"; // Reset button text
    }
  });

  handleCartRendering(listProductCheckout);
}

// Usage example

if (listProductCheckout) {
  handleCartRendering(listProductCheckout);
}

// Show, hide login block in checkout
const formLoginHeading = document.querySelector(
  ".checkout-block .form-login-block"
);
const loginHeading = document.querySelector(
  ".checkout-block .login .left span.text-button"
);
const iconDownHeading = document.querySelector(
  ".checkout-block .login .right i"
);

if (loginHeading) {
  loginHeading.addEventListener("click", () => {
    formLoginHeading.classList.toggle("open");
    iconDownHeading.classList.toggle("up");
  });

  iconDownHeading.addEventListener("click", () => {
    formLoginHeading.classList.toggle("open");
    iconDownHeading.classList.toggle("up");
  });
}

// Show, hide payment type in checkout
const listPayment = document.querySelector(".payment-block .list-payment");
const paymentCheckbox = document.querySelectorAll(
  ".payment-block .list-payment .type>input"
);

if (paymentCheckbox) {
  paymentCheckbox.forEach((item) => {
    item.addEventListener("click", () => {
      if (listPayment.querySelector(".open")) {
        listPayment.querySelector(".open").classList.remove("open");
      }

      let parentType = item.parentElement;
      if (item.checked) {
        parentType.classList.add("open");
      }
    });
  });
}

// faqs
const menuTab = document.querySelector(".menu-tab");
const listQuestion = document.querySelector(".list-question");
const tabQuestions = document.querySelectorAll(".tab-question");
const questionItems = document.querySelectorAll(".question-item");

if (tabItems) {
  tabItems.forEach((tabItem) => {
    tabQuestions.forEach((tabQuestion) => {
      let activeMenuTab = menuTab.querySelector(".active");

      if (
        activeMenuTab.getAttribute("data-item") ===
        tabQuestion.getAttribute("data-item")
      ) {
        tabQuestion.classList.add("active");
      }

      tabItem.addEventListener("click", () => {
        if (
          tabItem.getAttribute("data-item") ===
          tabQuestion.getAttribute("data-item")
        ) {
          listQuestion.querySelector(".active").classList.remove("active");
          tabQuestion.classList.add("active");
        }
      });
    });
  });
}

if (questionItems) {
  questionItems.forEach((item, index) => {
    item.addEventListener("click", () => {
      item.classList.toggle("open");

      removeOpen(index);
    });
  });
}

function removeOpen(index1) {
  questionItems.forEach((item2, index2) => {
    if (index1 != index2) {
      item2.classList.remove("open");
    }
  });
}

function showPassword(inputField, showButton) {
  let passwordField = document.getElementById(inputField);
  let eyeButton = document.getElementById(showButton);
  if (passwordField.type == "password") {
    passwordField.type = "text";
    eyeButton.classList.add("active");
  } else {
    passwordField.type = "password";
    eyeButton.classList.remove("active");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  function moveCartHome() {
    if (window.innerWidth <= 768) {
      document.querySelectorAll(".product-infor").forEach(productInfo => {
        let cartAtHome = productInfo.closest(".product-item")?.querySelector(".cartathome");

        if (cartAtHome) {
          productInfo.appendChild(cartAtHome);
        }
      });
    }
  }

  moveCartHome(); // Run on page load

  window.addEventListener("resize", function () {
    moveCartHome(); // Re-run when resizing
  });
});

