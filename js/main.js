// KabawBaka Main JavaScript File

document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    initPasswordToggles();

    // Form validation
    initFormValidation();

    // Smooth scrolling for anchor links
    initSmoothScrolling();

    // Mobile menu toggle (if needed)
    initMobileMenu();

    // Dynamic marketplace filtering
    initMarketplaceFilters();

    // Initialize any animations
    initAnimations();
});

// Password Toggle Functionality
function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.toggle-password');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle icon (you can add eye/eye-slash icons)
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    });
}

// Form Validation
function initFormValidation() {
    const loginForm = document.querySelector('#loginForm');
    const registerForm = document.querySelector('#registerForm');
    const tipForm = document.querySelector('#tipForm');

    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);
    }

    if (registerForm) {
        registerForm.addEventListener('submit', validateRegisterForm);
    }

    if (tipForm) {
        tipForm.addEventListener('submit', validateTipForm);
    }
}

function validateLoginForm(e) {
    const email = document.querySelector('#loginEmail').value;
    const password = document.querySelector('#loginPassword').value;

    if (!email || !password) {
        alert('Please fill in all fields');
        e.preventDefault();
        return false;
    }

    if (!isValidEmail(email)) {
        alert('Please enter a valid email address');
        e.preventDefault();
        return false;
    }

    return true;
}

function validateRegisterForm(e) {
    const fullName = document.querySelector('#fullName').value;
    const email = document.querySelector('#registerEmail').value;
    const password = document.querySelector('#registerPassword').value;
    const confirmPassword = document.querySelector('#confirmPassword').value;
    const contact = document.querySelector('#contactNumber').value;
    const registerBtn = document.querySelector('#registerBtn');

    if (!fullName || !email || !password || !confirmPassword) {
        alert('Please fill in all required fields');
        e.preventDefault();
        return false;
    }

    if (!isValidEmail(email)) {
        alert('Please enter a valid email address');
        e.preventDefault();
        return false;
    }

    if (password.length < 6) {
        alert('Password must be at least 6 characters long');
        e.preventDefault();
        return false;
    }

    if (password !== confirmPassword) {
        alert('Passwords do not match');
        e.preventDefault();
        return false;
    }

    if (contact && !isValidPhone(contact)) {
        alert('Please enter a valid phone number');
        e.preventDefault();
        return false;
    }

    // Disable button and show loading state
    if (registerBtn) {
        registerBtn.disabled = true;
        registerBtn.textContent = 'Sending OTP...';
    }

    // Simulate OTP sending (in real implementation, this would call an API)
    setTimeout(() => {
        const otp = Math.floor(100000 + Math.random() * 900000); // Generate 6-digit OTP
        const userOtp = prompt(`OTP sent to ${contact || email}. Enter OTP to continue:`);

        if (userOtp && userOtp == otp) {
            alert('Registration successful!');
            // Here you would normally submit the form
            // e.target.submit();
        } else {
            alert('Invalid OTP. Please try again.');
            if (registerBtn) {
                registerBtn.disabled = false;
                registerBtn.textContent = 'Register';
            }
            e.preventDefault();
            return false;
        }
    }, 1000);

    e.preventDefault(); // Prevent immediate form submission
    return false;
}

function validateTipForm(e) {
    const content = document.querySelector('#tipContent').value;

    if (!content.trim()) {
        alert('Please enter your tip');
        e.preventDefault();
        return false;
    }

    if (content.length < 10) {
        alert('Tip must be at least 10 characters long');
        e.preventDefault();
        return false;
    }

    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[0-9\-\(\)\s]+$/;
    return phoneRegex.test(phone);
}

// Smooth Scrolling
function initSmoothScrolling() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');

    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Mobile Menu Toggle
