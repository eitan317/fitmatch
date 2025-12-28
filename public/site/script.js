// Admin email constant
const ADMIN_EMAIL = "eitansheli2019@gmail.com";

// Training types labels
const TRAINING_TYPE_LABELS = {
    // Gym / Strength
    "strength_training": "אימוני כוח",
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
    "cardiovascular_endurance": "סיבולת לב ריאה",
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
        // הסרתי: e.preventDefault() ו-e.stopPropagation() - חוסמים את הגלילה
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

    // Close dropdown when scrolling - passive listener to not block scrolling
    document.addEventListener('scroll', function() {
        if (dropdown && dropdown.classList.contains('open')) {
            // סגירת dropdown כשגוללים - רק אם הגלילה היא של ה-body
            // לא נסגור אם הגלילה היא בתוך element אחר (אבל אין scroll containers נפרדים יותר)
            closeDropdown();
        }
    }, { passive: true }); // passive: true - לא חוסם את הגלילה

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
    const body = document.body;
    
    if (!toggle || !links) return;

    toggle.addEventListener("click", function (e) {
        e.stopPropagation(); // מונע סגירה מיידית
        toggle.classList.toggle("active"); // Toggle hamburger animation
        links.classList.toggle("nav-open");
        
        // מונע scroll של body כשהתפריט פתוח
        if (links.classList.contains("nav-open")) {
            body.style.overflow = "hidden";
        } else {
            body.style.overflow = "";
        }
        
        // Close language menu when opening nav
        const languageMenu = document.getElementById('languageMenu');
        if (languageMenu) {
            languageMenu.classList.remove('active');
        }
    });
    
    // Close nav when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!toggle.contains(e.target) && !links.contains(e.target)) {
                toggle.classList.remove('active');
                links.classList.remove('nav-open');
                body.style.overflow = "";
            }
        }
    });
    
    // Close nav when clicking on a link
    const navLinks = links.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggle.classList.remove('active');
                links.classList.remove('nav-open');
                body.style.overflow = "";
            }
        });
    });
}

// Language Selector Toggle
function initLanguageSelector() {
    const languageToggle = document.getElementById('languageToggle');
    const languageMenu = document.getElementById('languageMenu');
    
    if (!languageToggle || !languageMenu) return;
    
    languageToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        languageMenu.classList.toggle('active');
    });
    
    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!languageToggle.contains(e.target) && !languageMenu.contains(e.target)) {
            languageMenu.classList.remove('active');
        }
    });
    
    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && languageMenu.classList.contains('active')) {
            languageMenu.classList.remove('active');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;

    // Theme + navbar first
    initTheme && initTheme();
    updateNavbarForUser && updateNavbarForUser();
    initNavbarToggle && initNavbarToggle();
    initLanguageSelector && initLanguageSelector();
    
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



// ============================================
// REGISTRATION ACCORDION FUNCTIONALITY
// ============================================

function initRegistrationAccordion() {
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    if (accordionHeaders.length === 0) {
        return; // Not on registration page
    }
    
    // Ensure all sections start closed
    document.querySelectorAll('.accordion-section').forEach(section => {
        section.classList.remove('active');
        const header = section.querySelector('.accordion-header');
        if (header) {
            header.setAttribute('aria-expanded', 'false');
        }
        const content = section.querySelector('.accordion-content');
        if (content) {
            content.style.maxHeight = '0';
            content.style.padding = '0 1.75rem';
        }
    });
    
    accordionHeaders.forEach(header => {
        // Click event
        header.addEventListener('click', function() {
            toggleAccordionSection(this);
        });
        
        // Keyboard navigation
        header.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleAccordionSection(this);
            }
        });
    });
}

function toggleAccordionSection(header) {
    const section = header.closest('.accordion-section');
    const isActive = section.classList.contains('active');
    
    // Close all other sections (optional - can be changed to allow multiple open)
    if (!isActive) {
        document.querySelectorAll('.accordion-section.active').forEach(activeSection => {
            activeSection.classList.remove('active');
            const activeHeader = activeSection.querySelector('.accordion-header');
            if (activeHeader) {
                activeHeader.setAttribute('aria-expanded', 'false');
            }
        });
    }
    
    // Toggle current section
    section.classList.toggle('active');
    header.setAttribute('aria-expanded', section.classList.contains('active'));
    
    // Smooth scroll to section if opening
    if (section.classList.contains('active')) {
        setTimeout(() => {
            section.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start',
                inline: 'nearest'
            });
        }, 50); // Reduced from 100ms for faster response
    }
    
    // Update progress after toggle
    updateRegistrationProgress();
}

