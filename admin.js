document.addEventListener("DOMContentLoaded", () => {
    const forgotPasswordLink = document.getElementById("forgotPasswordLink");
    const forgotModal = document.getElementById("forgotModal");
    const closeModal = document.getElementById("closeModal");
    const forgotForm = document.getElementById("forgotForm");

    /* ------------------- */
    /* Forgot Password Modal */
    /* ------------------- */
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

    // Admin login and logout forms submit normally to
    // admin_login_process.php / admin_logout.php - no JS interception needed.

    /* ------------------------------- */
    /* Ask for a rejection reason      */
    /* before submitting a Reject form */
    /* ------------------------------- */
    document.querySelectorAll(".reject-form").forEach((form) => {
        form.addEventListener("submit", (e) => {
            const reasonInput = form.querySelector(".rejection-reason-input");
            if (reasonInput) {
                const reason = prompt("Please enter a reason for rejecting this request:");
                if (reason === null) {
                    // Admin cancelled the prompt - do not submit
                    e.preventDefault();
                    return;
                }
                reasonInput.value = reason.trim();
            }
        });
    });
});
