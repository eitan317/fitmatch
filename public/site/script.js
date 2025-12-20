// Admin email constant
const ADMIN_EMAIL = "eitansheli2019@gmail.com";

// Training types labels
const TRAINING_TYPE_LABELS = {
    // Gym / Strength
    "gym_basic": "חדר כושר בסיסי",
    "hypertrophy": "מסת שריר",
    "powerlifting": "פאוורליפטינג",
    "crossfit": "קרוספיט",
    "street_workout": "סטריט וורקאאוט / מתח מקבילים",
    
    // Fat loss / conditioning
    "weightloss": "חיטוב / ירידה במשקל",
    "hiit": "אימוני HIIT",
    "intervals": "אינטרוולים עצימים",
    
    // Mobility / posture / rehab
    "mobility": "מוביליטי וגמישות",
    "yoga": "יוגה",
    "pilates": "פילאטיס",
    "physio_rehab": "שיקום / פיזיותרפיה (אם קיים)",
    "back_pain": "אימונים לכאבי גב",
    "postnatal": "נשים אחרי לידה",
    
    // Home / minimal equipment
    "home_bodyweight": "אימוני בית (משקל גוף)",
    "trx": "אימוני TRX",
    "short20": "אימונים קצרים (20 דק׳)",
    
    // Endurance / cardio
    "running": "ריצה",
    "sprints": "ספרינטים",
    "marathon": "הכנה למרתון / חצי מרתון",
    "cycling": "רכיבה על אופניים",
    "swimming": "שחייה",
    
    // Combat sports
    "boxing": "אגרוף",
    "kickboxing": "קיקבוקס",
    "mma": "MMA",
    "kravmaga": "קרב מגע",
    
    // Special formats
    "couple": "אימונים זוגיים",
    "group": "אימונים קבוצתיים",
    "online": "אימונים אונליין (זום)",
    "outdoor": "אימונים בחוץ / בפארק",
    "bootcamp": "בוטקמפ",
    
    // Target population
    "women_only": "נשים בלבד",
    "men_only": "גברים בלבד",
    "teens": "נוער",
    "kids": "ילדים",
    "seniors": "גיל שלישי"
};

// Global variables for filtering and sorting
let currentTypeFilters = []; // we will still support the checkbox filters if they exist
let currentSearchQuery = "";
let currentMinPrice = null;
let currentMaxPrice = null;
let currentSingleTypeFilter = "";
let currentSortMode = "default";

// Render training type badges
function renderTrainingTypeBadges(trainer) {
    const types = Array.isArray(trainer.trainingTypes) ? trainer.trainingTypes : [];
    if (!types.length) return "";
    const items = types.map(t => {
        const label = TRAINING_TYPE_LABELS[t] || t;
        return `<span class="badge badge-type">${label}</span>`;
    });
    return `<div class="trainer-tags">${items.join("")}</div>`;
}

// Review System - LocalStorage Helpers
function getTrainerReviews() {
    const raw = localStorage.getItem("trainerReviews");
    if (!raw) return [];
    try {
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        console.error("Failed to parse trainerReviews", e);
        return [];
    }
}

function saveTrainerReviews(reviews) {
    localStorage.setItem("trainerReviews", JSON.stringify(reviews || []));
}

function getReviewsForTrainer(trainerId) {
    const all = getTrainerReviews();
    return all.filter(r => String(r.trainerId) === String(trainerId));
}

function addReviewForTrainer(trainerId, review) {
    const all = getTrainerReviews();
    all.push({
        id: Date.now().toString(),
        trainerId: String(trainerId),
        rating: Number(review.rating) || 0,
        text: (review.text || "").trim(),
        authorName: (review.authorName || "משתמש").trim(),
        createdAt: Date.now()
    });
    saveTrainerReviews(all);
}

function formatReviewDate(ts) {
    const d = new Date(ts);
    return d.toLocaleDateString("he-IL");
}

function renderReviews(trainerId) {
    const reviews = getReviewsForTrainer(trainerId);
    const listEl = document.getElementById("reviews-list");
    const summaryEl = document.getElementById("reviews-summary");

    if (!listEl || !summaryEl) return;

    // Summary
    if (reviews.length === 0) {
        summaryEl.innerHTML = "<p>אין עדיין ביקורות למאמן זה.</p>";
    } else {
        const avg = reviews.reduce((a, r) => a + r.rating, 0) / reviews.length;
        summaryEl.innerHTML = `
            <p>
                ממוצע דירוג: ${avg.toFixed(1)}
                <span style="color:#facc15">${"★".repeat(Math.round(avg))}</span>
                (${reviews.length} ביקורות)
            </p>
        `;
    }

    // List
    listEl.innerHTML = reviews
        .sort((a, b) => b.createdAt - a.createdAt)
        .map(r => `
            <div class="review-card">
                <div class="review-header">
                    <span class="review-author">${r.authorName}</span>
                    <span class="review-date">${formatReviewDate(r.createdAt)}</span>
                </div>
                <div class="review-stars">${"★".repeat(r.rating)}</div>
                <div class="review-text">${r.text || ""}</div>
            </div>
        `)
        .join("");
}

// Theme system
const THEME_KEY = "siteTheme"; // "light" or "dark"

function applyTheme(theme) {
    const body = document.body;
    if (theme === "dark") {
        body.classList.add("theme-dark");
        body.classList.remove("theme-light");
    } else {
        body.classList.add("theme-light");
        body.classList.remove("theme-dark");
        theme = "light";
    }
    localStorage.setItem(THEME_KEY, theme);

    const btn = document.getElementById("theme-toggle-button");
    if (btn) {
        btn.textContent = theme === "dark" ? "מצב בהיר" : "מצב כהה";
    }
}

function initTheme() {
    const saved = localStorage.getItem(THEME_KEY) || "light";
    applyTheme(saved);
}

function toggleTheme() {
    const current = localStorage.getItem(THEME_KEY) || "light";
    const next = current === "light" ? "dark" : "light";
    applyTheme(next);
}

// Helper functions for trainer management
function getPendingTrainers() {
    const raw = localStorage.getItem("pendingTrainers");
    if (!raw) return [];
    try {
        const arr = JSON.parse(raw);
        if (!Array.isArray(arr)) return [];
        
        // Ensure all trainers have ownerEmail and trainingTypes
        let changed = false;
        const trainersWithOwnerEmail = arr.map(trainer => {
            if (!trainer.ownerEmail) {
                trainer.ownerEmail = "";
                changed = true;
            }
            if (!Array.isArray(trainer.trainingTypes)) {
                trainer.trainingTypes = [];
                changed = true;
            }
            return trainer;
        });
        
        // Re-save if any ownerEmail fields were added
        if (changed) {
            savePendingTrainers(trainersWithOwnerEmail);
        }
        
        return trainersWithOwnerEmail;
    } catch (e) {
        return [];
    }
}