function initMobileMenu() {
    // Create mobile menu button if it doesn't exist
    const header = document.querySelector('header');
    if (header && window.innerWidth <= 768) {
        const mobileMenuBtn = document.createElement('button');
        mobileMenuBtn.className = 'mobile-menu-btn';
        mobileMenuBtn.innerHTML = '☰';
        mobileMenuBtn.style.cssText = `
            display: none;
            background: none;
            border: none;
            color: #F5F2E7;
            font-size: 1.5rem;
            cursor: pointer;
            margin-left: auto;
        `;

        const nav = document.querySelector('nav');
        if (nav) {
            nav.style.cssText = `
                display: flex;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(45, 71, 57, 0.95);
                flex-direction: column;
                padding: 20px;
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            `;

            mobileMenuBtn.addEventListener('click', function() {
                const isOpen = nav.style.visibility === 'visible';
                nav.style.transform = isOpen ? 'translateY(-100%)' : 'translateY(0)';
                nav.style.opacity = isOpen ? '0' : '1';
                nav.style.visibility = isOpen ? 'hidden' : 'visible';
            });

            header.appendChild(mobileMenuBtn);
            mobileMenuBtn.style.display = 'block';
        }
    }
}

// Marketplace Filters
function initMarketplaceFilters() {
    const productSearchInput = document.getElementById('productSearch');
    const productCategorySelect = document.getElementById('productCategory');
    const livestockSearchInput = document.getElementById('livestockSearch');
    const livestockTypeSelect = document.getElementById('livestockType');
    const healthStatusSelect = document.getElementById('healthStatus');

    // Initialize marketplace tabs
    initializeMarketplaceTabs();

    if (productSearchInput && productCategorySelect) {
        // Fetch and display products initially
        fetchProducts();

        // Debounced search for products
        let productSearchTimeout;
        productSearchInput.addEventListener('input', function() {
            clearTimeout(productSearchTimeout);
            productSearchTimeout = setTimeout(filterProducts, 300);
        });

        productCategorySelect.addEventListener('change', filterProducts);
    }

    if (livestockSearchInput && livestockTypeSelect && healthStatusSelect) {
        // Fetch and display livestock initially
        fetchLivestock();

        // Debounced search for livestock
        let livestockSearchTimeout;
        livestockSearchInput.addEventListener('input', function() {
            clearTimeout(livestockSearchTimeout);
            livestockSearchTimeout = setTimeout(filterLivestock, 300);
        });

        livestockTypeSelect.addEventListener('change', filterLivestock);
        healthStatusSelect.addEventListener('change', filterLivestock);
    }
}

let allProducts = []; // Store all products globally for filtering

async function fetchProducts() {
    try {
        const response = await fetch('php/fetch_products.php');
        const products = await response.json();
        allProducts = products; // Store for filtering
        displayProducts(products);
    } catch (error) {
        console.error('Error fetching products:', error);
        // Fallback to sample data
        const sampleProducts = [
            { product_id: 1, product_name: 'Premium Hog Feed 50kg', category: 'Feeds', price: 1200, image: 'hog_feed.jpg' },
            { product_id: 2, product_name: 'Vitamin Boost', category: 'Medicines', price: 450, image: 'vitamin_boost.jpg' },
        ];
        allProducts = sampleProducts;
        displayProducts(sampleProducts);
    }
}