// ============================================
// REGISTRATION PROGRESS TRACKING
// ============================================

function updateRegistrationProgress() {
    const form = document.getElementById('trainerRegistrationForm');
    if (!form) return;
    
    const sections = [
        {
            num: 1,
            required: ['full_name', 'city'],
            optional: ['age', 'phone', 'experience_years', 'main_specialization']
        },
        {
            num: 2,
            required: [], // At least one training type
            optional: []
        },
        {
            num: 3,
            required: [],
            optional: ['price_per_session']
        },
        {
            num: 4,
            required: [],
            optional: ['instagram', 'tiktok', 'bio']
        }
    ];
    
    let completedSections = 0;
    let totalRequired = 0;
    let completedRequired = 0;
    
    sections.forEach((section, index) => {
        const sectionElement = document.querySelector(`.accordion-section[data-section="${section.num}"]`);
        const progressItem = document.querySelector(`.progress-section-item[data-section="${section.num}"]`);
        
        if (!sectionElement || !progressItem) return;
        
        let sectionStatus = 'not-started';
        let hasRequired = true;
        let hasAnyField = false;
        
        // Check required fields
        if (section.num === 1) {
            // Personal details
            const fullName = form.querySelector('#full_name')?.value.trim();
            const city = form.querySelector('#city')?.value.trim();
            hasRequired = fullName && city;
            hasAnyField = hasRequired || form.querySelector('#age')?.value || form.querySelector('#phone')?.value || form.querySelector('#experience_years')?.value || form.querySelector('#main_specialization')?.value;
        } else if (section.num === 2) {
            // Training types - at least one checkbox checked
            const checkedTypes = form.querySelectorAll('input[name="training_types[]"]:checked');
            hasRequired = checkedTypes.length > 0;
            hasAnyField = hasRequired;
        } else if (section.num === 3) {
            // Pricing - optional
            hasRequired = true;
            hasAnyField = form.querySelector('#price_per_session')?.value;
        } else if (section.num === 4) {
            // Additional details - optional
            hasRequired = true;
            hasAnyField = form.querySelector('#instagram')?.value || form.querySelector('#tiktok')?.value || form.querySelector('#bio')?.value;
        }
        
        // Determine status
        if (hasRequired && hasAnyField) {
            sectionStatus = 'completed';
            completedSections++;
        } else if (hasAnyField) {
            sectionStatus = 'in-progress';
        } else {
            sectionStatus = 'not-started';
        }
        
        // Update section element
        sectionElement.classList.remove('not-started', 'in-progress', 'completed');
        sectionElement.classList.add(sectionStatus);
        
        // Update progress item
        progressItem.classList.remove('not-started', 'in-progress', 'completed');
        progressItem.classList.add(sectionStatus);
        
        // Update status icon
        const statusIcon = progressItem.querySelector('.section-status-icon');
        const accordionStatusIcon = sectionElement.querySelector('.accordion-header .section-status-icon');
        
        if (sectionStatus === 'completed') {
            if (statusIcon) statusIcon.textContent = '✓';
            if (accordionStatusIcon) accordionStatusIcon.textContent = '✓';
        } else if (sectionStatus === 'in-progress') {
            if (statusIcon) statusIcon.textContent = '◐';
            if (accordionStatusIcon) accordionStatusIcon.textContent = '◐';
        } else {
            if (statusIcon) statusIcon.textContent = '○';
            if (accordionStatusIcon) accordionStatusIcon.textContent = '○';
        }
        
        // Count required fields
        if (section.required.length > 0 || section.num === 2) {
            totalRequired++;
            if (hasRequired) completedRequired++;
        }
    });
    
    // Update progress bar
    const progressPercentage = (completedSections / sections.length) * 100;
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressPercentageText = document.getElementById('progressPercentage');
    
    if (progressBar) {
        progressBar.style.width = progressPercentage + '%';
    }
    
    if (progressText) {
        progressText.textContent = `סקציה ${completedSections} מתוך ${sections.length}`;
    }
    
    if (progressPercentageText) {
        progressPercentageText.textContent = Math.round(progressPercentage) + '%';
    }
}

// ============================================
// REGISTRATION FORM VALIDATION
// ============================================

