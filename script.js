/* ------------------- */
/*  Forgot Password Modal */
/* ------------------- */
document.addEventListener("DOMContentLoaded", () => {
    const forgotPasswordLink = document.getElementById("forgotPasswordLink");
    const forgotModal = document.getElementById("forgotModal");
    const closeModal = document.getElementById("closeModal");
    const forgotForm = document.getElementById("forgotForm");

    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener("click", (e) => {
            e.preventDefault();
            forgotModal.style.display = "flex";
        });
    }

    if (closeModal) {
        closeModal.addEventListener("click", () => {
            forgotModal.style.display = "none";
        });
    }

    window.addEventListener("click", (e) => {
        if (e.target === forgotModal) {
            forgotModal.style.display = "none";
        }
    });

    /* ------------------- */
    /* Forgot Password Submit (demo only - no backend yet) */
    /* ------------------- */
    if (forgotForm) {
        forgotForm.addEventListener("submit", (e) => {
            e.preventDefault();
            alert("Password reset link sent to your email!");
            forgotModal.style.display = "none";
        });
    }

    // Login, Register and Logout forms submit normally to their PHP pages
    // (index.php, register.php, logout.php) - no JS interception needed.
});