function displayProducts(products) {
    const grid = document.getElementById('productGrid');
    if (!grid) return;

    grid.innerHTML = '';
    products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.dataset.category = product.category;
        productCard.innerHTML = `
            <img src="uploads/${product.image || 'default.jpg'}" alt="${product.product_name}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
            <h3>${product.product_name}</h3>
            <p>₱${product.price}</p>
            <div class="product-stats">
                <span class="reviews-count">⭐ ${product.reviews_count || 0} reviews</span>
                <span class="sold-count">Sold: ${product.sold_count || 0}</span>
            </div>
            <div class="product-actions">
                <button class="btn-secondary buy-now-btn" data-product-id="${product.product_id}" data-price="${product.price}">Buy Now</button>
                <button class="btn-primary add-to-cart-btn" data-product-id="${product.product_id}">Add to Barn</button>
            </div>
            <div class="comments-section" id="comments-${product.product_id}" style="display: none;">
                <h4>Reviews & Comments</h4>
                <div class="comments-list" id="comments-list-${product.product_id}"></div>
                <form class="comment-form" id="comment-form-${product.product_id}">
                    <input type="hidden" name="product_id" value="${product.product_id}">
                    <div class="rating-input">
                        <label>Rating:</label>
                        <select name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐</option>
                            <option value="4">⭐⭐⭐⭐</option>
                            <option value="3">⭐⭐⭐</option>
                            <option value="2">⭐⭐</option>
                            <option value="1">⭐</option>
                        </select>
                    </div>
                    <textarea name="comment" placeholder="Write your review..." required></textarea>
                    <button type="submit" class="btn-primary">Submit Review</button>
                </form>
            </div>
        `;
        grid.appendChild(productCard);
    });

    // Add event listeners to the buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            addToCart(productId);
        });
    });

    document.querySelectorAll('.buy-now-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const price = this.dataset.price;
            handleBuyNow(productId, price);
        });
    });

    // Add event listeners for comment forms
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = this.querySelector('input[name="product_id"]').value;
            const rating = this.querySelector('select[name="rating"]').value;
            const comment = this.querySelector('textarea[name="comment"]').value;
            submitReview(productId, rating, comment);
        });
    });

    // Load reviews for each product
    products.forEach(product => {
        fetchReviews(product.product_id);
    });
}

function filterProducts() {
    const searchTerm = document.getElementById('productSearch').value.toLowerCase();
    const category = document.getElementById('productCategory').value;

    const filtered = allProducts.filter(product => {
        const matchesSearch = product.product_name.toLowerCase().includes(searchTerm);
        const matchesCategory = category === '' || product.category === category;
        return matchesSearch && matchesCategory;
    });

    displayProducts(filtered);
}

// Initialize marketplace tabs
function initializeMarketplaceTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            const tabId = this.dataset.tab;
            const activeContent = document.getElementById(tabId);
            if (activeContent) {
                activeContent.classList.add('active');
            }
        });
    });
}

let allLivestock = []; // Store all livestock globally for filtering

function filterLivestock() {
    const searchTerm = document.getElementById('livestockSearch').value.toLowerCase();
    const type = document.getElementById('livestockType').value;
    const healthStatus = document.getElementById('healthStatus').value;

    const filtered = allLivestock.filter(animal => {
        const matchesSearch = animal.name.toLowerCase().includes(searchTerm) ||
                              animal.breed.toLowerCase().includes(searchTerm) ||
                              animal.owner_name.toLowerCase().includes(searchTerm);
        const matchesType = type === '' || animal.type === type;
        const matchesHealth = healthStatus === '' || animal.health_status === healthStatus;
        return matchesSearch && matchesType && matchesHealth;
    });

    displayLivestock(filtered);
}