function validateRegistrationForm() {
    const form = document.getElementById('trainerRegistrationForm');
    if (!form) return true;
    
    let isValid = true;
    const errors = [];
    
    // Section 1: Personal Details
    const fullName = form.querySelector('#full_name')?.value.trim();
    const city = form.querySelector('#city')?.value.trim();
    
    if (!fullName) {
        errors.push({ section: 1, field: 'full_name', message: 'שם מלא הוא שדה חובה' });
        isValid = false;
    }
    
    if (!city) {
        errors.push({ section: 1, field: 'city', message: 'עיר היא שדה חובה' });
        isValid = false;
    }
    
    // Section 2: Training Types
    const checkedTypes = form.querySelectorAll('input[name="training_types[]"]:checked');
    if (checkedTypes.length === 0) {
        errors.push({ section: 2, field: 'training_types', message: 'יש לבחור לפחות סוג אימון אחד' });
        isValid = false;
    }
    
    // Show errors and open relevant sections
    if (!isValid) {
        errors.forEach(error => {
            const section = document.querySelector(`.accordion-section[data-section="${error.section}"]`);
            if (section) {
                section.classList.add('active');
                const header = section.querySelector('.accordion-header');
                if (header) {
                    header.setAttribute('aria-expanded', 'true');
                }
            }
        });
        
        // Scroll to first error
        if (errors.length > 0) {
            const firstErrorSection = document.querySelector(`.accordion-section[data-section="${errors[0].section}"]`);
            if (firstErrorSection) {
                firstErrorSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    }
    
    return isValid;
}

// Initialize progress tracking on form inputs
function initRegistrationProgressTracking() {
    const form = document.getElementById('trainerRegistrationForm');
    if (!form) return;
    
    // Track all input changes
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', updateRegistrationProgress);
        input.addEventListener('change', updateRegistrationProgress);
    });
    
    // Track checkbox changes for training types
    const checkboxes = form.querySelectorAll('input[name="training_types[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateRegistrationProgress);
    });
    
    // Initial progress update (with delay to ensure DOM is ready)
    setTimeout(updateRegistrationProgress, 100);
}

// ============================================
// REUSABLE MOBILE SLIDER WITH POINTER EVENTS
// ============================================

/**
 * Initialize a mobile-first slider with Pointer Events API
 * @param {string} containerSelector - CSS selector for the slider container
 * @param {Object} options - Configuration options
 */