function savePendingTrainers(list) {
    localStorage.setItem("pendingTrainers", JSON.stringify(list));
}

function generateTrainerId() {
    return "t_" + Date.now() + "_" + Math.floor(Math.random() * 100000);
}

function getApprovedTrainers() {
    const raw = localStorage.getItem("approvedTrainers");
    if (!raw) return [];
    try {
        const arr = JSON.parse(raw);
        if (!Array.isArray(arr)) return [];
        
        // Ensure all trainers have IDs, ownerEmail, and trainingTypes
        let changed = false;
        const trainersWithIds = arr.map(trainer => {
            if (!trainer.id) {
                trainer.id = generateTrainerId();
                changed = true;
            }
            if (!trainer.ownerEmail) {
                trainer.ownerEmail = "";
                changed = true;
            }
            if (!Array.isArray(trainer.trainingTypes)) {
                trainer.trainingTypes = [];
                changed = true;
            }
            return trainer;
        });
        
        // Re-save if any fields were added
        if (changed) {
            saveApprovedTrainers(trainersWithIds);
        }
        
        return trainersWithIds;
    } catch (e) {
        return [];
    }
}

function saveApprovedTrainers(list) {
    localStorage.setItem("approvedTrainers", JSON.stringify(list));
}

// Rating helper functions
function getTrainerById(id) {
    const approvedTrainers = getApprovedTrainers();
    return approvedTrainers.find(t => t.id.toString() === id.toString());
}

function updateTrainer(updatedTrainer) {
    const approvedTrainers = getApprovedTrainers();
    const index = approvedTrainers.findIndex(t => t.id.toString() === updatedTrainer.id.toString());
    if (index !== -1) {
        approvedTrainers[index] = updatedTrainer;
        saveApprovedTrainers(approvedTrainers);
        return true;
    }
    return false;
}

function getTrainerAverageRating(trainer) {
    if (!trainer) return null;
    const ratingCount = trainer.ratingCount || 0;
    if (ratingCount === 0) return null;
    const ratingSum = trainer.ratingSum || 0;
    return ratingSum / ratingCount;
}

function renderStars(averageRating, maxStars = 5) {
    if (averageRating === null || averageRating === undefined) {
        return '<span class="star">☆</span>'.repeat(maxStars);
    }
    
    const fullStars = Math.floor(averageRating);
    const hasHalfStar = averageRating % 1 >= 0.5;
    const emptyStars = maxStars - fullStars - (hasHalfStar ? 1 : 0);
    
    let starsHtml = '';
    for (let i = 0; i < fullStars; i++) {
        starsHtml += '<span class="star filled">★</span>';
    }
    if (hasHalfStar) {
        starsHtml += '<span class="star filled">★</span>'; // Show as full for simplicity
    }
    for (let i = 0; i < emptyStars; i++) {
        starsHtml += '<span class="star">☆</span>';
    }
    
    return starsHtml;
}

// Get current user email (trimmed and lowercased)
function getCurrentUserEmail() {
    const raw = localStorage.getItem("currentUserEmail") || "";
    return raw.trim().toLowerCase();
}

// Check if current user can edit a trainer
// Note: This is now handled server-side, but kept for backward compatibility
function canEditTrainer(trainer) {
    // Permission check is now done server-side
    // This function is kept for backward compatibility
    // For client-side checks, you can use data attributes from Blade
    if (trainer && trainer.canEdit) {
        return trainer.canEdit;
    }
    return false;
}

// Check if current user is admin
// Note: This is now handled server-side, but kept for backward compatibility
function isAdmin() {
    // Admin check is now done server-side via User model
    // This function is kept for backward compatibility
    return false; // Always return false, server-side handles this
}

// Require login - redirects to /login if not logged in
// Note: This is now handled by Laravel auth middleware, but kept for backward compatibility
function requireLogin() {
    // Laravel middleware handles this, but we keep the function for compatibility
    // If called, it will redirect if not authenticated (handled by middleware)
    return true;
}

// Update navbar to hide admin link for non-admin users
// Note: This is now handled by Blade @auth directives, but kept for backward compatibility
function updateNavbarForUser() {
    // Navbar is now handled server-side with @auth directives
    // This function is kept for backward compatibility but does nothing
}

