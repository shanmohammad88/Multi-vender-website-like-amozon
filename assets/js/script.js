// assets/js/script.js - MASTER JAVASCRIPT FILE

// 1. CONFIRMATION POPUP (Safety Feature)
// Finds all links with class "btn-danger" or links containing "delete"
document.addEventListener('DOMContentLoaded', function() {
    
    let deleteLinks = document.querySelectorAll('a[href*="delete"], .btn-danger');

    deleteLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            let confirmAction = confirm("Are you sure you want to delete this? This cannot be undone.");
            if (!confirmAction) {
                event.preventDefault(); // Stop the delete if they click Cancel
            }
        });
    });

    // 4. CHECK FOR PHP MESSAGES (To trigger Toasts)
    // This looks at the URL for ?msg=Something or ?error=Something
    const urlParams = new URLSearchParams(window.location.search);
    const myParam = urlParams.get('msg');
    const errParam = urlParams.get('error');

    if(myParam) {
        showToast(myParam, 'success');
        // Clean the URL so the message doesn't appear again on refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if(errParam) {
        showToast(errParam, 'error');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// 2. IMAGE PREVIEW (For Vendor Upload)
// Shows the image immediately when selected
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            // Check if a preview box exists, if not create one
            let previewBox = document.getElementById('img-preview');
            if(!previewBox) {
                previewBox = document.createElement('img');
                previewBox.id = 'img-preview';
                previewBox.style.width = '100px';
                previewBox.style.marginTop = '10px';
                previewBox.style.borderRadius = '5px';
                previewBox.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';
                input.parentNode.appendChild(previewBox);
            }
            previewBox.src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Attach listener to file inputs
document.addEventListener('change', function(e) {
    if (e.target.type === 'file') {
        previewImage(e.target);
    }
});

// 3. TOAST NOTIFICATION FUNCTION (The sliding popup)
function showToast(msg, type = 'success') {
    let toastBox = document.getElementById('toast-box');
    
    // Create the container if it doesn't exist
    if (!toastBox) {
        toastBox = document.createElement('div');
        toastBox.id = 'toast-box';
        document.body.appendChild(toastBox);
    }

    // Create the toast element
    let toast = document.createElement('div');
    toast.classList.add('toast');
    if (type === 'error') {
        toast.classList.add('error');
    }

    // Choose icon based on type
    let icon = type === 'error' ? '<i class="fas fa-times-circle"></i>' : '<i class="fas fa-check-circle"></i>';
    
    toast.innerHTML = icon + msg;
    toastBox.appendChild(toast);

    // Remove it automatically after 3.5 seconds
    setTimeout(() => {
        toast.remove();
    }, 3500);
}

/* HERO SLIDER LOGIC */
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');

function showSlide(index) {
    // Loop back to start if at the end
    if (index >= slides.length) { currentSlide = 0; }
    else if (index < 0) { currentSlide = slides.length - 1; }
    else { currentSlide = index; }

    // Remove 'active' class from all slides
    slides.forEach(slide => slide.classList.remove('active'));

    // Add 'active' class to current slide
    slides[currentSlide].classList.add('active');
}

function changeSlide(direction) {
    showSlide(currentSlide + direction);
}

// Auto-play slider every 5 seconds
setInterval(() => {
    changeSlide(1);
}, 5000);

/* --- SCROLL ANIMATION ENGINE --- */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Target elements to animate
    // We want cards, headings, and sections to fade in
    const elementsToAnimate = document.querySelectorAll('.card, .hero, .section-header, .category-box, .product-wrapper');

    // 2. Add the 'reveal' class to them initially (hides them)
    elementsToAnimate.forEach(el => {
        el.classList.add('reveal');
    });

    // 3. Create the Observer (Watcher)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // When element enters screen, add 'active' to show it
                entry.target.classList.add('active');
            }
        });
    }, {
        threshold: 0.1 // Trigger when 10% of the item is visible
    });

    // 4. Start Watching
    elementsToAnimate.forEach(el => observer.observe(el));
});