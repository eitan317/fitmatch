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
let currentTypeFilters = [];
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
const THEME_KEY = "siteTheme";

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

// Helpers for trainer management
function getPendingTrainers() {
    const raw = localStorage.getItem("pendingTrainers");
    if (!raw) return [];
    try {
        const arr = JSON.parse(raw);
        if (!Array.isArray(arr)) return [];

        let changed = false;
        const trainersWithOwnerEmail = arr.map(trainer => {
            if (!trainer.ownerEmail) { trainer.ownerEmail = ""; changed = true; }
            if (!Array.isArray(trainer.trainingTypes)) { trainer.trainingTypes = []; changed = true; }
            return trainer;
        });

        if (changed) savePendingTrainers(trainersWithOwnerEmail);
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

        let changed = false;
        const trainersWithIds = arr.map(trainer => {
            if (!trainer.id) { trainer.id = generateTrainerId(); changed = true; }
            if (!trainer.ownerEmail) { trainer.ownerEmail = ""; changed = true; }
            if (!Array.isArray(trainer.trainingTypes)) { trainer.trainingTypes = []; changed = true; }
            return trainer;
        });

        if (changed) saveApprovedTrainers(trainersWithIds);
        return trainersWithIds;
    } catch (e) {
        return [];
    }
}

function saveApprovedTrainers(list) {
    localStorage.setItem("approvedTrainers", JSON.stringify(list));
}

// Rating helpers
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
    for (let i = 0; i < fullStars; i++) starsHtml += '<span class="star filled">★</span>';
    if (hasHalfStar) starsHtml += '<span class="star filled">★</span>';
    for (let i = 0; i < emptyStars; i++) starsHtml += '<span class="star">☆</span>';

    return starsHtml;
}

// Current user
function getCurrentUserEmail() {
    const raw = localStorage.getItem("currentUserEmail") || "";
    return raw.trim().toLowerCase();
}

function canEditTrainer(trainer) {
    const current = getCurrentUserEmail();
    if (!current) return false;
    const owner = (trainer.ownerEmail || "").toLowerCase();
    if (current === owner) return true;
    if (current === ADMIN_EMAIL.toLowerCase()) return true;
    return false;
}

function isAdmin() {
    return getCurrentUserEmail() === ADMIN_EMAIL.toLowerCase();
}

// ✅ Laravel routes (NO .html)
const ROUTES = {
    home: "/",
    login: "/login",
    register: "/register",
    trainers: "/trainers",
    admin: "/admin",
    trainerProfile: "/trainer-profile",
    editTrainer: "/edit-trainer"
};

// Require login - redirects to /login if not logged in
function requireLogin() {
    const currentUserEmail = getCurrentUserEmail();
    if (!currentUserEmail) {
        window.location.href = ROUTES.login;
        return false;
    }
    return true;
}

function updateNavbarForUser() {
    const adminLink = document.getElementById('admin-link');
    if (!adminLink) return;
    adminLink.style.display = isAdmin() ? '' : 'none';
}

function logoutUser() {
    localStorage.removeItem('currentUserEmail');
    localStorage.removeItem('isAdminLoggedIn');
    window.location.href = ROUTES.login;
}

