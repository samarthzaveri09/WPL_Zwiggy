let restaurants = [
    {
        name: "Tandoor Treats",
        location: "Mumbai",
        cuisine: "Indian",
        rating: 4.5,
        dishes: [
            { name: "Paneer Butter Masala", price: 250 },
            { name: "Tandoori Roti", price: 30 }
        ]
    },
    {
        name: "Sushi World",
        location: "Delhi",
        cuisine: "Japanese",
        rating: 4.8,
        dishes: [
            { name: "Sashimi", price: 450 },
            { name: "Dragon Roll", price: 350 }
        ]
    }
];

// Cart array to store selected items
let cart = [];

function renderRestaurants() {
    const existingDiv = document.getElementById('existing-restaurants');

    if (restaurants.length === 0) {
        existingDiv.innerHTML = "<p>No existing restaurants yet.</p>";
        return;
    }

    existingDiv.innerHTML = restaurants.map((restaurant, index) => `
        <div class="restaurant-card">
            <h3>${restaurant.name} - ${restaurant.location}</h3>
            <p>Cuisine: ${restaurant.cuisine}</p>
            <p>Rating: ${restaurant.rating}/5</p>
            <div class="menu-items">
                <h4>Menu:</h4>
                <ul>
                    ${restaurant.dishes.map(dish => `
                        <li>
                            ${dish.name} - ₹${dish.price}
                            <button onclick="addToCart('${restaurant.name}', '${dish.name}', ${dish.price})">
                                Add to Cart
                            </button>
                        </li>
                    `).join('')}
                </ul>
            </div>
            <hr>
        </div>
    `).join('');
}

function addRestaurant(name, location, cuisine, rating, dishes) {
    if (!name || !location || !cuisine || !rating || !dishes || dishes.length < 2) {
        alert("Error: All fields including at least two dishes are required.");
        return;
    }

    // Convert dish strings to objects with prices
    const dishObjects = dishes.map(dishStr => {
        const parts = dishStr.split(':');
        return {
            name: parts[0].trim(),
            price: parts.length > 1 ? parseFloat(parts[1].trim()) : 0
        };
    });

    restaurants.push({ 
        name, 
        location, 
        cuisine, 
        rating, 
        dishes: dishObjects 
    });
    renderRestaurants();
}

function updateRestaurantMenu(name, newDishes) {
    const restaurant = restaurants.find(r => r.name.toLowerCase() === name.toLowerCase());

    if (!restaurant) {
        alert("Error: Restaurant not found.");
        return;
    }

    if (newDishes.length < 1) {
        alert("Error: Please enter at least one new dish.");
        return;
    }

    // Convert new dish strings to objects with prices
    const newDishObjects = newDishes.map(dishStr => {
        const parts = dishStr.split(':');
        return {
            name: parts[0].trim(),
            price: parts.length > 1 ? parseFloat(parts[1].trim()) : 0
        };
    });

    // Merge dishes, avoiding duplicates by name
    const existingDishNames = restaurant.dishes.map(d => d.name.toLowerCase());
    
    for (const newDish of newDishObjects) {
        if (!existingDishNames.includes(newDish.name.toLowerCase())) {
            restaurant.dishes.push(newDish);
            existingDishNames.push(newDish.name.toLowerCase());
        }
    }

    alert(`Menu for "${restaurant.name}" updated successfully!`);
    renderRestaurants();
}

function deleteRestaurantOrDish(name, dish = '') {
    const restaurantIndex = restaurants.findIndex(r => r.name.toLowerCase() === name.toLowerCase());

    if (restaurantIndex === -1) {
        alert("Error: Restaurant not found.");
        return;
    }

    const restaurant = restaurants[restaurantIndex];

    if (dish) {
        // Delete specific dish
        const dishIndex = restaurant.dishes.findIndex(d => d.name.toLowerCase() === dish.toLowerCase());

        if (dishIndex === -1) {
            alert(`Error: Dish "${dish}" not found in "${restaurant.name}".`);
            return;
        }

        restaurant.dishes.splice(dishIndex, 1);

        // If no dishes remain, delete the restaurant
        if (restaurant.dishes.length === 0) {
            restaurants.splice(restaurantIndex, 1);
            alert(`Restaurant "${restaurant.name}" deleted as no dishes remain.`);
        } else {
            alert(`Dish "${dish}" removed successfully from "${restaurant.name}".`);
        }
    } else {
        // Delete the entire restaurant
        restaurants.splice(restaurantIndex, 1);
        alert(`Restaurant "${name}" deleted successfully.`);
    }

    renderRestaurants();
}

// Cart Functions
function addToCart(restaurantName, dishName, price) {
    cart.push({
        restaurantName,
        dishName,
        price,
        quantity: 1
    });
    
    updateCartDisplay();
    alert(`Added ${dishName} from ${restaurantName} to cart!`);
}