// Logout user - uses Laravel logout route
function logoutUser() {
    // Create and submit logout form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/logout';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    } else {
        // Fallback: try to get CSRF token from Laravel
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('input[name="_token"]')?.value || '';
        form.appendChild(csrfInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

// Get filtered and sorted trainers
function getFilteredAndSortedTrainers() {
    let trainers = getApprovedTrainers();
    if (!Array.isArray(trainers)) trainers = [];

    // Normalize trainingTypes and pricePerSession
    let changed = false;
    trainers.forEach(t => {
        if (!Array.isArray(t.trainingTypes)) {
            t.trainingTypes = [];
            changed = true;
        }
        if (t.pricePerSession != null && t.pricePerSession !== "") {
            t.pricePerSession = Number(t.pricePerSession);
        }
    });
    if (changed && typeof saveApprovedTrainers === "function") {
        saveApprovedTrainers(trainers);
    }

    // 1) Text search by name or city
    if (currentSearchQuery) {
        const q = currentSearchQuery.toLowerCase();
        trainers = trainers.filter(t => {
            const name = (t.fullName || "").toLowerCase();
            const city = (t.city || "").toLowerCase();
            return name.includes(q) || city.includes(q);
        });
    }

    // 2) Single training type select (from dropdown)
    if (currentSingleTypeFilter) {
        trainers = trainers.filter(t => {
            const types = Array.isArray(t.trainingTypes) ? t.trainingTypes : [];
            return types.includes(currentSingleTypeFilter);
        });
    }

    // 3) Checkbox-based filters (if used elsewhere)
    if (currentTypeFilters && currentTypeFilters.length) {
        trainers = trainers.filter(t => {
            const types = Array.isArray(t.trainingTypes) ? t.trainingTypes : [];
            if (!types.length) return false;
            return types.some(type => currentTypeFilters.includes(type));
        });
    }

    // 4) Price range
    if (currentMinPrice != null) {
        trainers = trainers.filter(t => {
            const price = Number(t.pricePerSession || t.price);
            if (isNaN(price)) return false;
            return price >= currentMinPrice;
        });
    }
    if (currentMaxPrice != null) {
        trainers = trainers.filter(t => {
            const price = Number(t.pricePerSession || t.price);
            if (isNaN(price)) return false;
            return price <= currentMaxPrice;
        });
    }

    // 5) Sorting
    if (currentSortMode === "rating_desc") {
        trainers.sort((a, b) => {
            const ra = a.ratingCount ? a.ratingSum / a.ratingCount : 0;
            const rb = b.ratingCount ? b.ratingSum / b.ratingCount : 0;
            return rb - ra;
        });
    } else if (currentSortMode === "price_asc") {
        trainers.sort((a, b) => {
            const pa = isNaN(a.pricePerSession || a.price) ? Infinity : (a.pricePerSession || a.price);
            const pb = isNaN(b.pricePerSession || b.price) ? Infinity : (b.pricePerSession || b.price);
            return pa - pb;
        });
    } else if (currentSortMode === "price_desc") {
        trainers.sort((a, b) => {
            const pa = isNaN(a.pricePerSession || a.price) ? -Infinity : (a.pricePerSession || a.price);
            const pb = isNaN(b.pricePerSession || b.price) ? -Infinity : (b.pricePerSession || b.price);
            return pb - pa;
        });
    } else if (currentSortMode === "experience_desc") {
        trainers.sort((a, b) => {
            const ea = Number(a.experienceYears || a.experience) || 0;
            const eb = Number(b.experienceYears || b.experience) || 0;
            return eb - ea;
        });
    }

    return trainers;
}

// Render public trainers (approved trainers from localStorage)
function renderPublicTrainers(filters = {}) {
    const container = document.getElementById('public-trainers-container');
    if (!container) return;
    
    let approvedTrainers = getFilteredAndSortedTrainers();
    
    container.innerHTML = '';
    
    if (!approvedTrainers.length) {
        container.innerHTML = '<div class="no-trainers" style="grid-column: 1 / -1;">לא נמצאו מאמנים בהתאם לסינון.</div>';
        return;
    }
    
    approvedTrainers.forEach(trainer => {
        const card = document.createElement('div');
        card.className = 'trainer-card';
        
        // Profile image
        let profileImg = '';
        if (trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== '') {
            profileImg = `<img src="${trainer.profileImageBase64}" alt="${trainer.fullName}" class="trainer-profile-img">`;
        }
        
        // Default avatar with initials
        const initials = trainer.fullName ? trainer.fullName.split(' ').map(n => n[0]).join('').substring(0, 2) : 'נס';
        const defaultAvatar = `<div class="trainer-avatar" style="display: ${trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== '' ? 'none' : 'flex'};">${initials}</div>`;
        
        // Badges
        let badges = '<div class="trainer-badges">';
        if (trainer.isOnline) badges += '<span class="badge badge-online">אונליין</span>';
        if (trainer.isForTeens) badges += '<span class="badge badge-teens">נוער</span>';
        if (trainer.isForWomen) badges += '<span class="badge badge-women">נשים בלבד</span>';
        badges += '</div>';
        
        // Rating display
        const averageRating = getTrainerAverageRating(trainer);
        let ratingHtml = '';
        if (averageRating !== null) {
            const ratingCount = trainer.ratingCount || 0;
            const roundedRating = Math.round(averageRating * 10) / 10; // Round to 1 decimal
            ratingHtml = `<div class="trainer-rating">
                <div class="star-row-small">${renderStars(averageRating, 5)}</div>
                <div class="rating-text">דירוג: ${roundedRating} (${ratingCount})</div>
            </div>`;
        } else {
            ratingHtml = '<div class="trainer-rating"><div class="rating-text">עדיין אין דירוגים</div></div>';
        }
        
        // Phone cleaning and contact buttons
        let whatsappButton = '';
        let callButton = '';
        
        if (trainer.phone && trainer.phone.trim() !== '') {
            const phoneDigits = trainer.phone.replace(/\D/g, '');
            if (phoneDigits.length > 0) {
                // WhatsApp button
                whatsappButton = `<a href="https://wa.me/${phoneDigits}" target="_blank" class="btn btn-primary">ווטסאפ</a>`;
                
                // Call button
                callButton = `<a href="tel:${trainer.phone}" class="btn btn-outline">התקשר</a>`;
            } else {
                whatsappButton = `<button class="btn btn-primary" onclick="alert('לא נמצא מספר טלפון עבור המאמן.'); return false;">ווטסאפ</button>`;
                callButton = `<button class="btn btn-outline" onclick="alert('לא נמצא מספר טלפון עבור המאמן.'); return false;">התקשר</button>`;
            }
        } else {
            whatsappButton = `<button class="btn btn-primary" onclick="alert('לא נמצא מספר טלפון עבור המאמן.'); return false;">ווטסאפ</button>`;
            callButton = `<button class="btn btn-outline" onclick="alert('לא נמצא מספר טלפון עבור המאמן.'); return false;">התקשר</button>`;
        }
        
        card.innerHTML = `
            <div class="trainer-card-image">
                ${profileImg}
                ${defaultAvatar}
            </div>
            <div class="verified-badge">מאמן מאושר</div>
            <h3>${trainer.fullName}</h3>
            ${ratingHtml}
            <div class="trainer-info">
                <p><strong>עיר:</strong> ${trainer.city}</p>
                <p><strong>שנות ניסיון:</strong> ${trainer.experienceYears || trainer.experience || ""}</p>
                <p><strong>התמחות עיקרית:</strong> ${trainer.mainSpecialization || trainer.specialization || ""}</p>
            </div>
            ${renderTrainingTypeBadges(trainer)}
            ${badges}
            <div class="price">₪${trainer.price} לאימון</div>
            <div class="trainer-actions">
                ${whatsappButton}
                ${callButton}
                <button type="button" class="btn btn-secondary" onclick="openTrainerProfileFromList('${trainer.id}')">לצפייה בפרופיל</button>
            </div>
        `;
        
        container.appendChild(card);
    });
}

// Profile return target helpers
function setProfileReturnTarget(target) {
    // target can be "admin" or "trainers"
    if (!target) {
        localStorage.removeItem("profileReturnTarget");
    } else {
        localStorage.setItem("profileReturnTarget", target);
    }
}

function getProfileReturnTarget() {
    return localStorage.getItem("profileReturnTarget") || "trainers";
}

// Open trainer profile page
function openTrainerProfile(trainerId) {
    if (!trainerId) return;
    localStorage.setItem("selectedTrainerId", String(trainerId));
    window.location.href = "/trainer-profile";
}

// Open trainer profile from admin panel
function openTrainerProfileFromAdmin(trainerId) {
    if (!trainerId) return;
    setProfileReturnTarget("admin");
    openTrainerProfile(trainerId);
}

// Open trainer profile from public trainers list
function openTrainerProfileFromList(trainerId) {
    if (!trainerId) return;
    setProfileReturnTarget("trainers");
    openTrainerProfile(trainerId);
}

// Go back from profile page
function goBackFromProfile() {
    const target = getProfileReturnTarget();
    // Clear the target after using it
    setProfileReturnTarget(null);

    if (target === "admin") {
        window.location.href = "/admin";
    } else {
        window.location.href = "/trainers";
    }
}

// Initialize trainers filters
function initTrainersFilters() {
    const searchInput = document.getElementById("trainerSearchInput");
    const minPriceInput = document.getElementById("minPriceFilter");
    const maxPriceInput = document.getElementById("maxPriceFilter");
    const typeSelect = document.getElementById("trainingTypeFilter");
    const sortSelect = document.getElementById("trainerSortSelect");

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            currentSearchQuery = this.value.trim();
            renderPublicTrainers();
        });
    }

    if (minPriceInput) {
        minPriceInput.addEventListener("input", function () {
            const val = this.value.trim();
            currentMinPrice = val === "" ? null : Number(val);
            if (isNaN(currentMinPrice)) currentMinPrice = null;
            renderPublicTrainers();
        });
    }

    if (maxPriceInput) {
        maxPriceInput.addEventListener("input", function () {
            const val = this.value.trim();
            currentMaxPrice = val === "" ? null : Number(val);
            if (isNaN(currentMaxPrice)) currentMaxPrice = null;
            renderPublicTrainers();
        });
    }

    if (typeSelect) {
        typeSelect.addEventListener("change", function () {
            currentSingleTypeFilter = this.value || "";
            renderPublicTrainers();
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener("change", function () {
            currentSortMode = this.value || "default";
            renderPublicTrainers();
        });
    }

    // Also support checkbox filters if they exist (for backward compatibility)
    const checkboxes = document.querySelectorAll(".filter-type");
    if (checkboxes.length) {
        checkboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                const value = this.value;
                const checked = this.checked;

                if (checked) {
                    if (!currentTypeFilters.includes(value)) {
                        currentTypeFilters.push(value);
                    }
                } else {
                    currentTypeFilters = currentTypeFilters.filter(v => v !== value);
                }

                renderPublicTrainers();
            });
        });
    }
}

