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
            <h2 data-key="Search1">Search</h2>
            <a href="javascript:history.back()" style="text-decoration:none; color:#333; font-size:24px;">✕</a>
        </div>
        <div class="input-area">
            <div class="search-box-wrapper">
                <input id="searchInput" type="text" placeholder="Search items...እዚህ ይጻፉ" autocomplete="off">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="results-container" id="searchResults">
            <div class="status-msg" data-key="Search2">Start typing to find products...</div>
        </div>
    </div>
    <div style="flex:1" onclick="history.back()"></div>
</div>


    <div class="language-bar" style="position: fixed; top: 600px; left: 70px; display: flex; background: white; padding: 5px; border-radius: 50px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 10000; border: 2px solid #136835;">
    
    <button onclick="updatePageLanguage('en')" id="btn-en" 
        style="border: none; padding: 8px 18px; border-radius: 50px; cursor: pointer; font-weight: 800; font-size: 13px; transition: 0.3s; background: #136835; color: white;">
        English
    </button>
    
    <div style="width: 1px; height: 20px; background: #eee; align-self: center; margin: 0 2px;"></div>
    
    <button onclick="updatePageLanguage('am')" id="btn-am" 
        style="border: none; padding: 8px 18px; border-radius: 50px; cursor: pointer; font-weight: 800; font-size: 13px; transition: 0.3s; background: transparent; color: #136835;">
        አማርኛ
    </button>
    
</div>


<script>
(function(){
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");
    
    // MATCHING YOUR STORAGE KEY FROM search.html
    const CART_KEY = "cartItems";

    // Focus search box automatically
    setTimeout(() => searchInput.focus(), 400);

    searchInput.addEventListener("input", () => {
        const query = searchInput.value.trim();

        if (query.length < 1) {
    const startTypingMsg = (typeof translations !== 'undefined' && translations.search3) 
                           ? translations.search3 
                           : "Start typing to find products";
                           
    searchResults.innerHTML = `<div class="status-msg">${startTypingMsg}</div>`;
    return;
}

        // Fetch from your PHP worker
        fetch(`fetch_search_results.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(products => {
               if (products.length === 0) {
    const noResultsMsg = (typeof translations !== 'undefined' && translations.search4) 
                         ? translations.search4 
                         : "No products found";
                         
    searchResults.innerHTML = `<div class="status-msg">${noResultsMsg}</div>`;
    return;
}

                searchResults.innerHTML = products.map(p => {
                    const isSoldOut = (p.status && (p.status.toLowerCase() === 'sold_out' || p.status.toLowerCase() === 'out of stock'));

                    return `
                        <div class="product-card" style="position: relative; ${isSoldOut ? 'opacity: 0.6;' : ''}">
                            ${isSoldOut ? '<div style=" position: absolute; top: 20px; left: -32px; transform: rotate(-45deg);  background: #e74c3c;   color: white;   padding: 5px 0;  width: 120px;   text-align: center;  font-size: 11px;  font-weight: bold; z-index: 2;  box-shadow: 0 2px 4px rgba(0,0,0,0.2);">  SOLD OUT</div>' : ''}
                            
                            <img src="${p.image_url}" alt="${p.name}">
                            <div class="product-name">${p.name}</div>
                            <div class="product-price">${parseFloat(p.price).toFixed(2)} birr</div>
                            
                            <button class="quick-add-btn" 
                                    style="background: ${isSoldOut ? '#888' : '#008cff'}; cursor: ${isSoldOut ? 'not-allowed' : 'pointer'};"
                                    onclick='handleQuickAdd(${JSON.stringify(p)})'>
                                ${isSoldOut ? 'Sold Out' : 'Quick Add'}
                            </button>
                        </div>
                    `;
                }).join('');
            })
            .catch(err => {
                // If you see this, your db.php host is still wrong
                searchResults.innerHTML = '<div class="status-msg">Database Connection Error. Check db.php</div>';
            });
    });

    // GLOBAL FUNCTION TO HANDLE ADDING
window.handleQuickAdd = function(product) {
    // 1. Check if sold out
    const isSoldOut = (product.status && (product.status.toLowerCase() === 'sold_out' || product.status.toLowerCase() === 'out of stock'));
    if (isSoldOut) {
    const soldOutAlert = (typeof translations !== 'undefined' && translations.search5) 
                         ? translations.search5 
                         : "Sorry! This item is sold out. We'll add it soon.";
                         
    alert(soldOutAlert);
    return; 
}


    // 2. Prepare the data for cart_handler.php
    const formData = new FormData();
    formData.append('product_id', product.id);
    formData.append('quantity', 1);
    formData.append('action', 'add'); // Tells cart_handler to add the item

    // 3. Send to your PHP backend
    fetch('cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // 4. Redirect immediately after the database is updated
        window.location.href = "Cart.php"; 
    })
    .catch(error => {
        console.error('Error adding to database cart:', error);
        // Fallback: Redirect anyway to see if it worked
        window.location.href = "Cart.php";
    });
};
})();



function updatePageLanguage(lang) {
        if (typeof ebroTranslations !== 'undefined' && ebroTranslations[lang]) {
            const selectedLanguage = ebroTranslations[lang];
            
            document.querySelectorAll('[data-key]').forEach(el => {
                const key = el.getAttribute('data-key');
                if (selectedLanguage[key]) el.innerText = selectedLanguage[key];
            });


            const btnEn = document.getElementById('btn-en');
            const btnAm = document.getElementById('btn-am');

            if (lang === 'en') {
                btnEn.style.background = "#136835";
                btnEn.style.color = "white";
                
                btnAm.style.background = "transparent";
                btnAm.style.color = "#136835";
            } else {
                btnAm.style.background = "#136835";
                btnAm.style.color = "white";
                
                btnEn.style.background = "transparent";
                btnEn.style.color = "#136835";
            }

            localStorage.setItem('userLanguage', lang);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedLang = localStorage.getItem('userLanguage') || 'en';
        updatePageLanguage(savedLang);
    });



// Add this translation dictionary
const ebroTranslations = {
    'en': {
        "Search1": "Search",
        "Search2": "Start typing to find products...",
        "search3": "Start typing to find products",
        "search4": "No products found",
        "search5": "Sorry! This item is sold out. We'll add it soon.\n\nይቅርታ! ይህ እቃ አልቋል። በቅርቡ እናመጣዋለን።"
    },
    'am': {
        "Search1": "ፈልግ",
        "Search2": "የሚፈልጉትን እቃ እዚህ ጋር መጻፍ ይጀምሩ......",
        "search3": "የሚፈልጉትን እቃ እዚህ ጋር መጻፍ ይጀምሩ...",
        "search4": "ምንም ተዛማጅ እቃ አልተገኘም",
        "search5": "ይቅርታ! ይህ እቃ አልቋል። አሁን ላይ ወደ ቦርሳዎስንንስ መጨመር አይችሉም። በቅርቡ እናስመጣለን።"
    }
};

// Update your variable reference
const translations = ebroTranslations[localStorage.getItem('userLanguage') || 'en'];
</script>
</body>
</html>