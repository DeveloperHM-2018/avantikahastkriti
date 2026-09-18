// Table of contents
/**** List scroll you'll love this to ****/
/**** detail infor by fetch data ****/
/*****----- Next, Prev products when click button -----************/
/*****----- list-img -----************/
/*****----- list-img countdown timer -----************/
/*****----- list-img variable -----************/
/*****----- list-img on-sale -----************/
/*****----- list-img fixed-price -----************/
/*****----- product sale -----************/
/*****----- infor -----************/
/**** detail ****/
/**** desc-tab ****/
/**** list-img on-sale ****/
/**** list-img review ****/
/**** Redirect filter type product-sidebar ****/

// List scroll you'll love this to
if (document.querySelector(".swiper-product-scroll")) {
  var swiperCollection = new Swiper(".swiper-product-scroll", {
    scrollbar: {
      el: ".swiper-scrollbar",
      hide: true,
    },
    loop: false,
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      1280: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
    },
  });
}

// detail infor by fetch data
const urlParams = new URLSearchParams(window.location.search);
const pathSegments = window.location.pathname.split('/');
const lastSegment = pathSegments[pathSegments.length - 1];
const parts = lastSegment.split('-');
const potentialId = parts[parts.length - 1];

const productId =
  urlParams.get("id") !== null
    ? urlParams.get("id")
    : (potentialId && !isNaN(potentialId) ? potentialId : "1");
const productDetail = document.querySelector(".product-detail");
let currentIndex;

// Href
let classes = productDetail.className.split(" ");
let typePage = classes[1];