// Open trainer edit page
function openTrainerEdit(trainerId) {
    if (!trainerId) return;
    localStorage.setItem("editTrainerId", String(trainerId));
    window.location.href = "/edit-trainer";
}

// Initialize trainer profile page
function initTrainerProfilePage() {
    const id = localStorage.getItem("selectedTrainerId");
    const container = document.getElementById("trainerProfileContainer");
    if (!container) return;

    if (!id) {
        container.innerHTML = "<p>לא נמצא מאמן להצגה.</p>";
        return;
    }

    let trainer = null;

    // Try approved trainers first
    let list = getApprovedTrainers && getApprovedTrainers();
    if (Array.isArray(list)) {
        trainer = list.find(t => String(t.id) === String(id));
    }

    // If not found, try pending trainers
    if (!trainer) {
        list = getPendingTrainers && getPendingTrainers();
        if (Array.isArray(list)) {
            trainer = list.find(t => String(t.id) === String(id));
        }
    }

    if (!trainer) {
        container.innerHTML = "<p>לא נמצא מאמן להצגה.</p>";
        return;
    }
    
    // Profile image
    let profileImg = '';
    if (trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== '') {
        profileImg = `<img src="${trainer.profileImageBase64}" alt="${trainer.fullName}" class="trainer-profile-large-img">`;
    }
    
    const initials = trainer.fullName ? trainer.fullName.split(' ').map(n => n[0]).join('').substring(0, 2) : 'נס';
    const defaultAvatar = `<div class="trainer-avatar-large" style="display: ${trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== '' ? 'none' : 'flex'};">${initials}</div>`;
    
    // Badges
    let badges = '<div class="trainer-profile-badges">';
    if (trainer.isOnline) badges += '<span class="badge badge-online">אונליין</span>';
    if (trainer.isForTeens) badges += '<span class="badge badge-teens">נוער</span>';
    if (trainer.isForWomen) badges += '<span class="badge badge-women">נשים בלבד</span>';
    badges += '</div>';
    
    // Training types badges
    const trainingTypesHtml = renderTrainingTypeBadges(trainer);
    
    // Social media links
    let socialLinks = '';
    if (trainer.instagram || trainer.tiktok) {
        socialLinks = '<div class="social-links"><strong>רשתות חברתיות:</strong> ';
        if (trainer.instagram) {
            const instagramUrl = trainer.instagram.startsWith('http') ? trainer.instagram : `https://instagram.com/${trainer.instagram.replace('@', '')}`;
            socialLinks += `<a href="${instagramUrl}" target="_blank" class="social-link">Instagram</a> `;
        }
        if (trainer.tiktok) {
            const tiktokUrl = trainer.tiktok.startsWith('http') ? trainer.tiktok : `https://tiktok.com/@${trainer.tiktok.replace('@', '')}`;
            socialLinks += `<a href="${tiktokUrl}" target="_blank" class="social-link">TikTok</a>`;
        }
        socialLinks += '</div>';
    }
    
    // WhatsApp and phone links
    let contactButtons = '';
    if (trainer.phone && trainer.phone.trim() !== '') {
        const phoneDigits = trainer.phone.replace(/\D/g, '');
        if (phoneDigits.length > 0) {
            contactButtons = `
                <div class="profile-actions">
                    <a href="https://wa.me/${phoneDigits}" target="_blank" class="btn btn-primary">ווטסאפ</a>
                    <a href="tel:${trainer.phone}" class="btn btn-outline">התקשר</a>
                </div>
            `;
        } else {
            contactButtons = '<div class="profile-actions"><p style="color: var(--text-muted);">לא קיים מספר טלפון למאמן זה.</p></div>';
        }
    } else {
        contactButtons = '<div class="profile-actions"><p style="color: var(--text-muted);">לא קיים מספר טלפון למאמן זה.</p></div>';
    }
    
    // Rating section
    const averageRating = getTrainerAverageRating(trainer);
    const ratingCount = trainer.ratingCount || 0;
    
    // Render stars based on average rating (RTL: 5 to 1)
    let starsHtml = '';
    for (let i = 5; i >= 1; i--) {
        const starClass = averageRating !== null && i <= Math.round(averageRating) ? 'star filled' : 'star';
        const starChar = averageRating !== null && i <= Math.round(averageRating) ? '★' : '☆';
        starsHtml += `<span class="${starClass}" data-rating="${i}">${starChar}</span>`;
    }
    
    let ratingSectionHtml = '';
    if (averageRating !== null) {
        const roundedRating = Math.round(averageRating * 10) / 10;
        ratingSectionHtml = `
            <div class="rating-section">
                <h3>דירוג המאמן</h3>
                <div class="star-row" data-trainer-id="${trainer.id}">
                    ${starsHtml}
                </div>
                <div class="rating-text">דירוג ממוצע: ${roundedRating} (${ratingCount} דירוגים)</div>
            </div>
        `;
    } else {
        ratingSectionHtml = `
            <div class="rating-section">
                <h3>דירוג המאמן</h3>
                <div class="star-row" data-trainer-id="${trainer.id}">
                    ${starsHtml}
                </div>
                <div class="rating-text">עדיין אין דירוגים למאמן זה.</div>
            </div>
        `;
    }
    
    container.innerHTML = `
        <div class="trainer-profile">
            <div class="trainer-profile-header">
                <div class="trainer-profile-image-container">
                    ${profileImg}
                    ${defaultAvatar}
                </div>
                <div class="trainer-profile-info">
                    <h1>${trainer.fullName}</h1>
                    <p class="trainer-location">📍 ${trainer.city}</p>
                    <p class="trainer-experience">💪 ${trainer.experienceYears || trainer.experience || ""} שנות ניסיון</p>
                    <p class="trainer-specialization">🎯 ${trainer.mainSpecialization || trainer.specialization || ""}</p>
                    ${badges}
                </div>
            </div>
            
            <div class="trainer-profile-details">
                <div class="price-large">₪${trainer.pricePerSession || trainer.price || ""} לאימון בודד</div>
                ${trainingTypesHtml}
                ${socialLinks}
                
                ${trainer.bio ? `<div class="trainer-bio"><h3>אודות המאמן</h3><p>${trainer.bio}</p></div>` : ''}
                
                ${contactButtons}
                
                ${ratingSectionHtml}
            </div>
        </div>
    `;
    
    // Initialize star click handlers
    const starRow = container.querySelector('.star-row');
    if (starRow) {
        const stars = starRow.querySelectorAll('.star');
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                rateTrainer(trainer.id, rating);
            });
        });
    }
    
    // Add edit button if user can edit this trainer
    if (canEditTrainer(trainer)) {
        const actions = container.querySelector(".profile-actions");
        if (actions) {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "btn btn-outline";
            btn.textContent = "עריכת פרופיל";
            btn.addEventListener("click", function () {
                openTrainerEdit(trainer.id);
            });
            actions.appendChild(btn);
        }
    }
    
    // Update back button text based on return target
    const backBtn = document.getElementById("back-from-profile-btn");
    if (backBtn) {
        const target = getProfileReturnTarget();
        if (target === "admin") {
            backBtn.textContent = "חזור לפאנל המאמנים";
        } else {
            backBtn.textContent = "חזור לרשימת המאמנים";
        }
    }

    // Initialize reviews
    const trainerId = trainer.id;
    renderReviews(trainerId);

    // Handle review form submission
    const form = document.getElementById("addReviewForm");
    const message = document.getElementById("reviewMessage");

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const name = document.getElementById("reviewerName").value.trim() || "משתמש";
            const rating = Number(document.getElementById("reviewRating").value);
            const text = document.getElementById("reviewText").value.trim();

            if (!rating) {
                if (message) message.textContent = "נא לבחור דירוג.";
                return;
            }

            addReviewForTrainer(trainerId, { rating, text, authorName: name });

            document.getElementById("reviewerName").value = "";
            document.getElementById("reviewRating").value = "";
            document.getElementById("reviewText").value = "";

            if (message) message.textContent = "הביקורת נוספה!";
            renderReviews(trainerId);
        });
    }
}

