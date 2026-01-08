
// Dictionary for the About.html content
const ebroTranslations = {
    "en": { 
        /*common for all pages*/  
        /*Overlay Menu*/
         "common1": "Signup",
         "common2": "Home",
         "common3": "About-us",
         "common4": "Contact-us",
         "common5": "FAQs",
         "common6": "Terms of Service",
         "common7": "Return Policy",
         "common8": "Privacy Policy",
         "common9": "Products",
         "common10": "Basic Food",
         "common11": "Cooking Ingridient",
         "common12": "Baby Products",
         "common13": "HealthCare",
         "common14": "Packaged Goods",
         "common15": "Powder Soap",
         "common16": "Dayper&Wipes",
         "common17": "Packed Foods",
         "common18": "Spices Powder",
         "common19": "Food Oils",
         "common20": "Modes&Softs",
         "common21": "Liquid Soap",
         "common22": "Additional",
         "common23": "Our Hosts",
         "common24": "Developer",


       
    },
    "am": {
        "common1": "ይመዝገቡ",
        "common2": "ዋና ገፅ",  
        "common3": "ስለ እኛ",
        "common4": "ያግኙን",
        "common5": "ተደጋጋሚ ጥያቄዎች",
        "common6": "የአገልግሎት ውል",
        "common7": "የመመለሻ ህጎች",
        "common8": "የግላዊነት መመርያ",
        "common9": "ምርቶቻችን",
        "common10": "የመሬት እቃዎች", 
        "common11": "ምግብ መስረያ ግባቶች",
        "common12": "የህፃናት ወተት",
        "common13": "ንፅህና መጠበቂዎች",
        "common14": "የታሸጉ እቃዎች",
        "common15": "የዱቄት ሳሙና",
        "common16": "ዳይፐር እና ዋይፕስ",
        "common17": "የታሸጉ ምግቦች",
        "common18": "ቅመማቅመም ዱቄቶች",
        "common19": "የምግብ ዘይት",
        "common20": "ሞዲየስ እና ሶፍት",
        "common21": "ፈሳሽ ሳሙና",
        "common22": "በተጨማሪ",
        "common23": "አስተናጋጆቻችን",
        "common24": "ወብሳይቱ ገንቢ",


       
       
    }
};

// Toggle the menu visibility
function toggleLangMenu() {
    document.getElementById('langOptions').classList.toggle('show');
}

// Close menu if user clicks outside
window.onclick = function(event) {
    if (!event.target.closest('.custom-lang-dropdown')) {
        document.getElementById('langOptions').classList.remove('show');
    }
}

function changeLanguage(lang) {
    localStorage.setItem("userLanguage", lang);
    document.documentElement.lang = lang;
    
    // Update the trigger label
    document.getElementById('current-lang-label').innerText = (lang === 'am') ? 'አማርኛ' : 'English';

    // Update all elements with data-key
    const elements = document.querySelectorAll("[data-key]");
    elements.forEach(el => {
        const key = el.getAttribute("data-key");
        if (ebroTranslations[lang] && ebroTranslations[lang][key]) {
            el.innerText = ebroTranslations[lang][key];
        }
    });
    
    // Hide menu after selection
    document.getElementById('langOptions').classList.remove('show');
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', () => {
    const savedLang = localStorage.getItem("userLanguage");
    if (savedLang) {
        changeLanguage(savedLang);
    }
});