// Animations
function initAnimations() {
    // Add fade-in animation to elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observe elements that should animate in
    const animateElements = document.querySelectorAll('.feature-card, .tip-card, .product-card');
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    .fade-in {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

    .mobile-menu-btn {
        display: none;
    }

    @media (max-width: 768px) {
        .mobile-menu-btn {
            display: block;
        }
    }
`;
document.head.appendChild(style);

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;

    if (type === 'success') {
        notification.style.backgroundColor = '#27ae60';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#e74c3c';
    } else {
        notification.style.backgroundColor = '#3498db';
    }

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

const notificationStyle = document.createElement('style');
notificationStyle.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(notificationStyle);

// Order Modal Functions
function openOrderModal(productId, productName, price) {
    const modal = document.getElementById('orderModal');
    const productIdInput = document.getElementById('orderProductId');
    const quantityInput = document.getElementById('orderQuantity');
    const totalDisplay = document.getElementById('orderTotal');

    if (modal && productIdInput && quantityInput && totalDisplay) {
        productIdInput.value = productId;
        quantityInput.value = 1;
        updateOrderTotal(price);

        modal.style.display = 'block';

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };

        // Close modal with close button
        const closeBtn = modal.querySelector('.close');
        if (closeBtn) {
            closeBtn.onclick = function() {
                modal.style.display = 'none';
            };
        }

        // Update total on quantity change
        quantityInput.addEventListener('input', function() {
            updateOrderTotal(price);
        });
    }
}

function updateOrderTotal(price) {
    const quantity = document.getElementById('orderQuantity').value;
    const total = price * quantity;
    document.getElementById('orderTotal').textContent = `Total: ₱${total.toFixed(2)}`;
}

// Cart functionality
let cartCount = 0;

function initCart() {
    const cartToggle = document.getElementById('cartToggle');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartClose = document.querySelector('.cart-close');
    const checkoutBtn = document.getElementById('checkoutBtn');

    if (cartToggle && cartSidebar) {
        cartToggle.addEventListener('click', toggleCart);
        cartClose.addEventListener('click', closeCart);
        checkoutBtn.addEventListener('click', handleCheckout);

        // Close cart when clicking outside
        document.addEventListener('click', function(e) {
            if (!cartSidebar.contains(e.target) && !cartToggle.contains(e.target)) {
                closeCart();
            }
        });

        // Load cart on page load
        loadCart();
    }
}

function toggleCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    cartSidebar.classList.toggle('open');
}

function closeCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    cartSidebar.classList.remove('open');
}

async function loadCart() {
    try {
        const response = await fetch('php/fetch_cart.php');
        const result = await response.json();

        if (result.success) {
            displayCart(result.cart);
            updateCartCount(result.cart.length);
        }
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

function displayCart(cartItems) {
    const cartItemsContainer = document.getElementById('cartItems');
    if (!cartItemsContainer) return;

    if (cartItems.length === 0) {
        cartItemsContainer.innerHTML = '<p>Your barn is empty. Add some products!</p>';
        return;
    }

    cartItemsContainer.innerHTML = '';
    let total = 0;

    cartItems.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <img src="uploads/${item.image || 'default.jpg'}" alt="${item.product_name}">
            <div class="cart-item-details">
                <h4>${item.product_name}</h4>
                <p>₱${item.price} x ${item.quantity} = ₱${itemTotal.toFixed(2)}</p>
                <div class="cart-item-controls">
                    <button onclick="updateCartQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="updateCartQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
                    <button onclick="removeFromCart(${item.cart_id})" class="remove-btn">Remove</button>
                </div>
            </div>
        `;
        cartItemsContainer.appendChild(cartItem);
    });

    // Add total
    const totalDiv = document.createElement('div');
    totalDiv.className = 'cart-total';
    totalDiv.innerHTML = `<strong>Total: ₱${total.toFixed(2)}</strong>`;
    cartItemsContainer.appendChild(totalDiv);
}

function updateCartCount(count) {
    const cartCountElement = document.getElementById('cartCount');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
    cartCount = count;
}

async function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    try {
        const response = await fetch('php/add_to_cart.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Added to barn!', 'success');
            loadCart(); // Refresh cart
        } else {
            showNotification(result.message || 'Failed to add to cart', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('An error occurred', 'error');
    }
}

async function updateCartQuantity(cartId, newQuantity) {
    if (newQuantity < 1) return;

    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', newQuantity);

    try {
        const response = await fetch('php/update_cart_quantity.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            loadCart(); // Refresh cart
        } else {
            showNotification(result.message || 'Failed to update quantity', 'error');
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        showNotification('An error occurred', 'error');
    }
}

async function removeFromCart(cartId) {
    const formData = new FormData();
    formData.append('cart_id', cartId);

    try {
        const response = await fetch('php/remove_from_cart.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Removed from barn', 'success');
            loadCart(); // Refresh cart
        } else {
            showNotification(result.message || 'Failed to remove item', 'error');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        showNotification('An error occurred', 'error');
    }
}

function handleCheckout() {
    if (cartCount === 0) {
        showNotification('Your barn is empty!', 'error');
        return;
    }

    // Redirect to checkout or show checkout modal
    showNotification('Checkout functionality coming soon!', 'info');
}

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initCart();
    fetchLivestock();
});