if (productDetail) {
  fetch(`${BASE_URL}Test/products?product_id=${productId}`)
    .then((response) => response.json())
    .then((data) => {
      let productMain = data.find((product) => product.id === productId);

      if (productMain === undefined) {
        productDetail.innerHTML =
          '<div class="text-center py-20"><p class="text-secondary">Product not found.</p></div>';
        return;
      }

      if (window.fbq) {
        fbq('track', 'ViewContent', {
          content_ids: [String(productMain.id)],
          content_type: 'product',
          content_name: productMain.name,
          value: productMain.price,
          currency: 'INR',
        });
      }

      if (productMain.discontinued) {
        const actionsBlock = productDetail.querySelector(".list-action.quick-shop-block");
        const notice = document.createElement("div");
        notice.className = "discontinued-notice mt-5 mb-3 p-4 rounded-lg bg-surface border border-line text-center";
        notice.innerHTML = '<strong>This product has been discontinued</strong><br><span class="text-secondary">It is no longer available for purchase.</span>';
        if (actionsBlock) {
          actionsBlock.insertAdjacentElement("beforebegin", notice);
          actionsBlock.classList.add("pointer-events-none", "opacity-50");
          actionsBlock.setAttribute("aria-disabled", "true");
          actionsBlock.querySelectorAll("button, input, select, [role='button']").forEach((el) => {
            el.disabled = true;
          });
        }
      } else if (productMain.outOfStock) {
        const actionsBlock = productDetail.querySelector(".list-action.quick-shop-block");
        const notice = document.createElement("div");
        notice.className = "discontinued-notice mt-5 mb-3 p-4 rounded-lg bg-surface border border-line text-center";
        notice.innerHTML = '<strong>This product is currently out of stock</strong><br><span class="text-secondary">It is not available for purchase right now.</span>';
        if (actionsBlock) {
          actionsBlock.insertAdjacentElement("beforebegin", notice);
          actionsBlock.classList.add("pointer-events-none", "opacity-50");
          actionsBlock.setAttribute("aria-disabled", "true");
          actionsBlock.querySelectorAll("button, input, select, [role='button']").forEach((el) => {
            el.disabled = true;
          });
        }
      }

      // find location of current product in array
      currentIndex = data.findIndex((product) => product.id === productId);

      // Next, Prev products when click button
      const prevBtn = document.querySelector(".breadcrumb-product .prev-btn");
      const nextBtn = document.querySelector(".breadcrumb-product .next-btn");

      nextBtn.addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % data.length;
        const nextProduct = data[currentIndex];
        window.location.href = `${BASE_URL}product/${nextProduct.slug}`;
      });

      if (productId === "1") {
        prevBtn.remove();
      } else {
        prevBtn.addEventListener("click", () => {
          currentIndex = (currentIndex - 1) % data.length;
          const nextProduct = data[currentIndex];
          window.location.href = `${BASE_URL}product/${nextProduct.slug}`;
        });
      }

      // list-img
      const listImg2 = productDetail.querySelector(
        ".featured-product .list-img .mySwiper2 .swiper-wrapper"
      );
      const listImg = productDetail.querySelector(
        ".featured-product .list-img .mySwiper .swiper-wrapper"
      );
      const listImgMain = productDetail.querySelector(
        ".featured-product .list-img .popup-img .swiper-wrapper"
      );
      const popupImg = productDetail.querySelector(
        ".featured-product .list-img .popup-img"
      );

      if (listImg2 && listImg) {
        productMain?.images.map((item) => {
          const imgItem = document.createElement("div");
          imgItem.classList.add("swiper-slide", "popup-link");
          imgItem.innerHTML = `
                        <img src=${item} alt='img' class='w-full aspect-[3/4] object-cover' />
                    `;
          const imgItemClone = imgItem.cloneNode(true); // Copy imgItem
          const imgItemClone2 = imgItem.cloneNode(true); // Copy imgItem
          imgItemClone.classList.remove("popup-link");

          listImg2.appendChild(imgItem);
          listImg.appendChild(imgItemClone);
          listImgMain.appendChild(imgItemClone2);

          const slides = document.querySelectorAll(".mySwiper .swiper-slide");
          slides[0].classList.add("swiper-slide-thumb-active");

          slides.forEach((img, index) => {
            img.addEventListener("click", () => {
              // Chuyển swiper 2 đến vị trí tương ứng với ảnh được click trong swiper 1
              swiper2.slideTo(index);
            });
          });
        });

        // Update Swipers after adding slides
        if (document.querySelector(".mySwiper2") && document.querySelector(".mySwiper2").swiper) {
          document.querySelector(".mySwiper2").swiper.update();
          document.querySelector(".mySwiper2").swiper.slideTo(0);
        } else if (typeof swiper2 !== 'undefined') {
          swiper2.update();
          swiper2.slideTo(0);
        }

        if (document.querySelector(".mySwiper") && document.querySelector(".mySwiper").swiper) {
          document.querySelector(".mySwiper").swiper.update();
          document.querySelector(".mySwiper").swiper.slideTo(0);
        } else if (typeof swiperUnderwear !== 'undefined') {
          swiperUnderwear.update();
          swiperUnderwear.slideTo(0);
        }
      }

      // list-img countdown timer
      const listImg3 = productDetail.querySelector(
        ".featured-product.countdown-timer .list-img .list"
      );
      if (listImg3) {
        productMain.images.map((item) => {
          const imgItem = document.createElement("div");
          imgItem.classList.add("popup-link", "swiper-slide");
          imgItem.innerHTML = `
                        <img src=${item} alt='img' class='w-full aspect-[3/4] object-cover rounded-[20px]' />
                    `;
          const imgItemClone2 = imgItem.cloneNode(true);

          listImg3.appendChild(imgItem);
          listImgMain.appendChild(imgItemClone2);
        });
      }

      // list-img variable
      const listImg4 = productDetail.querySelector(
        ".featured-product.variable .list-img .list"
      );

      if (listImg4) {
        productMain.images.forEach((item, index) => {
          const imgItem = document.createElement("div");
          imgItem.classList.add("popup-link", "swiper-slide");
          imgItem.innerHTML = `
                        <img src=${item} alt='img' class='w-full aspect-[3/4] object-cover rounded-[20px]' />
                    `;

          const imgItemClone2 = imgItem.cloneNode(true);

          // Add img 1st and 4th,... to listImg4
          if (index === 0 || index === 3) {
            imgItem.classList.add("col-span-2");
          }

          listImg4.appendChild(imgItem);
          listImgMain.appendChild(imgItemClone2);
        });
      }

      // list-img on-sale
      const listImg5 = productDetail.querySelector(
        ".featured-product.on-sale .list-img .swiper .swiper-wrapper"
      );

      if (listImg5) {
        productMain.images.map((item) => {
          const imgItem = document.createElement("div");
          imgItem.classList.add("swiper-slide", "popup-link");
          imgItem.innerHTML = `
                        <img src=${item} alt='img' class='w-full aspect-[3/4] object-cover' />
                    `;
          const imgItemClone2 = imgItem.cloneNode(true);

          listImg5.appendChild(imgItem);
          listImgMain.appendChild(imgItemClone2);
        });
      }

      // list-img fixed-price
      const listImg6 = productDetail.querySelector(
        ".featured-product.fixed-price .list-img .list"
      );

      if (listImg6) {
        productMain.images.forEach((item, index) => {
          const imgItem = document.createElement("div");
          imgItem.classList.add("popup-link", "swiper-slide");
          imgItem.innerHTML = `
                        <img src=${item} alt='img' class='w-full h-full object-cover' />
                    `;

          // Add img 1st and 2nd,... to listImg6
          if (index === 0 || index === 1) {
            imgItem.classList.add(
              "md:row-span-2",
              "row-span-1",
              "col-span-1",
              "max-md:aspect-[3/4]",
              "lg:rounded-[20px]",
              "rounded-xl",
              "overflow-hidden"
            );
          }

          // Add img 3rd and 4th,... to listImg6
          if (productMain.images.length < 4) {
            console.log(false);
            if (index === 2) {
              imgItem.classList.add(
                "md:row-span-2",
                "row-span-1",
                "col-span-1",
                "max-md:aspect-[3/4]",
                "lg:rounded-[20px]",
                "rounded-xl",
                "overflow-hidden"
              );
            }
          } else {
            console.log(true);
            if (index === 2 || index === 3) {
              imgItem.classList.add(
                "row-span-1",
                "md:col-span-1",
                "col-span-2",
                "aspect-[5/3]",
                "lg:rounded-[20px]",
                "rounded-xl",
                "overflow-hidden"
              );
            }
          }
          const imgItemClone2 = imgItem.cloneNode(true);

          listImg6.appendChild(imgItem);
          listImgMain.appendChild(imgItemClone2);
        });
      }

      // product sale
      const productSale = productDetail.querySelector(".sold-block");
      if (productSale) {
        const percentSold = productSale.querySelector(".percent-sold");
        const percentSoldNumber = productSale.querySelector(
          ".percent-sold-number"
        );
        const remainingNumber = productSale.querySelector(".remaining-number");

        percentSold.style.width =
          Math.floor((productMain.sold / productMain.quantity) * 100) + "%";
        percentSoldNumber.innerHTML =
          Math.floor((productMain.sold / productMain.quantity) * 100) +
          "% Sold -";
        remainingNumber.innerHTML = productMain.quantity - productMain.sold;
      }

      // show, hide popup img
      const imgItems = productDetail.querySelectorAll(
        ".list-img .popup-link>img"
      );
      const closePopupBtn = productDetail.querySelector(
        ".list-img .popup-img .close-popup-btn"
      );

      imgItems.forEach((item, index) => {
        item.addEventListener("click", () => {
          console.log(index);
          popupImg.classList.add("open");

          // list-img popup
          var listPopupImg = new Swiper(".popup-img", {
            loop: true,
            clickable: true,
            slidesPerView: 1,
            spaceBetween: 0,
            navigation: {
              nextEl: ".swiper-button-next",
              prevEl: ".swiper-button-prev",
            },
            initialSlide: index,
          });
        });
      });

      closePopupBtn.addEventListener("click", () => {
        popupImg.classList.remove("open");
      });

      // infor
      productDetail
        .querySelector(".product-infor")
        .setAttribute("data-item", productId);
      productDetail.querySelector(".product-category").innerHTML =
        productMain.category;
      productDetail.querySelector(".product-name").innerHTML = productMain.name;
      if (productMain.outOfStock && !productMain.discontinued) {
        productDetail.querySelector(".product-name").insertAdjacentHTML(
          "beforeend",
          ' <span class="caption1 font-semibold text-white bg-black px-3 py-0.5 rounded-full inline-block ml-2">Out of Stock</span>'
        );
      }
    const tempDiv = document.createElement("div");
tempDiv.innerHTML = productMain.description;

productDetail.querySelector(".product-description").textContent =
  tempDiv.textContent || tempDiv.innerText || "";
      productDetail.querySelector(".productDesc").innerHTML =
        productMain.description;
      productDetail.querySelector(".product-price").innerHTML =
        "₹" + parseFloat(productMain.price).toFixed(2);
      const discountPercent = Math.floor(
        100 - (productMain.price / productMain.originPrice) * 100
      );
      if (discountPercent > 0) {
        productDetail.querySelector(".product-origin-price").innerHTML =
          "<del>₹" + parseFloat(productMain.originPrice).toFixed(2) + "</del>";
        productDetail.querySelector(".product-origin-price").style.display = "";
        productDetail.querySelector(".product-sale").innerHTML =
          "-" + discountPercent + "%";
        productDetail.querySelector(".product-sale").style.display = "";
      } else {
        productDetail.querySelector(".product-origin-price").innerHTML = "";
        productDetail.querySelector(".product-origin-price").style.display = "none";
        productDetail.querySelector(".product-sale").innerHTML = "";
        productDetail.querySelector(".product-sale").style.display = "none";
      }

      window.baseProductPricing = {
        price: productMain.price,
        originPrice: productMain.originPrice,
      };
      document.dispatchEvent(new CustomEvent("baseProductPricingReady"));

      productMain?.variation?.map((item) => {
        const colorItem = document.createElement("div");
        colorItem.classList.add(
          "color-item",
          "w-12",
          "h-12",
          "rounded-xl",
          "duration-300",
          "relative",
          "cursor-pointer",
          "border",
          "border-transparent"
        );
        // Use colorImage for the swatch. item.colorImage is set in Test.php
        colorItem.innerHTML = `
                        <img src='${item.colorImage}' alt='${item.color}' class='rounded-xl w-full h-full object-cover' />
                        <div
                            class="tag-action bg-black text-white caption2 capitalize px-1.5 py-0.5 rounded-sm">
                            ${item.colorName || item.color}
                        </div>
                    `;

        // Click Event for Filtering Images
        colorItem.addEventListener("click", () => {
          // 1. Highlight selection
          document.querySelectorAll(".color-item").forEach(el => el.classList.remove("border-black"));
          colorItem.classList.add("border-black");

          // 2. Get Color
          const selectedColor = item.color;

          // 3. Get Images for this color
          // productMain.colorImages is the map [color] => [url1, url2...]
          let newImages = [];

          if (productMain.colorImages && productMain.colorImages[selectedColor]) {
            newImages = productMain.colorImages[selectedColor];
          } else {
            // Fallback if no specific images or key not found: show all or main images
            // But user wants "only show product red color images"
            // If empty, maybe show only the variation image?
            newImages = [item.colorImage];
          }

          // 4. Update Swiper
          updateProductSwiper(newImages);
        });

        productDetail.querySelector(".choose-color .list-color") &&
          productDetail
            .querySelector(".choose-color .list-color")
            .appendChild(colorItem);
      });

      // Helper function to update Swiper slides
      function updateProductSwiper(images) {
        const listImg2 = productDetail.querySelector(".featured-product .list-img .mySwiper2 .swiper-wrapper");
        const listImg = productDetail.querySelector(".featured-product .list-img .mySwiper .swiper-wrapper");
        const listImgMain = productDetail.querySelector(".featured-product .list-img .popup-img .swiper-wrapper");

        if (!listImg2 || !listImg) return;

        // Clear existing
        listImg2.innerHTML = "";
        listImg.innerHTML = "";
        listImgMain.innerHTML = "";

        images.forEach((url, index) => {
          // Slide for Main Swiper
          const slide = document.createElement("div");
          slide.classList.add("swiper-slide", "popup-link");
          slide.innerHTML = `<img src=${url} alt='img' class='w-full aspect-[3/4] object-cover' />`;

          // Slide for Thumb Swiper
          const thumb = slide.cloneNode(true);
          thumb.classList.remove("popup-link");

          // Slide for Popup Swiper
          const popupSlide = slide.cloneNode(true);

          listImg2.appendChild(slide);
          listImg.appendChild(thumb);
          listImgMain.appendChild(popupSlide);

          // Add click to thumb to change main
          thumb.addEventListener("click", () => {
            swiper2.slideTo(index);
          });

          // Add click to popup
          slide.addEventListener("click", () => {
            popupImg.classList.add("open");
            // Re-init popup swiper or slide to index
            // list-img popup
            var listPopupImg = new Swiper(".popup-img", {
              loop: true,
              clickable: true,
              slidesPerView: 1,
              spaceBetween: 0,
              navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
              },
              initialSlide: index,
            });
          });
        });

        // Re-initialize Swipers if needed, or update() 
        // Assuming globals or re-instantiate. 
        // Looking at existing code, swiper2 might be global or local but not stored in var here.
        // Let's try to update existing swipers if possible, or just manipulating DOM might work if observer is on.
        // BUT, usually one needs to swiper.update().
        // Existing code defines `var swiper2` somewhere? Ah, I don't see `var swiper2 = new Swiper` in the previous `view_file`.
        // I see `listImg2` variable, but not the Swiper instantiation in the part I viewed?
        // I see `var swiperCollection`... 

        // Let's assume Swipers are initialized globally or we need to re-init.
        // To be safe, I'll check if Swiper instances exist on elements and call update.

        if (document.querySelector(".mySwiper2").swiper) {
          document.querySelector(".mySwiper2").swiper.update();
          document.querySelector(".mySwiper2").swiper.slideTo(0);
        } else if (typeof swiper2 !== 'undefined') {
          swiper2.update();
          swiper2.slideTo(0);
        }

        if (document.querySelector(".mySwiper").swiper) {
          document.querySelector(".mySwiper").swiper.update();
          document.querySelector(".mySwiper").swiper.slideTo(0);
        } else if (typeof swiper !== 'undefined') {
          swiper.update();
          swiper.slideTo(0);
        }
      }

      productMain?.sizes?.map((item, index) => {
        const sizeItem = document.createElement("div");
        if (item !== "freesize") {
          sizeItem.classList.add(
            "size-item",
            "w-12",
            "h-12",
            "flex",
            "items-center",
            "justify-center",
            "text-button",
            "rounded-full",
            "bg-white",
            "border",
            "border-line"
          );
        } else {
          sizeItem.classList.add(
            "size-item",
            "px-3",
            "py-2",
            "flex",
            "items-center",
            "justify-center",
            "text-button",
            "rounded-full",
            "bg-white",
            "border",
            "border-line"
          );
        }
        sizeItem.setAttribute("key", index);
        sizeItem.innerHTML = item;

        productDetail.querySelector(".choose-size .list-size") &&
          productDetail
            .querySelector(".choose-size .list-size")
            .appendChild(sizeItem);
      });

      const listCategory = productDetail.querySelector(".list-category");

      listCategory.innerHTML = `
            <a href="shop-breadcrumb1.html" class="text-secondary">${productMain.category}</a>
            `;

      const listTag = productDetail.querySelector(".list-tag");

      listTag.innerHTML = `
            <a href="shop-breadcrumb1.html" class="text-secondary">${productMain.type}</a>
            `;
    })
    .catch((error) => console.error("Error fetching products:", error));
}

