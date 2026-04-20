/* =====================================
 Wu Spa Cart Manager
 localStorage-based cart for Skin Script products
====================================== */

var WuSpaCart = (function () {
    var CART_KEY = 'wuspa_cart';

    function getCart() {
        try {
            return JSON.parse(localStorage.getItem(CART_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
    }

    function addToCart(id, name, price, image, qty) {
        var cart = getCart();
        qty = qty || 1;
        var existing = null;
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === id) { existing = cart[i]; break; }
        }
        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({ id: id, name: name, price: price, image: image, qty: qty });
        }
        saveCart(cart);
        updateCartCount();
        showCartToast(name);
        return cart;
    }

    function removeFromCart(id) {
        var cart = getCart().filter(function (item) { return item.id !== id; });
        saveCart(cart);
        updateCartCount();
        return cart;
    }

    function updateQty(id, qty) {
        var cart = getCart();
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === id) {
                cart[i].qty = qty;
                break;
            }
        }
        saveCart(cart);
        updateCartCount();
    }

    function clearCart() {
        saveCart([]);
        updateCartCount();
    }

    function getTotal() {
        return getCart().reduce(function (sum, item) {
            return sum + item.price * item.qty;
        }, 0);
    }

    function getTotalItems() {
        return getCart().reduce(function (sum, item) {
            return sum + item.qty;
        }, 0);
    }

    function updateCartCount() {
        var count = getTotalItems();
        // Update the header badge — waits for header to be injected
        var badge = document.querySelector('.wcmenucart-count');
        if (badge) {
            badge.textContent = count;
        }
        // Also update nav cart items dropdown if present
        refreshCartDropdown();
    }

    function refreshCartDropdown() {
        var cart = getCart();
        var container = document.querySelector('.nav-cart-items');
        var totalEl = document.querySelector('.nav-cart-title h5');
        if (!container) return;

        if (cart.length === 0) {
            container.innerHTML = '<p class="p-a15 m-a0" style="color:#fff;text-align:center;">Your cart is empty.<br><a href="product.html" style="color:#EC5598;">Start Shopping</a></p>';
            if (totalEl) totalEl.textContent = '$0.00';
            return;
        }

        var html = '';
        var total = 0;
        cart.forEach(function (item) {
            total += item.price * item.qty;
            html += '<div class="nav-cart-item clearfix">' +
                '<div class="nav-cart-item-image"><a href="product-detail.html?product=' + item.id + '">' +
                '<img src="' + item.image + '" alt="' + item.name + '" style="width:60px;height:60px;object-fit:cover;"></a></div>' +
                '<div class="nav-cart-item-desc">' +
                '<a href="product-detail.html?product=' + item.id + '">' + item.name + '</a>' +
                '<span class="nav-cart-item-price"><strong>' + item.qty + '</strong> x $' + item.price.toFixed(2) + '</span>' +
                '<a href="javascript:void(0);" class="nav-cart-item-quantity" onclick="WuSpaCart.removeFromCart(\'' + item.id + '\'); WuSpaCart.updateCartCount();">x</a>' +
                '</div></div>';
        });

        container.innerHTML = html;
        if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
    }

    function showCartToast(productName) {
        // Remove existing toast
        var existing = document.getElementById('wuspa-cart-toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.id = 'wuspa-cart-toast';
        toast.style.cssText = [
            'position:fixed', 'bottom:30px', 'right:30px', 'z-index:99999',
            'background:#EC5598', 'color:#fff', 'padding:14px 22px',
            'border-radius:4px', 'font-size:14px', 'font-family:Poppins,sans-serif',
            'box-shadow:0 4px 20px rgba(0,0,0,0.2)', 'transition:opacity 0.4s ease',
            'opacity:1', 'max-width:320px'
        ].join(';');
        toast.innerHTML = '<i class="fa fa-check-circle" style="margin-right:8px;"></i><strong>' +
            productName + '</strong> added to cart!';
        document.body.appendChild(toast);

        setTimeout(function () {
            toast.style.opacity = '0';
            setTimeout(function () { if (toast.parentNode) toast.remove(); }, 400);
        }, 2800);
    }

    // Poll for the badge element after DOM is ready (handles async header injection)
    document.addEventListener('DOMContentLoaded', function () {
        var attempts = 0;
        var pollBadge = setInterval(function () {
            var badge = document.querySelector('.wcmenucart-count');
            if (badge || attempts >= 20) {
                clearInterval(pollBadge);
                if (badge) updateCartCount();
            }
            attempts++;
        }, 200);
    });

    return {
        getCart: getCart,
        addToCart: addToCart,
        removeFromCart: removeFromCart,
        updateQty: updateQty,
        clearCart: clearCart,
        getTotal: getTotal,
        getTotalItems: getTotalItems,
        updateCartCount: updateCartCount,
        refreshCartDropdown: refreshCartDropdown
    };
})();
