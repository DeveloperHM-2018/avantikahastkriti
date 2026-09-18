 /**
 * Product Variant Manager
 * Handles variant selection (size, color) on product detail page
 * Features: Color filtering by size, actual color display, tooltips
 */

class ProductVariantManager {
    constructor(productId) {
        this.productId = productId;
        this.variants = [];
        this.selectedVariant = null;
        this.selectedSize = null;
        this.selectedColor = null;

        document.addEventListener('baseProductPricingReady', () => {
            if (this.selectedVariant) {
                this.updatePrice();
            }
        });

        this.init();
    }

    async init() {
        await this.fetchVariants();
        if (this.variants.length > 0) {
            this.renderSizes();
            this.renderColors();
            this.attachEventListeners();
            this.selectDefaultVariant();
        }
    }

    async fetchVariants() {
        try {
            const response = await fetch(`${BASE_URL}Web/get_product_variants/${this.productId}`);
            const data = await response.json();

            if (data.success && data.variants && data.variants.length > 0) {
                this.variants = data.variants;
                this.colorImagesMap = data.color_images_map || {};
                this.colorImagesAll = data.color_images_all || {};
                console.log('Loaded variants:', this.variants);

                document.getElementById('variant-section').style.display = 'block';
            }
        } catch (error) {
            console.error('Error fetching variants:', error);
        }
    }

    getUniqueSizes() {
        const sizes = this.variants.map(v => v.size).filter(Boolean);
        return [...new Set(sizes)];
    }

    getAvailableColors() {
        if (!this.selectedSize) {
            // No size selected - show all colors
            const colors = this.variants.map(v => v.color).filter(Boolean);
            return [...new Set(colors)];
        }

        // Filter colors by selected size
        const colors = this.variants
            .filter(v => v.size === this.selectedSize)
            .map(v => v.color)
            .filter(Boolean);
        return [...new Set(colors)];
    }

    getColorHex(colorName) {
        const colorMap = {
            'black': '#000000',
            'white': '#FFFFFF',
            'red': '#FF0000',
            'blue': '#0000FF',
            'green': '#00FF00',
            'yellow': '#FFFF00',
            'orange': '#FFA500',
            'purple': '#800080',
            'pink': '#FFC0CB',
            'brown': '#A52A2A',
            'gray': '#808080',
            'grey': '#808080',
            'navy': '#000080',
            'beige': '#F5F5DC',
            'maroon': '#800000',
            'olive': '#808000',
            'cyan': '#00FFFF',
            'magenta': '#FF00FF',
            'silver': '#C0C0C0',
            'gold': '#FFD700',
            'dark blue': '#00008B',
            'light blue': '#ADD8E6',
            'sky blue': '#87CEEB',
            'dark green': '#006400',
            'light green': '#90EE90',
            'dark red': '#8B0000',
            'light pink': '#FFB6C1',
            'hot pink': '#FF69B4',
            'dark gray': '#A9A9A9',
            'light gray': '#D3D3D3',
        };

        const normalized = colorName.toLowerCase().trim();

        // Check if hex code
        if (normalized.startsWith('#')) {
            return normalized;
        }

        // Return mapped color or generate from string
        return colorMap[normalized] || this.stringToColor(colorName);
    }

    stringToColor(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        const c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
        return '#' + '00000'.substring(0, 6 - c.length) + c;
    }

    renderSizes() {
        const sizes = this.getUniqueSizes();
        if (sizes.length === 0) return;

        const sizeList = document.getElementById('size-list');
        const sizeSelection = document.querySelector('.size-selection');

        if (!sizeList || !sizeSelection) return;

        sizeSelection.style.display = 'block';

        sizeList.innerHTML = sizes.map((size, i) => `
            <div class="size-item px-4 py-2 flex items-center justify-center text-button rounded-full bg-white border border-line cursor-pointer hover:bg-black hover:text-white transition-all duration-300" data-size="${size}">
                ${size}
            </div>
        `).join('');
    }