// Simple, reliable mobile slider with Pointer Events
function initMobileSlider(containerSelector, options = {}) {
    const container = document.querySelector(containerSelector);
    if (!container) {
        console.warn('Slider container not found:', containerSelector);
        return;
    }

    const {
        cardsPerView = 1,
        gap = 16,
        showIndicators = true,
        swipeThreshold = 60,
        velocityThreshold = 0.3
    } = options;

    // Find track - try multiple methods
    let track = container.querySelector('.features-slider-track') ||
                container.querySelector('.stats-slider-track') ||
                container.querySelector('.trainers-slider-track') ||
                container.querySelector('[class*="slider-track"]') ||
                container.firstElementChild;
    
    if (!track) {
        console.warn('Slider track not found in:', containerSelector);
        return;
    }

    // Get all cards
    const cards = Array.from(track.children).filter(child => 
        child.classList.contains('feature-card') ||
        child.classList.contains('stat-card') ||
        child.classList.contains('trainer-card') ||
        child.classList.contains('admin-stat-card') ||
        child.classList.contains('admin-trainer-card') ||
        child.classList.contains('faq-item') ||
        (!child.classList.contains('slider-indicators') && child.nodeType === 1)
    );
    
    if (cards.length === 0) {
        console.warn('No cards found in slider');
        return;
    }

    const isMobile = () => window.innerWidth < 768;
    
    // Slider state
    let currentIndex = 0;
    let isDragging = false;
    let startX = 0;
    let currentX = 0;
    let startTime = 0;
    let currentPointerId = null;
    let startY = 0; // Track Y for vertical scroll detection

    // Update position - HORIZONTAL
    function updatePosition(index, animate = true) {
        if (!isMobile()) {
            track.style.transform = 'none';
            track.style.transition = '';
            return;
        }

        currentIndex = Math.max(0, Math.min(cards.length - cardsPerView, index));
        
        if (animate) {
            track.style.transition = 'transform 200ms ease-out';
        } else {
            track.style.transition = 'none';
        }

        const containerWidth = container.offsetWidth;
        const cardWidth = containerWidth;
        const offset = -currentIndex * cardWidth;
        
        track.style.transform = `translateX(${offset}px)`;
        
        if (showIndicators) updateIndicators();
    }

    // Create indicators
    let indicatorsContainer = null;
    function createIndicators() {
        if (!showIndicators || !isMobile()) return;
        
        let existing = container.querySelector('.slider-indicators');
        if (existing) existing.remove();
        
        indicatorsContainer = document.createElement('div');
        indicatorsContainer.className = 'slider-indicators';
        container.appendChild(indicatorsContainer);

        const totalPages = Math.ceil(cards.length / cardsPerView);
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('button');
            dot.className = 'slider-dot';
            dot.setAttribute('aria-label', `עמוד ${i + 1}`);
            dot.addEventListener('click', () => updatePosition(i * cardsPerView, true));
            indicatorsContainer.appendChild(dot);
        }
        updateIndicators();
    }

    function updateIndicators() {
        if (!indicatorsContainer) return;
        const currentPage = Math.floor(currentIndex / cardsPerView);
        const dots = indicatorsContainer.querySelectorAll('.slider-dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentPage);
        });
    }

    // Check if element is interactive (should not trigger swipe)
    function isInteractiveElement(element) {
        if (!element) return false;
        const tagName = element.tagName.toLowerCase();
        const interactiveTags = ['a', 'button', 'input', 'select', 'textarea', 'label'];
        if (interactiveTags.includes(tagName)) return true;
        
        // Check if element has click handlers or is inside a link/button
        while (element && element !== container) {
            if (element.onclick || element.getAttribute('onclick')) return true;
            if (element.tagName && interactiveTags.includes(element.tagName.toLowerCase())) return true;
            element = element.parentElement;
        }
        return false;
    }

    // Pointer events - HORIZONTAL swipe - attach to CONTAINER so it works everywhere
    function handleDown(e) {
        if (!isMobile() || e.isPrimary === false) return;
        
        // Don't start swipe if clicking on interactive elements (unless it's a card itself)
        const target = e.target;
        if (isInteractiveElement(target) && 
            !target.closest('.feature-card, .stat-card, .trainer-card, .admin-stat-card, .admin-trainer-card, .faq-item')) {
            return;
        }
        
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY; // Track Y for vertical scroll detection
        currentX = startX;
        startTime = Date.now();
        currentPointerId = e.pointerId;
        container.setPointerCapture(e.pointerId);
        track.classList.add('dragging');
        // Don't prevent default on pointerdown - let the browser handle initial touch
    }

    function handleMove(e) {
        if (!isDragging || !isMobile() || e.pointerId !== currentPointerId) return;
        
        currentX = e.clientX;
        const currentY = e.clientY;
        const deltaX = currentX - startX;
        const deltaY = Math.abs(currentY - startY);
        
        // If vertical movement is greater than horizontal, allow scrolling instead
        if (deltaY > Math.abs(deltaX) && deltaY > 10) {
            // This is a vertical scroll, don't prevent it
            isDragging = false;
            track.classList.remove('dragging');
            container.releasePointerCapture(currentPointerId);
            currentPointerId = null;
            return;
        }
        
        // Only prevent default if we have significant horizontal movement (slider swipe)
        // Small movements should allow page scrolling
        if (Math.abs(deltaX) > 10) {
            const containerWidth = container.offsetWidth;
            const baseOffset = -currentIndex * containerWidth;
            let offset = baseOffset + deltaX;
            
            const maxIndex = cards.length - cardsPerView;
            if (currentIndex === 0 && deltaX > 0) {
                offset = baseOffset + deltaX * 0.3;
            } else if (currentIndex >= maxIndex && deltaX < 0) {
                offset = baseOffset + deltaX * 0.3;
            }
            
            track.style.transform = `translateX(${offset}px)`;
            track.style.transition = 'none';
            e.preventDefault(); // Only prevent default for significant slider swipes
        }
    }

    function handleUp(e) {
        if (!isDragging || !isMobile() || e.pointerId !== currentPointerId) return;
        
        const deltaX = currentX - startX;
        const deltaY = Math.abs(e.clientY - startY);
        const deltaTime = Date.now() - startTime;
        const velocity = deltaTime > 0 ? Math.abs(deltaX) / deltaTime : 0;
        
        // If it was more of a vertical scroll, don't swipe
        if (deltaY > Math.abs(deltaX) && deltaY > 10) {
            isDragging = false;
            track.classList.remove('dragging');
            container.releasePointerCapture(currentPointerId);
            currentPointerId = null;
            updatePosition(currentIndex, true);
            return;
        }
        
        let newIndex = currentIndex;
        const isRTL = document.documentElement.dir === 'rtl';

        // Horizontal swipe: swipe left = next, swipe right = previous (Instagram-style)
        if (Math.abs(deltaX) > swipeThreshold || velocity > velocityThreshold) {
            if (deltaX < 0) { // Swiping left
                newIndex = isRTL ? Math.max(0, currentIndex - 1) : Math.min(cards.length - cardsPerView, currentIndex + 1);
            } else { // Swiping right
                newIndex = isRTL ? Math.min(cards.length - cardsPerView, currentIndex + 1) : Math.max(0, currentIndex - 1);
            }
        }
        
        isDragging = false;
        track.classList.remove('dragging');
        container.releasePointerCapture(currentPointerId);
        currentPointerId = null;
        
        updatePosition(newIndex, true);
        e.preventDefault();
    }

    function handleCancel(e) {
        isDragging = false;
        track.classList.remove('dragging');
        if (currentPointerId !== null) {
            container.releasePointerCapture(currentPointerId);
            currentPointerId = null;
        }
        updatePosition(currentIndex, true);
    }

    // Attach events to CONTAINER (not track) so it works everywhere
    container.addEventListener('pointerdown', handleDown);
    container.addEventListener('pointermove', handleMove);
    container.addEventListener('pointerup', handleUp);
    container.addEventListener('pointercancel', handleCancel);

    // Initialize
    updatePosition(0, false);
    if (isMobile()) createIndicators();

    // Resize handler
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updatePosition(0, false);
            if (isMobile()) {
                createIndicators();
            } else {
                if (indicatorsContainer) indicatorsContainer.remove();
            }
        }, 150);
    });
}