function updateCartDisplay() {
    const cartDiv = document.getElementById('cart-items');
    if (!cartDiv) return; // If not on a page with cart display
    
    if (cart.length === 0) {
        cartDiv.innerHTML = "<p>Your cart is empty</p>";
        document.getElementById('cart-total').textContent = "₹0";
        document.getElementById('checkout-btn').disabled = true;
        return;
    }
    
    let total = 0;
    cartDiv.innerHTML = cart.map((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        return `
            <div class="cart-item">
                <span>${item.dishName} (${item.restaurantName})</span>
                <div>
                    <button onclick="decreaseQuantity(${index})">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="increaseQuantity(${index})">+</button>
                </div>
                <span>₹${itemTotal}</span>
                <button onclick="removeFromCart(${index})">Remove</button>
            </div>
        `;
    }).join('');
    
    document.getElementById('cart-total').textContent = `₹${total}`;
    document.getElementById('checkout-btn').disabled = false;
    
    // Update local storage
    localStorage.setItem('restaurantCart', JSON.stringify(cart));
}

function increaseQuantity(index) {
    cart[index].quantity += 1;
    updateCartDisplay();
}

function decreaseQuantity(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity -= 1;
        updateCartDisplay();
    }
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

function proceedToCheckout() {
    // Save cart to localStorage for the order page
    localStorage.setItem('restaurantCart', JSON.stringify(cart));
    window.location.href = 'order.html';
}

function calculateOrderTotal() {
    // Get cart from localStorage
    const cartItems = JSON.parse(localStorage.getItem('restaurantCart')) || [];
    
    if (cartItems.length === 0) {
        alert("Your cart is empty!");
        window.location.href = 'index.html';
        return;
    }
    
    // Calculate totals
    const subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const gst = subtotal * 0.18; // 18% GST
    const total = subtotal + gst;
    
    // Display the order summary
    const orderSummary = document.getElementById('order-summary');
    orderSummary.innerHTML = `
        <h3>Order Items:</h3>
        <ul>
            ${cartItems.map(item => `
                <li>${item.dishName} (${item.restaurantName}) x ${item.quantity} - ₹${item.price * item.quantity}</li>
            `).join('')}
        </ul>
        <div class="order-totals">
            <p><strong>Subtotal:</strong> ₹${subtotal.toFixed(2)}</p>
            <p><strong>GST (18%):</strong> ₹${gst.toFixed(2)}</p>
            <hr>
            <p class="total"><strong>Total Amount:</strong> ₹${total.toFixed(2)}</p>
        </div>
    `;
    
    // Save order total for confirmation
    document.getElementById('final-amount').value = total.toFixed(2);
}

function placeOrder() {
    const form = document.getElementById('checkout-form');
    if (form.checkValidity()) {
        const name = document.getElementById('customer-name').value;
        const amount = document.getElementById('final-amount').value;
        
        // Clear cart after successful order
        localStorage.removeItem('restaurantCart');
        
        // Redirect to confirmation page or show confirmation message
        alert(`Thank you, ${name}! Your order of ₹${amount} has been placed successfully.`);
        window.location.href = 'index.html';
    } else {
        alert("Please fill in all required fields.");
    }
}

// Add Restaurant
if (document.getElementById('addForm')) {
    document.getElementById('addForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('name').value.trim();
        const location = document.getElementById('location').value.trim();
        const cuisine = document.getElementById('cuisine').value.trim();
        const rating = parseFloat(document.getElementById('rating').value.trim());
        const dishes = document.getElementById('dishes').value.split(",").map(d => d.trim());

        addRestaurant(name, location, cuisine, rating, dishes);
        e.target.reset();
    });
}

// Update Restaurant
if (document.getElementById('updateForm')) {
    document.getElementById('updateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('updateName').value.trim();
        const newDishes = document.getElementById('updateDishes').value.split(",").map(d => d.trim());

        updateRestaurantMenu(name, newDishes);
        e.target.reset();
    });
}

// Delete Restaurant or Dish
if (document.getElementById('deleteForm')) {
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('deleteName').value.trim();
        const dish = document.getElementById('deleteDish').value.trim();

        deleteRestaurantOrDish(name, dish);
        e.target.reset();
    });
}

// Load cart from localStorage if exists
document.addEventListener('DOMContentLoaded', function() {
    const savedCart = localStorage.getItem('restaurantCart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        if (document.getElementById('cart-items')) {
            updateCartDisplay();
        }
    }
    
    // Initial render of restaurants
    if (document.getElementById('existing-restaurants')) {
        renderRestaurants();
    }
    
    // Calculate order on order page
    if (document.getElementById('order-summary')) {
        calculateOrderTotal();
    }
    
    // Checkout form handling
    if (document.getElementById('checkout-form')) {
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            placeOrder();
        });
    }
});