// Initialize trainer edit page
function initEditTrainerPage() {
    const id = localStorage.getItem("editTrainerId");
    if (!id) {
        alert("לא נבחר מאמן לעריכה.");
        window.location.href = "/trainers";
        return;
    }

    const trainers = getApprovedTrainers();
    const trainer = trainers.find(t => String(t.id) === String(id));

    if (!trainer) {
        alert("לא נמצא מאמן לעריכה.");
        window.location.href = "/trainers";
        return;
    }

    // Permission check:
    if (!canEditTrainer(trainer)) {
        alert("אין לך הרשאה לערוך מאמן זה.");
        window.location.href = "/trainers";
        return;
    }

    // Fill form fields:
    const form = document.getElementById("edit-trainer-form");
    if (!form) return;

    form.querySelector("#edit-fullName").value = trainer.fullName || "";
    form.querySelector("#edit-age").value = trainer.age || "";
    form.querySelector("#edit-city").value = trainer.city || "";
    form.querySelector("#edit-phone").value = trainer.phone || "";
    form.querySelector("#edit-experienceYears").value = trainer.experienceYears || trainer.experience || "";
    form.querySelector("#edit-mainSpecialization").value = trainer.mainSpecialization || trainer.specialization || "";
    form.querySelector("#edit-pricePerSession").value = trainer.pricePerSession || trainer.price || "";
    form.querySelector("#edit-instagram").value = trainer.instagram || "";
    form.querySelector("#edit-tiktok").value = trainer.tiktok || "";
    form.querySelector("#edit-bio").value = trainer.bio || "";

    const messageBox = document.getElementById("edit-trainer-message");

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        // Read new values
        trainer.fullName = form.querySelector("#edit-fullName").value.trim();
        trainer.age = parseInt(form.querySelector("#edit-age").value, 10) || trainer.age;
        trainer.city = form.querySelector("#edit-city").value.trim();
        trainer.phone = form.querySelector("#edit-phone").value.trim();
        trainer.experienceYears = parseInt(form.querySelector("#edit-experienceYears").value, 10) || trainer.experienceYears || trainer.experience;
        trainer.mainSpecialization = form.querySelector("#edit-mainSpecialization").value.trim();
        trainer.pricePerSession = parseFloat(form.querySelector("#edit-pricePerSession").value) || trainer.pricePerSession || trainer.price;
        trainer.instagram = form.querySelector("#edit-instagram").value.trim();
        trainer.tiktok = form.querySelector("#edit-tiktok").value.trim();
        trainer.bio = form.querySelector("#edit-bio").value.trim();

        // Also update legacy fields for compatibility
        trainer.experience = trainer.experienceYears;
        trainer.specialization = trainer.mainSpecialization;
        trainer.price = trainer.pricePerSession;

        // Save back to approvedTrainers
        const list = getApprovedTrainers();
        const index = list.findIndex(t => String(t.id) === String(id));
        if (index !== -1) {
            list[index] = trainer;
            saveApprovedTrainers(list);
        }

        if (messageBox) {
            messageBox.textContent = "הפרופיל עודכן בהצלחה.";
            messageBox.className = "form-message success";
        } else {
            alert("הפרופיל עודכן בהצלחה.");
        }

        // Optionally redirect back to profile:
        setTimeout(function() {
            localStorage.setItem("selectedTrainerId", String(trainer.id));
            window.location.href = "/trainer-profile";
        }, 1500);
    });
}

