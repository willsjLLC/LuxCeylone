<div id="preloader">
    <div class="loader-wrapper">
        <!-- Left: Spinning circle -->
        {{-- <div class="left-circle">
            <svg class="spinner" viewBox="0 0 50 50">
                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
            </svg>
        </div> --}}

        <!-- Right: Optional pulse or logo -->
        <div class="right-circle">
            <svg class="pulse-ring" viewBox="0 0 50 50">
                <circle class="path-alt" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
            </svg>
        </div>
    </div>
</div>

<style>#preloader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: linear-gradient(135deg, #ffffffb9, #ffffff);
    display: flex;
    justify-content: center;
    align-items: center;
    transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.5s;
}

#preloader.fade-out {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

/* Two-column loader layout */
.loader-wrapper {
    display: flex;
    align-items: center;
    gap: 50px;
    animation: fadeIn 0.6s ease-in-out;
}

/* Spinner on the left */
.left-circle, .right-circle {
    width: 80px;
    height: 80px;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Rotate spinner */
.spinner {
    width: 100%;
    height: 100%;
    animation: rotate 1.5s linear infinite;
}

/* Spinner stroke style */
.path {
    stroke: #00515f;
    stroke-linecap: round;
    stroke-width: 5;
    fill: none;
    animation: dash 1.5s ease-in-out infinite;
}

/* Optional right pulse ring */
.pulse-ring {
    width: 100%;
    height: 100%;
    animation: pulse 1.5s ease-in-out infinite;
}

.path-alt {
    stroke: #00a8cc;
    stroke-linecap: round;
    stroke-width: 5;
    fill: none;
    opacity: 0.6;
}

/* Animations */
@keyframes rotate {
    100% {
        transform: rotate(360deg);
    }
}

@keyframes dash {
    0% {
        stroke-dasharray: 1, 150;
        stroke-dashoffset: 0;
    }
    50% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -35px;
    }
    100% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -124px;
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.6;
    }
    50% {
        transform: scale(1.15);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}


</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const preloader = document.getElementById('preloader');
        const containers = ['productContainer', 'cartContainer'];

        const checkIfLoaded = () => {
            let allLoaded = true;

            containers.forEach(containerId => {
                const container = document.getElementById(containerId);
                if (container && container.style.display === 'none') {
                    allLoaded = false;
                }
            });

            if (allLoaded) {
                preloader.style.display = 'none';
            }
        };

        setTimeout(() => {
            containers.forEach(containerId => {
                const container = document.getElementById(containerId);
                if (container) {
                    container.style.display = 'block';
                }
            });

            checkIfLoaded();
        }, 2000);

        const searchInput = document.getElementById('searchInput');
        const originalContent = document.getElementById('productContainer').innerHTML;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();

            if (query.length === 0) {
                productContainer.innerHTML = originalContent;
                return;
            }

            const categorySections = document.querySelectorAll('.category-section');
            let hasResults = false;
            let filteredContent = '';

            categorySections.forEach(section => {
                const categoryName = section.getAttribute('data-category');
                const products = section.querySelectorAll('.product-item');
                let matchingProducts = [];

                products.forEach(product => {
                    const productTitle = product.getAttribute('data-name');
                    if (productTitle.includes(query)) {
                        matchingProducts.push(product.outerHTML);
                    }
                });

                if (matchingProducts.length > 0) {
                    hasResults = true;
                }
                filteredContent += `
                    <div class="category-section mb-5" data-category="${categoryName}">
                        <a href="${section.querySelector('a').href}">
                            <h4 class="mb-4 border-bottom pb-2">${section.querySelector('h4').textContent}</h4>
                        </a>
                        <div class="scroll-container">
                            ${matchingProducts.join('')}
                        </div>
                    </div>
                `;
            });

            if (hasResults) {
                productContainer.innerHTML = filteredContent;
            } else {
                productContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info">No matching products found.</div>
                    </div>
                `;
            }
        });

        const popup = document.getElementById('popupBox');
        setTimeout(() => {
            popup.style.display = 'flex';
            setTimeout(() => {
                popup.classList.add('show');
            }, 10);
        }, 3000);

        window.closePopup = function() {
            popup.style.opacity = '0';
            setTimeout(() => {
                popup.classList.remove('show');
            }, 500);
        };
    });
</script>