// Load background images asynchronously for better performance
function loadBackgroundImages() {
    // Only load on desktop devices
    if (window.innerWidth <= 768) {
        return;
    }
    
    const bgImages = [
        'https://totalfusion.com.au/wp-content/uploads/elementor/thumbs/2023_02_08_SF_GymFloor-860-1024x683-1-qep3pa1r6s1f1ajydt3vni5ytgt0pju0tyjufoufzk.jpg',
        'https://plus.unsplash.com/premium_photo-1661580282598-6883482b4c8e?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cGVyc29uYWwlMjB0cmFpbmVyfGVufDB8fDB8fHww',
        'https://t3.ftcdn.net/jpg/03/17/91/76/360_F_317917629_HjBCyRlH1Hpwwg2HfEbExTdkbyWiGFuN.jpg'
    ];
    
    // Preload images
    const imagePromises = bgImages.map(src => {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(src);
            img.onerror = () => reject(src);
            img.src = src;
        });
    });
    
    // After images are loaded, apply them to body background
    Promise.allSettled(imagePromises).then(results => {
        const loadedImages = results
            .filter(r => r.status === 'fulfilled')
            .map(r => r.value);
        
        if (loadedImages.length >= 2) {
            const body = document.body;
            // Apply multi-layer background
            body.style.backgroundImage = `
                linear-gradient(to bottom, rgba(10, 26, 31, 0.85) 0%, rgba(10, 26, 31, 0.75) 30%, rgba(10, 26, 31, 0.80) 70%, rgba(10, 26, 31, 0.85) 100%),
                linear-gradient(135deg, rgba(10, 26, 31, 0.9) 0%, rgba(0, 26, 26, 0.8) 50%, rgba(10, 26, 31, 0.9) 100%),
                url('${loadedImages[2] || loadedImages[1]}'),
                url('${loadedImages[1] || loadedImages[0]}'),
                url('${loadedImages[0]}')
            `;
            body.style.backgroundSize = 'cover, cover, 800px 600px, 1200px 900px, cover';
            body.style.backgroundPosition = 'center center, center center, top 10% right 5%, center 40%, center center';
            body.style.backgroundBlendMode = 'normal, multiply, soft-light, overlay, normal';
            body.classList.add('bg-images-loaded');
        }
    });
}

// Initialize animations on page load
document.addEventListener('DOMContentLoaded', function() {
    initStatsCounter();
    
    // Load background images asynchronously (after page load)
    if (document.readyState === 'loading') {
        window.addEventListener('load', loadBackgroundImages);
    } else {
        // Page already loaded, start loading images after a short delay
        setTimeout(loadBackgroundImages, 100);
    }
});