// Function to handle rating a trainer
function rateTrainer(trainerId, rating) {
    const trainer = getTrainerById(trainerId);
    if (!trainer) {
        alert('שגיאה: לא נמצא מאמן.');
        return;
    }
    
    // Update rating
    trainer.ratingSum = (trainer.ratingSum || 0) + rating;
    trainer.ratingCount = (trainer.ratingCount || 0) + 1;
    
    // Save updated trainer
    if (updateTrainer(trainer)) {
        // Reload the profile page to show updated rating
        initTrainerProfilePage();
    } else {
        alert('שגיאה בעדכון הדירוג.');
    }
}

// Initialize training types selector on register page
function initTrainingTypesSelectorOnRegisterPage() {
    const toggle = document.getElementById("trainingTypesToggle");
    const dropdown = document.getElementById("trainingTypesDropdown");
    const searchInput = document.getElementById("trainingTypesSearch");
    const list = document.getElementById("trainingTypesList");
    const summarySpan = document.getElementById("trainingTypesSummary");

    // Debug: Log missing elements
    if (!toggle) console.error('Training types dropdown: toggle element not found');
    if (!dropdown) console.error('Training types dropdown: dropdown element not found');
    if (!searchInput) console.error('Training types dropdown: searchInput element not found');
    if (!list) console.error('Training types dropdown: list element not found');
    if (!summarySpan) console.error('Training types dropdown: summarySpan element not found');
    
    if (!toggle || !dropdown || !searchInput || !list || !summarySpan) {
        console.error('Training types dropdown initialization failed: missing required elements');
        return;
    }
    
    // Prevent multiple initializations
    if (dropdown.dataset.initialized === 'true') {
        console.log('Training types dropdown: Already initialized, skipping...');
        return;
    }
    dropdown.dataset.initialized = 'true';
    
    console.log('Training types dropdown: Initializing...');

    const options = Array.from(list.querySelectorAll(".training-type-option"));

    function updateSummaryText() {
        // Support both "trainingTypes" and "training_types[]" for compatibility
        const checkedInputs = list.querySelectorAll('input[name="trainingTypes"]:checked, input[name="training_types[]"]:checked');
        if (!checkedInputs.length) {
            summarySpan.textContent = "בחר סוגי אימונים...";
            return;
        }

        const labels = Array.from(checkedInputs).map(input => {
            const labelEl = input.closest(".training-type-option");
            const txt = labelEl ? labelEl.querySelector(".option-label").textContent.trim() : "";
            return txt;
        }).filter(Boolean);

        if (labels.length === 1) {
            summarySpan.textContent = labels[0];
        } else if (labels.length === 2) {
            summarySpan.textContent = labels[0] + ", " + labels[1];
        } else {
            const extra = labels.length - 2;
            summarySpan.textContent = `${labels[0]}, ${labels[1]} (+${extra})`;
        }
    }

    function closeDropdown() {
        dropdown.classList.remove("open");
    }

    // Open / close dropdown - must be attached before document click listener
    toggle.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Training types dropdown: Toggle clicked, current state:', dropdown.classList.contains("open"));
        const wasOpen = dropdown.classList.contains("open");
        dropdown.classList.toggle("open");
        const isNowOpen = dropdown.classList.contains("open");
        console.log('Training types dropdown: New state:', isNowOpen);
        console.log('Training types dropdown: Dropdown element classes:', dropdown.className);
        console.log('Training types dropdown: Dropdown computed display:', window.getComputedStyle(dropdown).display);
        if (isNowOpen) {
            searchInput.value = "";
            filterOptions("");
            // Use setTimeout to ensure dropdown is visible before focusing
            setTimeout(function() {
                searchInput.focus();
                console.log('Training types dropdown: Search input focused');
            }, 10);
        } else {
            console.log('Training types dropdown: Dropdown closed');
        }
    }, true); // Use capture phase to ensure this fires first

    // Prevent clicks inside dropdown from closing it
    dropdown.addEventListener("click", function (e) {
        e.stopPropagation();
    }, true); // Use capture phase

    // Close dropdown when clicking outside - must be attached after toggle listener
    // Use a small delay to allow toggle click to process first
    // Store handler reference to allow removal if needed
    const outsideClickHandler = function (e) {
        // Don't close if clicking on toggle or dropdown
        if (toggle.contains(e.target) || dropdown.contains(e.target)) {
            return;
        }
        closeDropdown();
    };
    document.addEventListener("click", outsideClickHandler);

    // Search filter
    function filterOptions(query) {
        const q = query.trim().toLowerCase();
        console.log('Training types dropdown: Filtering options with query:', q);
        let visibleCount = 0;
        options.forEach(opt => {
            const text = opt.querySelector(".option-label").textContent.toLowerCase();
            const listItem = opt.closest(".training-type-item"); // מצא את ה-li
            if (listItem) {
                const shouldShow = !q || text.includes(q);
                listItem.style.display = shouldShow ? "flex" : "none";
                if (shouldShow) visibleCount++;
            }
        });
        console.log('Training types dropdown: Visible options after filter:', visibleCount);
    }

    searchInput.addEventListener("input", function () {
        filterOptions(this.value);
    });

    // Update summary when any checkbox changes
    // Support both "trainingTypes" and "training_types[]" for compatibility
    list.addEventListener("change", function (e) {
        if (e.target && (e.target.name === "trainingTypes" || e.target.name === "training_types[]")) {
            console.log('Training types dropdown: Checkbox changed:', e.target.value, e.target.checked);
            updateSummaryText();
        }
    });

    // Initial text
    updateSummaryText();
    console.log('Training types dropdown: Initialization complete');
}

// Trainer registration is now handled inline in register page
// Initialize login page
// Note: Login is now handled by Laravel Breeze form submission
function initLoginPage() {
    // Login form submission is handled by Laravel's POST /login route
    // This function is kept for backward compatibility but does nothing
}

