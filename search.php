<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Your original design styles */
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; }
        #searchWrapper { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; }
        #searchPanel { width: 85%; max-width: 400px; height: 100%; background: #fff; display: flex; flex-direction: column; }
        .search-header { display: flex; justify-content: space-between; padding: 20px; align-items: center; }
        .input-area { padding: 0 20px 15px; }
        .search-box-wrapper { background: #f1f1f1; border-radius: 4px; display: flex; align-items: center; padding: 10px; }
        #searchInput { flex: 1; border: none; background: transparent; font-size: 16px; outline: none; }
        .results-container { flex: 1; overflow-y: auto; padding: 10px 20px; }
        .product-card { border: 1px solid #eee; border-radius: 8px; margin-bottom: 15px; padding: 10px; text-align: center; }
        .product-card img { width: 100%; height: 150px; object-fit: contain; }
        .product-name { font-weight: bold; margin: 10px 0 5px; }
        .product-price { color: #136835; font-weight: bold; margin-bottom: 10px; }
        .quick-add-btn { width: 100%; padding: 10px; background: #008cff; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .status-msg { text-align: center; color: #888; margin-top: 20px; }
    </style>
</head>
<body>

<div id="searchWrapper">
    <div id="searchPanel">
        <div class="search-header">
            <h2>Search</h2>
            <a href="javascript:history.back()" style="text-decoration:none; color:#333; font-size:24px;">✕</a>
        </div>
        <div class="input-area">
            <div class="search-box-wrapper">
                <input id="searchInput" type="text" placeholder="Search items..." autocomplete="off">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="results-container" id="searchResults">
            <div class="status-msg">Start typing to find products...</div>
        </div>
    </div>
    <div style="flex:1" onclick="history.back()"></div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    // Focus on search input when page opens
    setTimeout(() => searchInput.focus(), 400);

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();

        if (query.length < 1) {
            searchResults.innerHTML = '<div class="status-msg">Start typing to find product</div>';
            return;
        }

        fetch(`fetch_search_results.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(products => {
                if (products.length === 0) {
                    searchResults.innerHTML = '<div class="status-msg">No products found</div>';
                    return;
                }

                searchResults.innerHTML = products.map(p => {
                    // Check status for Sold Out system
                    const isSoldOut = (p.status.toLowerCase() === 'sold_out' || p.status.toLowerCase() === 'out of stock');

                    return ` 
                        <div class="product-card" style="position: relative; ${isSoldOut ? 'opacity: 0.6;' : ''}">
                            ${isSoldOut ? '<div style="position:absolute; top:10px; left:10px; background:red; color:white; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:bold; z-index:2;">SOLD OUT</div>' : ''}
                            
                            <img src="${p.image_url}" alt="${p.name}">
                            <div class="product-name">${p.name}</div>
                            <div class="product-price">${parseFloat(p.price).toFixed(2)} birr</div>
                            
                            <button class="quick-add-btn" 
                                    style="background: ${isSoldOut ? '#888' : '#008cff'}; cursor: ${isSoldOut ? 'not-allowed' : 'pointer'};"
                                    onclick='addToCartFromSearch(${JSON.stringify(p)})'>
                                ${isSoldOut ? 'Sold Out' : 'Quick Add'}
                            </button>
                        </div>
                    `;
                }).join('');
            })
            .catch(err => {
                console.error("Search Error:", err);
            });
    });

    // THIS FUNCTION MATCHES YOUR FILENAME "search.html" LOGIC EXACTLY
    function addToCartFromSearch(product) {
        // 1. Sold Out Check
        const status = product.status.toLowerCase();
        if (status === 'sold_out' || status === 'out of stock') {
            alert("This product is sold out!");
            return;
        }

        // 2. Use the EXACT storage key from your search.html
        const CART_KEY = "EBRO_CART"; 
        
        // 3. Load existing cart
        let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
        
        // 4. Check if item exists (using id from database)
        const existingItem = cart.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.qty += 1;
        } else {
            // 5. Add product using the database column names
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                image: product.image_url, // Maps image_url to image
                qty: 1
            });
        }
        
        // 6. Save back to EBRO_CART
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        
        // 7. Redirect to Cart
        window.location.href = "Cart.html";
    }
</script>
</body>
</html>