// Get filtered and sorted trainers
function getFilteredAndSortedTrainers() {
    let trainers = getApprovedTrainers();
    if (!Array.isArray(trainers)) trainers = [];

    let changed = false;
    trainers.forEach(t => {
        if (!Array.isArray(t.trainingTypes)) { t.trainingTypes = []; changed = true; }
        if (t.pricePerSession != null && t.pricePerSession !== "") t.pricePerSession = Number(t.pricePerSession);
    });
    if (changed) saveApprovedTrainers(trainers);

    if (currentSearchQuery) {
        const q = currentSearchQuery.toLowerCase();
        trainers = trainers.filter(t => {
            const name = (t.fullName || "").toLowerCase();
            const city = (t.city || "").toLowerCase();
            return name.includes(q) || city.includes(q);
        });
    }

    if (currentSingleTypeFilter) {
        trainers = trainers.filter(t => (t.trainingTypes || []).includes(currentSingleTypeFilter));
    }

    if (currentTypeFilters.length) {
        trainers = trainers.filter(t => (t.trainingTypes || []).some(type => currentTypeFilters.includes(type)));
    }

    if (currentMinPrice != null) {
        trainers = trainers.filter(t => {
            const price = Number(t.pricePerSession || t.price);
            return !isNaN(price) && price >= currentMinPrice;
        });
    }
    if (currentMaxPrice != null) {
        trainers = trainers.filter(t => {
            const price = Number(t.pricePerSession || t.price);
            return !isNaN(price) && price <= currentMaxPrice;
        });
    }

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

// Render public trainers
function renderPublicTrainers() {
    const container = document.getElementById('public-trainers-container');
    if (!container) return;

    const approvedTrainers = getFilteredAndSortedTrainers();
    container.innerHTML = '';

    if (!approvedTrainers.length) {
        container.innerHTML = '<div class="no-trainers" style="grid-column: 1 / -1;">לא נמצאו מאמנים בהתאם לסינון.</div>';
        return;
    }

    approvedTrainers.forEach(trainer => {
        const card = document.createElement('div');
        card.className = 'trainer-card';

        let profileImg = '';
        if (trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== '') {
            profileImg = `<img src="${trainer.profileImageBase64}" alt="${trainer.fullName}" class="trainer-profile-img">`;
        }

        const initials = trainer.fullName ? trainer.fullName.split(' ').map(n => n[0]).join('').substring(0, 2) : 'נס';
        const defaultAvatar = `<div class="trainer-avatar" style="display: ${trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== '' ? 'none' : 'flex'};">${initials}</div>`;

        let badges = '<div class="trainer-badges">';
        if (trainer.isOnline) badges += '<span class="badge badge-online">אונליין</span>';
        if (trainer.isForTeens) badges += '<span class="badge badge-teens">נוער</span>';
        if (trainer.isForWomen) badges += '<span class="badge badge-women">נשים בלבד</span>';
        badges += '</div>';

        const averageRating = getTrainerAverageRating(trainer);
        let ratingHtml = '';
        if (averageRating !== null) {
            const ratingCount = trainer.ratingCount || 0;
            const roundedRating = Math.round(averageRating * 10) / 10;
            ratingHtml = `<div class="trainer-rating">
                <div class="star-row-small">${renderStars(averageRating, 5)}</div>
                <div class="rating-text">דירוג: ${roundedRating} (${ratingCount})</div>
            </div>`;
        } else {
            ratingHtml = '<div class="trainer-rating"><div class="rating-text">עדיין אין דירוגים</div></div>';
        }

        let whatsappButton = '';
        let callButton = '';

        if (trainer.phone && trainer.phone.trim() !== '') {
            const phoneDigits = trainer.phone.replace(/\D/g, '');
            if (phoneDigits.length > 0) {
                whatsappButton = `<a href="https://wa.me/${phoneDigits}" target="_blank" class="btn btn-primary">ווטסאפ</a>`;
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
    if (!target) localStorage.removeItem("profileReturnTarget");
    else localStorage.setItem("profileReturnTarget", target);
}

function getProfileReturnTarget() {
    return localStorage.getItem("profileReturnTarget") || "trainers";
}

// Open trainer profile page
function openTrainerProfile(trainerId) {
    if (!trainerId) return;
    localStorage.setItem("selectedTrainerId", String(trainerId));
    window.location.href = ROUTES.trainerProfile;
}

function openTrainerProfileFromAdmin(trainerId) {
    setProfileReturnTarget("admin");
    openTrainerProfile(trainerId);
}

function openTrainerProfileFromList(trainerId) {
    setProfileReturnTarget("trainers");
    openTrainerProfile(trainerId);
}

function goBackFromProfile() {
    const target = getProfileReturnTarget();
    setProfileReturnTarget(null);
    window.location.href = target === "admin" ? ROUTES.admin : ROUTES.trainers;
}

// Trainers filters
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

    const checkboxes = document.querySelectorAll(".filter-type");
    if (checkboxes.length) {
        checkboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                const value = this.value;
                if (this.checked) {
                    if (!currentTypeFilters.includes(value)) currentTypeFilters.push(value);
                } else {
                    currentTypeFilters = currentTypeFilters.filter(v => v !== value);
                }
                renderPublicTrainers();
            });
        });
    }
}

function openTrainerEdit(trainerId) {
    if (!trainerId) return;
    localStorage.setItem("editTrainerId", String(trainerId));
    window.location.href = ROUTES.editTrainer;
}

// Trainer profile page (שאר הפונקציות שלך נשארו כמו שהן — לא שיניתי לוגיקה, רק כתובות)
// ----- (מכאן והלאה הקוד שלך 그대로, רק בלי .html במעברים) -----