// Initialize navbar toggle for mobile menu
function initNavbarToggle() {
    const toggle = document.getElementById("navToggle");
    const links = document.getElementById("navLinks");
    if (!toggle || !links) return;

    toggle.addEventListener("click", function () {
        links.classList.toggle("nav-open");
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;

    // Theme + navbar first
    initTheme && initTheme();
    updateNavbarForUser && updateNavbarForUser();
    initNavbarToggle && initNavbarToggle();
    
    // Theme toggle button
    const themeBtn = document.getElementById("theme-toggle-button");
    if (themeBtn) {
        themeBtn.addEventListener("click", toggleTheme);
    }

    if (path === "/login" || path.includes("/login")) {
        // Login page: DO NOT call requireLogin here
        initLoginPage && initLoginPage();
        return;
    }

    // Home page (/) should not require login - it's public
    if (path === "/" || path === "") {
        // Public home page - no login required
        return;
    }

    // Other pages: require login
    requireLogin && requireLogin();

    if (path === "/register" || path === "/register-trainer" || path.includes("/register")) {
        initTrainingTypesSelectorOnRegisterPage && initTrainingTypesSelectorOnRegisterPage();
    } else if (path === "/admin" || path.includes("/admin")) {
        initAdminPage && initAdminPage();
    } else if (path === "/trainers" || path.includes("/trainers")) {
        requireLogin && requireLogin();
        // Don't call renderPublicTrainers() - trainers are rendered by Blade template
        // initTrainersFilters && initTrainersFilters();
        // renderPublicTrainers && renderPublicTrainers();
        return;
    } else if (path === "/trainer-profile" || path.includes("/trainer-profile")) {
        initTrainerProfilePage && initTrainerProfilePage();
    } else if (path === "/edit-trainer" || path.includes("/edit-trainer")) {
        initEditTrainerPage && initEditTrainerPage();
    }
    
    // Trainers page functionality
    loadTrainersPage();
});

// Initialize admin page
function initAdminPage() {
    // First, require login
    requireLogin && requireLogin();

    const adminWrapper = document.getElementById('admin-wrapper');
    const adminNotAllowed = document.getElementById('admin-not-allowed');
    const loginForm = document.getElementById('loginForm');
    const adminPanel = document.getElementById('adminPanel');
    const adminLoginForm = document.getElementById('adminLoginForm');

    // Check if user is admin
    if (!isAdmin()) {
        // User is not admin - hide admin wrapper, show access denied message
        if (adminWrapper) adminWrapper.style.display = 'none';
        if (adminNotAllowed) {
            adminNotAllowed.style.display = 'block';
            adminNotAllowed.innerHTML = '<div class="admin-container" style="text-align: center; padding: 40px;"><h2 style="color: #e74c3c; margin-bottom: 20px;">אין לך הרשאה לצפות בעמוד זה.</h2></div>';
        }
        return;
    }

    // User is admin - show admin wrapper, hide access denied message
    if (adminWrapper) adminWrapper.style.display = 'block';
    if (adminNotAllowed) adminNotAllowed.style.display = 'none';

    if (!loginForm || !adminPanel) return;

    // Check if admin is logged in (password login)
    const isLoggedIn = localStorage.getItem('isAdminLoggedIn') === 'true';

    if (isLoggedIn) {
        // Show admin panel, hide login form
        loginForm.style.display = 'none';
        adminPanel.style.display = 'block';
        renderPendingTrainers();
        renderApprovedTrainers && renderApprovedTrainers();
    } else {
        // Show login form, hide admin panel
        loginForm.style.display = 'block';
        adminPanel.style.display = 'none';
    }

    // Handle login form submission (only attach once)
    if (adminLoginForm && !adminLoginForm.dataset.listenerAttached) {
        adminLoginForm.dataset.listenerAttached = 'true';
        adminLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('adminPassword').value;
            const ADMIN_PASSWORD = 'fitadmin123';
            const loginError = document.getElementById('loginError');

            if (password === ADMIN_PASSWORD) {
                // Correct password
                localStorage.setItem('isAdminLoggedIn', 'true');
                if (loginError) loginError.style.display = 'none';
                initAdminPage(); // Re-initialize to show admin panel
            } else {
                // Wrong password
                if (loginError) loginError.style.display = 'block';
                const passwordInput = document.getElementById('adminPassword');
                if (passwordInput) passwordInput.value = '';
            }
        });
    }
}

// Logout admin
function logoutAdmin() {
    localStorage.removeItem('isAdminLoggedIn');
    initAdminPage();
}

// Render pending trainers
function renderPendingTrainers() {
    const container = document.getElementById("pendingTrainersContainer");
    if (!container) return;

    const pending = getPendingTrainers();

    container.innerHTML = "";

    if (pending.length === 0) {
        container.innerHTML = '<div class="no-trainers">אין בקשות ממתינות כרגע.</div>';
        return;
    }

    pending.forEach((trainer) => {
        const card = document.createElement("div");
        card.className = "admin-trainer-card";

        // Profile image
        let imageHtml = "";
        if (trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== "") {
            imageHtml = `<img src="${trainer.profileImageBase64}" class="admin-avatar" alt="${trainer.fullName || ""}" />`;
        }

        card.innerHTML = `
            <div class="admin-card-header">
                ${imageHtml}
                <div>
                    <div class="admin-trainer-name">${trainer.fullName || ""}</div>
                    <div class="admin-trainer-city">עיר: ${trainer.city || ""}</div>
                    <div>גיל: ${trainer.age || ""}</div>
                    <div>טלפון: ${trainer.phone || ""}</div>
                </div>
            </div>
            <div class="admin-trainer-actions">
                <button type="button" class="btn-primary" onclick="openTrainerProfileFromAdmin('${trainer.id}')">
                    לצפייה בפרופיל
                </button>
            </div>
            <div class="admin-trainer-actions">
                <button type="button" class="btn btn-primary" onclick="approveTrainer('${trainer.id}')">אשר</button>
                <button type="button" class="btn btn-outline danger" onclick="rejectTrainer('${trainer.id}')">דחה</button>
            </div>
        `;

        container.appendChild(card);
    });
}

// Toggle pending trainer details
function togglePendingDetails(trainerId) {
    const el = document.getElementById("pending-extra-" + trainerId);
    if (!el) {
        console.warn("pending extra element not found for trainer:", trainerId);
        return;
    }

    if (el.style.display === "none" || el.style.display === "") {
        el.style.display = "block";
    } else {
        el.style.display = "none";
    }
}

// Render approved trainers
function renderApprovedTrainers() {
    const container = document.getElementById("approvedTrainersContainer");
    if (!container) return;

    const list = getApprovedTrainers();

    container.innerHTML = "";

    if (list.length === 0) {
        container.innerHTML = '<div class="no-trainers">אין מאמנים מאושרים כרגע.</div>';
        return;
    }

    list.forEach((trainer) => {
        const card = document.createElement("div");
        card.className = "admin-trainer-card approved";

        // Profile image
        let imageHtml = "";
        if (trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== "") {
            imageHtml = `<img src="${trainer.profileImageBase64}" class="admin-avatar" alt="${trainer.fullName || ""}" />`;
        }

        card.innerHTML = `
            <div class="admin-card-header">
                ${imageHtml}
                <div>
                    <div class="admin-trainer-name">${trainer.fullName || ""}</div>
                    <div class="admin-trainer-city">עיר: ${trainer.city || ""}</div>
                </div>
            </div>
            <div class="admin-trainer-details">
                <div>שנות ניסיון: ${trainer.experienceYears || ""}</div>
                <div>התמחות: ${trainer.mainSpecialization || ""}</div>
                <div>מחיר לאימון: ₪${trainer.pricePerSession || ""}</div>
            </div>
            <div class="admin-trainer-actions">
                <button type="button" class="btn btn-outline danger" onclick="deleteTrainer('${trainer.id}')">מחק</button>
            </div>
        `;

        container.appendChild(card);
    });
}

