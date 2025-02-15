let cart = [];
let cartCount = 0;

function addToCart(product, price) {
    const itemIndex = cart.findIndex(item => item.product === product);
    if (itemIndex > -1) {
        cart[itemIndex].quantity += 1;
    } else {
        const item = { product, price, quantity: 1 };
        cart.push(item);
    }
    updateCart();
}

function removeFromCart(product) {
    cart = cart.filter(item => item.product !== product);
    updateCart();
}

function increaseQuantity(product) {
    const itemIndex = cart.findIndex(item => item.product === product);
    if (itemIndex > -1) {
        cart[itemIndex].quantity += 1;
        updateCart();
    }
}

function decreaseQuantity(product) {
    const itemIndex = cart.findIndex(item => item.product === product);
    if (itemIndex > -1 && cart[itemIndex].quantity > 1) {
        cart[itemIndex].quantity -= 1;
        updateCart();
    } else if (itemIndex > -1 && cart[itemIndex].quantity === 1) {
        removeFromCart(product);
    }
}

function buyNow(product, price) {
    addToCart(product, price);
    checkout();
}

function updateCart() {
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const cartCountElement = document.getElementById('cart-count');
    
    cartItems.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        const li = document.createElement('li');
        li.innerHTML = `${item.product} - $${item.price.toFixed(2)} x ${item.quantity}
            <button onclick="increaseQuantity('${item.product}')">+</button>
            <button onclick="decreaseQuantity('${item.product}')">-</button>
            <button onclick="removeFromCart('${item.product}')">Remove</button>`;
        cartItems.appendChild(li);
        total += item.price * item.quantity;
    });

    cartTotal.textContent = `Total: $${total.toFixed(2)}`;
    cartCountElement.textContent = cart.length;
}

function checkout() {
    alert('Thank you for your purchase!');
    cart = [];
    updateCart();
}

function toggleCart() {
    const cartPopup = document.getElementById('cart-popup');
    if (cartPopup.style.display === 'block') {
        cartPopup.style.display = 'none';
    } else {
        cartPopup.style.display = 'block';
    }
}