function initTrainerProfilePage() {
    const id = localStorage.getItem("selectedTrainerId");
    const container = document.getElementById("trainerProfileContainer");
    if (!container) return;

    if (!id) {
        container.innerHTML = "<p>לא נמצא מאמן להצגה.</p>";
        return;
    }

    let trainer = null;
    let list = getApprovedTrainers && getApprovedTrainers();
    if (Array.isArray(list)) trainer = list.find(t => String(t.id) === String(id));
    if (!trainer) {
        list = getPendingTrainers && getPendingTrainers();
        if (Array.isArray(list)) trainer = list.find(t => String(t.id) === String(id));
    }
    if (!trainer) {
        container.innerHTML = "<p>לא נמצא מאמן להצגה.</p>";
        return;
    }

    let profileImg = '';
    if (trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== '') {
        profileImg = `<img src="${trainer.profileImageBase64}" alt="${trainer.fullName}" class="trainer-profile-large-img">`;
    }

    const initials = trainer.fullName ? trainer.fullName.split(' ').map(n => n[0]).join('').substring(0, 2) : 'נס';
    const defaultAvatar = `<div class="trainer-avatar-large" style="display: ${trainer.profileImageBase64 && trainer.profileImageBase64.trim() !== '' ? 'none' : 'flex'};">${initials}</div>`;

    let badges = '<div class="trainer-profile-badges">';
    if (trainer.isOnline) badges += '<span class="badge badge-online">אונליין</span>';
    if (trainer.isForTeens) badges += '<span class="badge badge-teens">נוער</span>';
    if (trainer.isForWomen) badges += '<span class="badge badge-women">נשים בלבד</span>';
    badges += '</div>';

    const trainingTypesHtml = renderTrainingTypeBadges(trainer);

    let socialLinks = '';
    if (trainer.instagram || trainer.tiktok) {
        socialLinks = '<div class="social-links"><strong>רשתות חברתיות:</strong> ';
        if (trainer.instagram) {
            const instagramUrl = trainer.instagram.startsWith('http')
                ? trainer.instagram
                : `https://instagram.com/${trainer.instagram.replace('@', '')}`;
            socialLinks += `<a href="${instagramUrl}" target="_blank" class="social-link">Instagram</a> `;
        }
        if (trainer.tiktok) {
            const tiktokUrl = trainer.tiktok.startsWith('http')
                ? trainer.tiktok
                : `https://tiktok.com/@${trainer.tiktok.replace('@', '')}`;
            socialLinks += `<a href="${tiktokUrl}" target="_blank" class="social-link">TikTok</a>`;
        }
        socialLinks += '</div>';
    }

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

    const averageRating = getTrainerAverageRating(trainer);
    const ratingCount = trainer.ratingCount || 0;

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

    const starRow = container.querySelector('.star-row');
    if (starRow) {
        const stars = starRow.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                rateTrainer(trainer.id, rating);
            });
        });
    }

    const backBtn = document.getElementById("back-from-profile-btn");
    if (backBtn) {
        const target = getProfileReturnTarget();
        backBtn.textContent = target === "admin" ? "חזור לפאנל המאמנים" : "חזור לרשימת המאמנים";
    }

    renderReviews(trainer.id);

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

            addReviewForTrainer(trainer.id, { rating, text, authorName: name });

            document.getElementById("reviewerName").value = "";
            document.getElementById("reviewRating").value = "";
            document.getElementById("reviewText").value = "";

            if (message) message.textContent = "הביקורת נוספה!";
            renderReviews(trainer.id);
        });
    }
}

function initEditTrainerPage() {
    const id = localStorage.getItem("editTrainerId");
    if (!id) { window.location.href = ROUTES.trainers; return; }

    const trainers = getApprovedTrainers();
    const trainer = trainers.find(t => String(t.id) === String(id));
    if (!trainer) { window.location.href = ROUTES.trainers; return; }

    if (!canEditTrainer(trainer)) { window.location.href = ROUTES.trainers; return; }

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

        trainer.experience = trainer.experienceYears;
        trainer.specialization = trainer.mainSpecialization;
        trainer.price = trainer.pricePerSession;

        const list = getApprovedTrainers();
        const index = list.findIndex(t => String(t.id) === String(id));
        if (index !== -1) {
            list[index] = trainer;
            saveApprovedTrainers(list);
        }

        if (messageBox) {
            messageBox.textContent = "הפרופיל עודכן בהצלחה.";
            messageBox.className = "form-message success";
        }

        setTimeout(function() {
            localStorage.setItem("selectedTrainerId", String(trainer.id));
            window.location.href = ROUTES.trainerProfile;
        }, 1200);
    });
}

function rateTrainer(trainerId, rating) {
    const trainer = getTrainerById(trainerId);
    if (!trainer) return;

    trainer.ratingSum = (trainer.ratingSum || 0) + rating;
    trainer.ratingCount = (trainer.ratingCount || 0) + 1;

    if (updateTrainer(trainer)) initTrainerProfilePage();
}

// Initialize login page
function initLoginPage() {
    const form = document.getElementById("login-form") || document.getElementById("loginForm") || document.querySelector("form#login-form") || document.querySelector("form[id*='login']");
    if (!form) return;

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        const emailInput = form.querySelector("#email");
        const email = emailInput ? emailInput.value.trim().toLowerCase() : "";
        if (!email) { alert("אנא הזן כתובת דוא\"ל."); return; }

        localStorage.setItem("currentUserEmail", email);
        localStorage.removeItem("isAdminLoggedIn");
        window.location.href = ROUTES.home;
    });
}

