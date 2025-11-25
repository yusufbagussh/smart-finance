import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

// --- TAMBAHKAN INI: DEFINISI KOMPONEN GLOBAL ---
document.addEventListener("alpine:init", () => {
    Alpine.data("currencyInput", (initialValue = 0) => ({
        rawValue: initialValue,
        formattedValue: "",

        init() {
            // Format nilai awal saat komponen dimuat
            if (this.rawValue) {
                this.formattedValue = this.format(this.rawValue);
            }
        },

        format(value) {
            // Hapus karakter selain angka
            let number = String(value).replace(/[^0-9]/g, "");
            if (!number) return "";
            // Format ke Rupiah (tanpa desimal, pakai titik)
            return new Intl.NumberFormat("id-ID").format(number);
        },

        handleInput(event) {
            // 1. Ambil input user
            let inputVal = event.target.value;

            // 2. Bersihkan titik untuk dapat angka murni (untuk server)
            // Kita ganti semua titik dengan string kosong
            this.rawValue = inputVal.replace(/\./g, "");

            // 3. Format ulang untuk tampilan (untuk user)
            this.formattedValue = this.format(this.rawValue);
        },
    }));
});
// -----------------------------------------------

Alpine.start();

var themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
var themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");
var themeToggleBtn = document.getElementById("theme-toggle"); // Desktop button

var themeToggleDarkIconMobile = document.getElementById(
    "theme-toggle-dark-icon-mobile"
);
var themeToggleLightIconMobile = document.getElementById(
    "theme-toggle-light-icon-mobile"
);
var themeToggleBtnMobile = document.getElementById("theme-toggle-mobile"); // Mobile button

// --- Function to UPDATE icons on BOTH buttons ---
function updateThemeIcons() {
    let isDarkMode;
    // Check localStorage first
    if (localStorage.getItem("color-theme") === "dark") {
        isDarkMode = true;
    } else if (localStorage.getItem("color-theme") === "light") {
        isDarkMode = false;
    } else {
        // If no preference, check system preference
        isDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches;
    }

    // Update Desktop Icons (check if elements exist)
    if (themeToggleDarkIcon && themeToggleLightIcon) {
        themeToggleDarkIcon.classList.toggle("hidden", !isDarkMode);
        themeToggleLightIcon.classList.toggle("hidden", isDarkMode);
    }
    // Update Mobile Icons (check if elements exist)
    if (themeToggleDarkIconMobile && themeToggleLightIconMobile) {
        themeToggleDarkIconMobile.classList.toggle("hidden", !isDarkMode);
        themeToggleLightIconMobile.classList.toggle("hidden", isDarkMode);
    }
}

// --- Function to HANDLE click on EITHER button ---
function handleThemeToggle() {
    let isDarkModeNow;
    // Check current state based on localStorage or class
    if (localStorage.getItem("color-theme")) {
        if (localStorage.getItem("color-theme") === "light") {
            document.documentElement.classList.add("dark");
            localStorage.setItem("color-theme", "dark");
            isDarkModeNow = true;
        } else {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("color-theme", "light");
            isDarkModeNow = false;
        }
    } else {
        // No localStorage preference yet
        if (document.documentElement.classList.contains("dark")) {
            // Check current class
            document.documentElement.classList.remove("dark");
            localStorage.setItem("color-theme", "light");
            isDarkModeNow = false;
        } else {
            document.documentElement.classList.add("dark");
            localStorage.setItem("color-theme", "dark");
            isDarkModeNow = true;
        }
    }
    // Update icons after toggling
    updateThemeIcons();
}

// --- Initial state check and icon update on page load ---
// Apply dark mode immediately if needed
if (
    localStorage.getItem("color-theme") === "dark" ||
    (!("color-theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
) {
    document.documentElement.classList.add("dark");
} else {
    document.documentElement.classList.remove("dark");
}
// Update icons based on the initial state
updateThemeIcons();

// --- Add event listeners to BOTH buttons (if they exist) ---
if (themeToggleBtn) {
    themeToggleBtn.addEventListener("click", handleThemeToggle);
}
if (themeToggleBtnMobile) {
    themeToggleBtnMobile.addEventListener("click", handleThemeToggle);
}

// (Optional) Listen for changes in other tabs/windows
window.addEventListener("storage", (event) => {
    if (event.key === "color-theme") {
        // Re-apply class and update icons if storage changes
        if (event.newValue === "dark") {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
        updateThemeIcons();
    }
});