    renderColors() {
        const colors = this.getAvailableColors();
        if (colors.length === 0) return;

        const colorList = document.getElementById('color-list');
        const colorSelection = document.querySelector('.color-selection');

        if (!colorList || !colorSelection) return;

        colorSelection.style.display = 'block';

        colorList.innerHTML = colors.map((color, i) => {
            const hexColor = this.getColorHex(color);
            const isLightColor = this.isLightColor(hexColor);

            // Check if we have an image for this color
            const imageUrl = this.colorImagesMap && this.colorImagesMap[color] ? this.colorImagesMap[color] : null;

            let contentHtml = '';
            if (imageUrl) {
                contentHtml = `
                    <div class="color-item w-12 h-12 rounded-lg border border-line cursor-pointer hover:border-black transition-all relative overflow-hidden"
                         data-color="${color}"
                         title="${color}">
                         <img src="${imageUrl}" alt="${color}" class="w-full h-full object-cover">
                    </div>
                `;
            } else {
                contentHtml = `
                    <div class="color-item w-10 h-10 rounded-full border-2 border-line cursor-pointer hover:border-black transition-all relative"
                         data-color="${color}"
                         style="background-color: ${hexColor}; ${isLightColor ? 'border-width: 2px;' : ''}"
                         title="${color}">
                        ${isLightColor ? '<div class="absolute inset-0 rounded-full border border-gray-300"></div>' : ''}
                    </div>
                `;
            }

            return `
                <div class="flex flex-col items-center gap-1 group">
                    ${contentHtml}
                    <span class="text-xs text-center text-secondary capitalize group-hover:text-black cursor-pointer" onclick="this.parentNode.querySelector('.color-item').click()">${color}</span>
                </div>
            `;
        }).join('');
    }

    isLightColor(hex) {
        // Convert hex to RGB
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);