// desc-tab
const descTabItem = document.querySelectorAll(".desc-tab .tab-item");
const descItem = document.querySelectorAll(".desc-tab .desc-block .desc-item");

descTabItem.forEach((tabItems) => {
  const handleOpen = () => {
    let dataItem = tabItems.innerHTML.replace(/\s+/g, "");

    descItem.forEach((item) => {
      if (item.getAttribute("data-item") === dataItem) {
        item.classList.add("open");
      } else {
        item.classList.remove("open");
      }
    });
  };

  if (tabItems.classList.contains("active")) {
    handleOpen();
  }

  tabItems.addEventListener("click", handleOpen);
});

// list-img on-sale
var swiperListImgOnSale = new Swiper(".swiper-img-on-sale", {
  loop: true,
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  clickable: true,
  slidesPerView: 2,
  spaceBetween: 0,
  breakpoints: {
    576: {
      slidesPerView: 2,
    },
    640: {
      slidesPerView: 2,
    },
    768: {
      slidesPerView: 2,
    },
    992: {
      slidesPerView: 3,
    },
    1290: {
      slidesPerView: 3,
    },
    2000: {
      slidesPerView: 4,
    },
  },
});

// list-img review
var swiperImgReview = new Swiper(".swiper-img-review", {
  loop: true,
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  clickable: true,
  slidesPerView: 3,
  spaceBetween: 12,
  breakpoints: {
    576: {
      slidesPerView: 4,
      spaceBetween: 16,
    },
    640: {
      slidesPerView: 5,
      spaceBetween: 16,
    },
    768: {
      slidesPerView: 4,
      spaceBetween: 16,
    },
    992: {
      slidesPerView: 5,
      spaceBetween: 20,
    },
    1100: {
      slidesPerView: 5,
      spaceBetween: 20,
    },
    1290: {
      slidesPerView: 7,
      spaceBetween: 20,
    },
  },
});

// Redirect filter type product-sidebar
const typeItems = document.querySelectorAll(
  ".product-detail.sidebar .list-type .item"
);

typeItems.forEach((item) => {
  item.addEventListener("click", () => {
    const type = item.getAttribute("data-item");
    window.location.href = `shop-breadcrumb1.html?type=${type}`;
  });
});