// Navbar toggle
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

    initTheme && initTheme();
    updateNavbarForUser && updateNavbarForUser();
    initNavbarToggle && initNavbarToggle();

    const themeBtn = document.getElementById("theme-toggle-button");
    if (themeBtn) themeBtn.addEventListener("click", toggleTheme);

    // ✅ Login page
    if (path === ROUTES.login) {
        initLoginPage && initLoginPage();
        return;
    }

    // ✅ All other pages require login
    requireLogin && requireLogin();

    if (path === ROUTES.register) {
        initTrainingTypesSelectorOnRegisterPage && initTrainingTypesSelectorOnRegisterPage();
        return;
    }

    if (path === ROUTES.admin) {
        initAdminPage && initAdminPage();
        return;
    }

    if (path === ROUTES.trainers) {
        initTrainersFilters && initTrainersFilters();
        renderPublicTrainers && renderPublicTrainers();
        return;
    }

    if (path === ROUTES.trainerProfile) {
        initTrainerProfilePage && initTrainerProfilePage();
        return;
    }

    if (path === ROUTES.editTrainer) {
        initEditTrainerPage && initEditTrainerPage();
        return;
    }
});

// ---- Admin functions (נשאר כמו אצלך) ----
function initAdminPage() {
    requireLogin && requireLogin();

    const adminWrapper = document.getElementById('admin-wrapper');
    const adminNotAllowed = document.getElementById('admin-not-allowed');
    const loginForm = document.getElementById('loginForm');
    const adminPanel = document.getElementById('adminPanel');
    const adminLoginForm = document.getElementById('adminLoginForm');

    if (!isAdmin()) {
        if (adminWrapper) adminWrapper.style.display = 'none';
        if (adminNotAllowed) {
            adminNotAllowed.style.display = 'block';
            adminNotAllowed.innerHTML = '<div class="admin-container" style="text-align: center; padding: 40px;"><h2 style="color: #e74c3c; margin-bottom: 20px;">אין לך הרשאה לצפות בעמוד זה.</h2></div>';
        }
        return;
    }

    if (adminWrapper) adminWrapper.style.display = 'block';
    if (adminNotAllowed) adminNotAllowed.style.display = 'none';
    if (!loginForm || !adminPanel) return;

    const isLoggedIn = localStorage.getItem('isAdminLoggedIn') === 'true';

    if (isLoggedIn) {
        loginForm.style.display = 'none';
        adminPanel.style.display = 'block';
        renderPendingTrainers();
        renderApprovedTrainers && renderApprovedTrainers();
    } else {
        loginForm.style.display = 'block';
        adminPanel.style.display = 'none';
    }

    if (adminLoginForm && !adminLoginForm.dataset.listenerAttached) {
        adminLoginForm.dataset.listenerAttached = 'true';
        adminLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('adminPassword').value;
            const ADMIN_PASSWORD = 'fitadmin123';
            const loginError = document.getElementById('loginError');

            if (password === ADMIN_PASSWORD) {
                localStorage.setItem('isAdminLoggedIn', 'true');
                if (loginError) loginError.style.display = 'none';
                initAdminPage();
            } else {
                if (loginError) loginError.style.display = 'block';
                const passwordInput = document.getElementById('adminPassword');
                if (passwordInput) passwordInput.value = '';
            }
        });
    }
}

function logoutAdmin() {
    localStorage.removeItem('isAdminLoggedIn');
    initAdminPage();
}

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

function approveTrainer(trainerId) {
    const pending = getPendingTrainers();
    const approved = getApprovedTrainers();

    const index = pending.findIndex(t => String(t.id) === String(trainerId));
    if (index === -1) return;

    const trainer = pending.splice(index, 1)[0];
    approved.push(trainer);

    savePendingTrainers(pending);
    saveApprovedTrainers(approved);

    renderPendingTrainers();
    renderApprovedTrainers && renderApprovedTrainers();
}

function rejectTrainer(trainerId) {
    const pending = getPendingTrainers();
    const index = pending.findIndex(t => String(t.id) === String(trainerId));
    if (index === -1) return;

    pending.splice(index, 1);
    savePendingTrainers(pending);
    renderPendingTrainers();
}

function deleteTrainer(trainerId) {
    const approved = getApprovedTrainers();
    const index = approved.findIndex(t => String(t.id) === String(trainerId));
    if (index === -1) return;

    approved.splice(index, 1);
    saveApprovedTrainers(approved);

    renderApprovedTrainers && renderApprovedTrainers();
    renderPublicTrainers && renderPublicTrainers();
}

// ✅ FIX: סידרתי את הסוף — אין טקסט/סוגריים שבורים
function contactTrainer(trainerName) {
    alert('יצירת קשר עם המאמן תתווסף בהמשך.');
}