// Approve trainer
function approveTrainer(trainerId) {
    const pending = getPendingTrainers();
    const approved = getApprovedTrainers();

    const index = pending.findIndex(t => String(t.id) === String(trainerId));
    if (index === -1) {
        console.warn("Trainer not found in pending list:", trainerId);
        return;
    }

    const trainer = pending.splice(index, 1)[0];
    approved.push(trainer);

    savePendingTrainers(pending);
    saveApprovedTrainers(approved);

    // Re-render lists after change
    renderPendingTrainers();
    renderApprovedTrainers && renderApprovedTrainers();
}

// Reject trainer
function rejectTrainer(trainerId) {
    const pending = getPendingTrainers();
    const index = pending.findIndex(t => String(t.id) === String(trainerId));
    if (index === -1) {
        console.warn("Trainer not found in pending list:", trainerId);
        return;
    }

    pending.splice(index, 1);
    savePendingTrainers(pending);
    renderPendingTrainers();
}

// Delete approved trainer
function deleteTrainer(trainerId) {
    const approved = getApprovedTrainers();
    const index = approved.findIndex(t => String(t.id) === String(trainerId));
    if (index === -1) {
        console.warn("Trainer not found in approvedTrainers:", trainerId);
        return;
    }

    approved.splice(index, 1);
    saveApprovedTrainers(approved);

    // Re-render admin lists if on admin page
    renderApprovedTrainers && renderApprovedTrainers();
    renderPublicTrainers && renderPublicTrainers();
}

// Delete approved trainer
function deleteApprovedTrainer(trainerId) {
    if (!confirm('האם אתה בטוח שברצונך למחוק מאמן זה?')) {
        return;
    }
    
    let approvedTrainers = getApprovedTrainers();
    
    const trainerIndex = approvedTrainers.findIndex(t => t.id === trainerId);
    
    if (trainerIndex !== -1) {
        approvedTrainers.splice(trainerIndex, 1);
        saveApprovedTrainers(approvedTrainers);
        
        // Re-render approved list
        renderApprovedTrainers();
    }
}

// Load trainers page
function loadTrainersPage() {
    const trainersGrid = document.getElementById('trainersGrid');
    const searchBtn = document.getElementById('searchBtn');
    
    if (!trainersGrid) return;
    
    // Example trainers (static)
    let trainers = [
        {
            name: 'דני כהן',
            city: 'תל אביב',
            experience: 8,
            specializations: ['חיטוב', 'כושר כללי'],
            price: 150,
            verified: true,
            id: 1
        },
        {
            name: 'שרה לוי',
            city: 'רמת גן',
            experience: 5,
            specializations: ['נשים בלבד', 'יוגה'],
            price: 120,
            verified: true,
            id: 2
        },
        {
            name: 'מיכאל דוד',
            city: 'חיפה',
            experience: 12,
            specializations: ['עלייה במסה', 'כוח'],
            price: 180,
            verified: false,
            id: 3
        }
    ];
    
    // Add approved trainers from localStorage
    const approvedTrainers = getApprovedTrainers();
    approvedTrainers.forEach(trainer => {
        trainers.push({
            name: trainer.fullName,
            city: trainer.city,
            experience: trainer.experience,
            specializations: [trainer.specialization],
            price: trainer.price,
            verified: true,
            id: trainer.id
        });
    });
    
    // Render trainers
    renderTrainers(trainers);
    
    // Search functionality
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const cityFilter = document.getElementById('cityFilter').value;
            const specializationFilter = document.getElementById('specializationFilter').value;
            
            let filtered = trainers;
            
            if (cityFilter && cityFilter !== 'all') {
                filtered = filtered.filter(t => t.city === cityFilter);
            }
            
            if (specializationFilter && specializationFilter !== 'all') {
                filtered = filtered.filter(t => 
                    t.specializations.some(s => s === specializationFilter)
                );
            }
            
            renderTrainers(filtered);
        });
    }
}

// Render trainer cards
function renderTrainers(trainers) {
    const trainersGrid = document.getElementById('trainersGrid');
    if (!trainersGrid) return;
    
    trainersGrid.innerHTML = '';
    
    if (trainers.length === 0) {
        trainersGrid.innerHTML = '<div class="no-trainers">לא נמצאו מאמנים התואמים לחיפוש</div>';
        return;
    }
    
    trainers.forEach(trainer => {
        const card = document.createElement('div');
        card.className = 'trainer-card';
        
        let verifiedBadge = '';
        if (trainer.verified) {
            verifiedBadge = '<div class="verified-badge">מאמן מאומת</div>';
        }
        
        card.innerHTML = `
            ${verifiedBadge}
            <h3>${trainer.name}</h3>
            <div class="trainer-info">
                <p><strong>עיר:</strong> ${trainer.city}</p>
                <p><strong>שנות ניסיון:</strong> ${trainer.experience}</p>
                <p><strong>התמחויות:</strong> ${trainer.specializations.join(', ')}</p>
            </div>
            <div class="price">₪${trainer.price} לאימון</div>
            <button class="btn" onclick="contactTrainer('${trainer.name}')">צור קשר</button>
        `;
        
        trainersGrid.appendChild(card);
    });
}

// Contact trainer function
function contactTrainer(trainerName) {
    alert('יצירת קשר עם המאמן תתווסף בהמשך.');
}

// Counter animation for stats section
function animateCounter(element, target, duration = 2000) {
    const start = 0;
    const increment = target / (duration / 16); // 60fps
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target % 1 === 0 ? Math.floor(target) : target.toFixed(1);
            clearInterval(timer);
        } else {
            element.textContent = current % 1 === 0 ? Math.floor(current) : current.toFixed(1);
        }
    }, 16);
}

// Initialize stats counter animation with Intersection Observer
function initStatsCounter() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    if (statNumbers.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                const target = parseFloat(entry.target.dataset.target);
                entry.target.dataset.animated = 'true';
                animateCounter(entry.target, target);
            }
        });
    }, {
        threshold: 0.5
    });
    
    statNumbers.forEach(stat => {
        observer.observe(stat);
    });
}

// Initialize fade-in animations with Intersection Observer
function initFadeInAnimations() {
    const fadeElements = document.querySelectorAll('.fade-in, .card, .feature-card, .stat-card');
    
    if (fadeElements.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.classList.add('visible');
                }, index * 100); // Staggered animation
            }
        });
    }, {
        threshold: 0.1
    });
    
    fadeElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        observer.observe(element);
    });
}

// Smooth scroll behavior
document.documentElement.style.scrollBehavior = 'smooth';

// Initialize animations on page load
document.addEventListener('DOMContentLoaded', function() {
    initStatsCounter();
    initFadeInAnimations();
});