        // Calculate perceived brightness
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        return brightness > 200;
    }

    attachEventListeners() {
        this.attachSizeListeners();
        this.attachColorListeners();
    }

    attachSizeListeners() {
        const sizeItems = document.querySelectorAll('.size-item');
        sizeItems.forEach(item => {
            item.addEventListener('click', (e) => {
                // Remove active class from all
                sizeItems.forEach(s => {
                    s.classList.remove('active', 'bg-black', 'text-white');
                    s.classList.add('bg-white', 'text-button');
                });

                // Add active class to clicked
                const clicked = e.currentTarget;
                clicked.classList.add('active', 'bg-black', 'text-white');
                clicked.classList.remove('bg-white', 'text-button');

                // Update selected size
                this.selectedSize = clicked.dataset.size;

                // Clear color selection when size changes
                this.selectedColor = null;
                document.querySelectorAll('.color-item').forEach(c => {
                    c.classList.remove('active', 'border-2', 'border-black');
                    c.classList.add('border-line');
                    c.style.borderColor = '';
                    c.style.borderWidth = '';
                });

                // Re-render colors based on new size availability
                this.renderColors();

                // Re-attach listeners to new color elements
                this.attachColorListeners();

                this.updateSelectedVariant();
            });
        });
    }

    attachColorListeners() {
        document.querySelectorAll('.color-item').forEach(item => {
            item.addEventListener('click', (e) => {
                // Update UI
                document.querySelectorAll('.color-item').forEach(i => {
                    i.classList.remove('active');
                    i.style.borderColor = '';
                    i.style.borderWidth = '';
                    if (i.querySelector('img')) {
                        i.classList.remove('border-2', 'border-black');
                        i.classList.add('border', 'border-line');
                    }
                });

                e.currentTarget.classList.add('active');
                if (e.currentTarget.querySelector('img')) {
                    e.currentTarget.classList.remove('border-line');
                    e.currentTarget.classList.add('border-2', 'border-black'); // highlight image
                } else {
                    e.currentTarget.style.borderColor = '#000';
                    e.currentTarget.style.borderWidth = '3px';
                }

                // Update selection
                this.selectedColor = e.currentTarget.dataset.color;
                this.updateSelectedVariant();

                // Update Image Slider
                this.updateImageSlider(this.selectedColor);
            });
        });
    }

    updateImageSlider(color) {
        if (this.colorImagesAll && this.colorImagesAll[color]) {
            const images = this.colorImagesAll[color];

            // Logic to update Swiper - copied/adapted from product-detail.js logic
            // We need to access the swiper instances.
            // Assuming they are available globally or we can find them in DOM.

            const listImg2 = document.querySelector(".mySwiper2 .swiper-wrapper");
            const listImg = document.querySelector(".mySwiper .swiper-wrapper");
            const listImgMain = document.querySelector(".popup-img .swiper-wrapper");

            if (!listImg2 || !listImg) return;

            // Clear existing
            listImg2.innerHTML = "";
            listImg.innerHTML = "";
            if (listImgMain) listImgMain.innerHTML = "";

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
                if (listImgMain) listImgMain.appendChild(popupSlide);

                // Add click to thumb
                thumb.addEventListener("click", () => {
                    if (document.querySelector(".mySwiper2").swiper) {
                        document.querySelector(".mySwiper2").swiper.slideTo(index);
                    } else if (typeof swiper2 !== 'undefined') {
                        swiper2.slideTo(index);
                    }
                });
            });

            // Update Swipers
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
    }

    updateSelectedVariant() {
        console.log('UpdateSelectedVariant: Size=', this.selectedSize, 'Color=', this.selectedColor);

        // Find matching variant
        this.selectedVariant = this.variants.find(v => {
            // Use String conversion for robust comparison (handles '30' vs 30)
            const sizeMatch = !this.selectedSize || String(v.size) === String(this.selectedSize);
            const colorMatch = !this.selectedColor || String(v.color) === String(this.selectedColor);
            return sizeMatch && colorMatch && v.is_active == 1;
        });

        console.log('Found variant match:', this.selectedVariant);

        if (this.selectedVariant) {
            this.displayVariantInfo();
            this.updatePrice();
        }
    }

    displayVariantInfo() {
        const variantInfo = document.getElementById('variant-info');
        const variantText = document.getElementById('selected-variant-text');
        const variantStock = document.getElementById('variant-stock');

        if (!variantInfo || !variantText) return;

        variantInfo.style.display = 'block';

        let text = [];
        if (this.selectedVariant.size) text.push(`Size: ${this.selectedVariant.size}`);
        if (this.selectedVariant.color) text.push(`Color: ${this.selectedVariant.color}`);

        variantText.textContent = text.join(' | ');

        if (variantStock) {
            const stock = this.selectedVariant.stock_quantity;
            if (stock !== null && stock !== undefined) {
                variantStock.textContent = stock > 0 ? `Stock: ${stock} available` : 'Out of stock';
                variantStock.className = stock > 0 ? 'text-sm text-success mt-1' : 'text-sm text-red mt-1';
            } else {
                variantStock.textContent = 'Stock: Available';
                variantStock.className = 'text-sm text-secondary mt-1';
            }
        }
    }

    updatePrice() {
        const priceElement = document.getElementById('variant-price');
        const mainPriceElement = document.querySelector('.product-price');
        const originPriceElement = document.querySelector('.product-origin-price');
        const saleElement = document.querySelector('.product-sale');

        if (this.selectedVariant && this.selectedVariant.price) {
            const price = parseFloat(this.selectedVariant.price);
            if (priceElement) {
                priceElement.textContent = `₹${price.toFixed(2)}`;
            }
            if (mainPriceElement) {
                mainPriceElement.innerHTML = `₹${price.toFixed(2)}`;
            }

            const originPrice = window.baseProductPricing
                ? parseFloat(window.baseProductPricing.originPrice)
                : 0;
            const discountPercent = Math.floor(100 - (price / originPrice) * 100);

            if (originPriceElement && saleElement) {
                if (originPrice > 0 && discountPercent > 0) {
                    originPriceElement.innerHTML = `<del>₹${originPrice.toFixed(2)}</del>`;
                    originPriceElement.style.display = "";
                    saleElement.innerHTML = `-${discountPercent}%`;
                    saleElement.style.display = "";
                } else {
                    originPriceElement.innerHTML = "";
                    originPriceElement.style.display = "none";
                    saleElement.innerHTML = "";
                    saleElement.style.display = "none";
                }
            }
        }
    }

    getSelectedVariantId() {
        return this.selectedVariant ? this.selectedVariant.variant_id : null;
    }

    getSelectedVariantData() {
        return this.selectedVariant;
    }

    hasVariants() {
        return this.variants.length > 0;
    }

    isVariantSelected() {
        return this.selectedVariant !== null;
    }

    selectDefaultVariant() {
        // Find first active and in-stock variant
        // Some variants might have null stock_quantity if not tracked, treat as in-stock
        const defaultVariant = this.variants.find(v =>
            v.is_active == 1 &&
            (v.stock_quantity === null || v.stock_quantity === undefined || v.stock_quantity > 0)
        );

        if (defaultVariant) {
            console.log('Selecting default variant:', defaultVariant);

            // 1. Select Size first if it exists
            if (defaultVariant.size) {
                const sizeItem = document.querySelector(`.size-item[data-size="${defaultVariant.size}"]`);
                if (sizeItem) {
                    sizeItem.click();
                }
            }

            // 2. Select Color (Size click re-renders colors, but synchronously)
            if (defaultVariant.color) {
                const colorItem = document.querySelector(`.color-item[data-color="${defaultVariant.color}"]`);
                if (colorItem) {
                    colorItem.click();
                }
            }
        }
    }
}

// Auto-initialize on product detail page
if (document.querySelector('.product-detail')) {
    const urlParams = new URLSearchParams(window.location.search);
    const pathSegments = window.location.pathname.split('/');
    const lastSegment = pathSegments[pathSegments.length - 1];
    const parts = lastSegment.split('-');
    const potentialId = parts[parts.length - 1];

    const productId =
        urlParams.get("id") !== null
            ? urlParams.get("id")
            : (potentialId && !isNaN(potentialId) ? potentialId : null);

    if (productId) {
        window.variantManager = new ProductVariantManager(productId);
    }
}
