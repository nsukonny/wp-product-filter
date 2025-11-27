document.addEventListener("DOMContentLoaded", async function () {
    let filterWrapper = document.querySelector(".wp-product-filter");

    if (!filterWrapper) {
        return;
    }

    let productsData = await loadProductsData();
    document.addEventListener("click", async function (e) {

        const clickedFilterBtn = e.target;
        if (clickedFilterBtn.tagName.toLowerCase() !== 'button' || !clickedFilterBtn.closest('.wp-product-filter')) {
            return;
        }

        let parentProduct = clickedFilterBtn.closest(".wp-product-filter__product");
        if (!parentProduct) {
            return;
        }

        const productId = parentProduct.dataset.id;
        const buttonType = clickedFilterBtn.getAttribute("data-type");

        let oldSelectedBtn = parentProduct.querySelector(`button.selected[data-type="${buttonType}"]`);
        if (oldSelectedBtn) {
            oldSelectedBtn.classList.remove("selected");
        }
        clickedFilterBtn.classList.toggle("selected");

        updateProduct(productId, productsData);
    });
});

/**
 * Load all products by AJAX
 *
 * @returns {*[]}
 */
const loadProductsData = async () => {
    try {
        const payload = new URLSearchParams({
            action: 'wpp_get_products',
            _wpnonce: framework._ajax_nonce,
        });
        const response = await fetch(framework.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: payload.toString(),
            credentials: 'same-origin'
        });

        if (response.ok) {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                const responseResult = await response.json();
                return responseResult.data.products || [];
            }
        }
    } catch (err) {
        console.warn("Ошибка при отправке формы:", err);
    }

    return [];
}

/**
 * Calculate product UI by combinations
 *
 * @param productId
 * @param productsData
 */
const updateProduct = (productId, productsData) => {
    /**
     * Update product image, price, stocks by selected combinations from size and color
     */
    const productData = productsData[productId];
    if (!productData) {
        return;
    }

    let productBlock = document.querySelector(`.wp-product-filter__product[data-id="${productId}"]`);
    if (!productBlock) {
        return;
    }

    let colorBlock = productBlock.querySelector(`.product-filter__colors button[data-color].selected`);
    let selectedColor = colorBlock ? colorBlock.dataset.color : null;

    updateProductSizesByColor(productBlock, productData, selectedColor);
    updateProductUI(productBlock, productData);
}

/**
 * Update product sizes availability by selected color
 *
 * @param productBlock
 * @param productData
 * @param selectedColor
 */
const updateProductSizesByColor = (productBlock, productData, selectedColor) => {
    let sizeButtons = productBlock.querySelectorAll(`.product-filter__sizes button[data-size]`),
        compatibility = productData.compatibility,
        combinations = productData.combinations;

    sizeButtons.forEach(sizeBtn => {
        let size = sizeBtn.dataset.size;
        let combination = combinations[`${selectedColor}_${size}`] || {};
        let isAvailable = !!(combination && combination.available);
        let isCompatibilities = compatibility[selectedColor] && compatibility[selectedColor].includes(size);
        if (isAvailable && isCompatibilities) {
            sizeBtn.disabled = false;
            sizeBtn.classList.remove("disabled");
        } else {
            sizeBtn.disabled = true;
            sizeBtn.classList.add("disabled");
            sizeBtn.classList.remove("selected");
        }
    });

    let selectedSizeBtn = productBlock.querySelector(`.product-filter__sizes button[data-size].selected`);
    if (selectedSizeBtn && selectedSizeBtn.disabled) {
        selectedSizeBtn.classList.remove("selected");
    }

    let firstAvailableSizeBtn = productBlock.querySelector(`.product-filter__sizes button[data-size]:not(.disabled)`);
    if ((!selectedSizeBtn || selectedSizeBtn.disabled) && firstAvailableSizeBtn) {
        firstAvailableSizeBtn.classList.add("selected");
    }
}

/**
 * Update product UI elements like image, price, stocks
 *
 * @param productBlock
 * @param productData
 */
const updateProductUI = (productBlock, productData) => {
    let colorBlock = productBlock.querySelector(`.product-filter__colors button[data-color].selected`);
    let selectedColor = colorBlock ? colorBlock.dataset.color : null;

    let sizeBlock = productBlock.querySelector(`.product-filter__sizes button[data-size].selected`);
    let selectedSize = sizeBlock ? sizeBlock.dataset.size : null;

    let combinations = productData.combinations;
    let combination = combinations[`${selectedColor}_${selectedSize}`] || {};
    if (!combination) {
        return;
    }

    let productImage = productBlock.querySelector(`.wp-product-filter__product-image img`);
    if (combination.image) {
        productImage.src = combination.image;
    }

    let productPrice = productBlock.querySelector(`.wp-product-filter__product-price`);
    if (combination.price) {
        productPrice.textContent = combination.price + 'RUB';

    }

    let productStock = productBlock.querySelector(`.wp-product-filter__product-stock`);
    if (combination.stock > 0) {
        productStock.textContent = `In Stock: ${combination.stock}`;
        productPrice.disabled = false;
    } else {
        productStock.textContent = `Out of Stock`;
        productPrice.disabled = true;
    }
}