// Handle order form submission
document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('php/place_order.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Order placed successfully!', 'success');
                    document.getElementById('orderModal').style.display = 'none';
                    // Optionally refresh products or update stock display
                } else {
                    showNotification(result.message || 'Failed to place order', 'error');
                }
            } catch (error) {
                console.error('Error placing order:', error);
                showNotification('An error occurred while placing the order', 'error');
            }
        });
    }
});

// Fetch and display livestock
async function fetchLivestock() {
    try {
        const response = await fetch('php/fetch_livestock.php');
        const livestock = await response.json();
        allLivestock = livestock; // Store for filtering
        displayLivestock(livestock);
    } catch (error) {
        console.error('Error fetching livestock:', error);
    }
}

function displayLivestock(livestock) {
    const grid = document.getElementById('livestockGrid');
    if (!grid) return;

    grid.innerHTML = '';
    livestock.forEach(animal => {
        const livestockCard = document.createElement('div');
        livestockCard.className = 'product-card livestock-card';
        livestockCard.innerHTML = `
            <img src="uploads/${animal.image || 'default.jpg'}" alt="${animal.name}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
            <h3>${animal.name}</h3>
            <p><strong>Type:</strong> ${animal.type}</p>
            <p><strong>Breed:</strong> ${animal.breed}</p>
            <p><strong>Age:</strong> ${animal.age} years</p>
            <p><strong>Health:</strong> ${animal.health_status}</p>
            <p><strong>Owner:</strong> ${animal.owner_name}</p>
            <p>${animal.description}</p>
            <button class="btn-primary">Contact Seller</button>
        `;
        grid.appendChild(livestockCard);
    });
}

// Buy Now functionality
async function handleBuyNow(productId, price) {
    const quantity = prompt('Enter quantity:', '1');
    if (!quantity || isNaN(quantity) || quantity <= 0) {
        showNotification('Invalid quantity', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    try {
        const response = await fetch('php/buy_now.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showNotification(`Order placed! Total: ₱${result.total_price}`, 'success');
            // Optionally redirect to order confirmation page
        } else {
            showNotification(result.message || 'Failed to place order', 'error');
        }
    } catch (error) {
        console.error('Error placing order:', error);
        showNotification('An error occurred', 'error');
    }
}

// Fetch reviews for a product
async function fetchReviews(productId) {
    try {
        const response = await fetch(`php/fetch_reviews.php?product_id=${productId}`);
        const reviews = await response.json();
        displayReviews(productId, reviews);
    } catch (error) {
        console.error('Error fetching reviews:', error);
    }
}

function displayReviews(productId, reviews) {
    const commentsList = document.getElementById(`comments-list-${productId}`);
    if (!commentsList) return;

    commentsList.innerHTML = '';
    if (reviews.length === 0) {
        commentsList.innerHTML = '<p>No reviews yet. Be the first to review!</p>';
        return;
    }

    reviews.forEach(review => {
        const reviewDiv = document.createElement('div');
        reviewDiv.className = 'review-item';
        reviewDiv.innerHTML = `
            <div class="review-header">
                <strong>${review.full_name}</strong>
                <span class="rating">${'⭐'.repeat(review.rating)}</span>
            </div>
            <p>${review.comment}</p>
            <small>${new Date(review.created_at).toLocaleDateString()}</small>
        `;
        commentsList.appendChild(reviewDiv);
    });
}

// Submit a review
async function submitReview(productId, rating, comment) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('rating', rating);
    formData.append('comment', comment);

    try {
        const response = await fetch('php/add_review.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Review submitted successfully!', 'success');
            fetchReviews(productId); // Refresh reviews
            // Clear form
            const form = document.getElementById(`comment-form-${productId}`);
            if (form) form.reset();
        } else {
            showNotification(result.message || 'Failed to submit review', 'error');
        }
    } catch (error) {
        console.error('Error submitting review:', error);
        showNotification('An error occurred', 'error');
    }
}
