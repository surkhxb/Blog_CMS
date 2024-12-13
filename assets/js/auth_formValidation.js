document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");

    form.addEventListener("submit", (e) => {
        let errorMessage = "";

        // Email Validation
        const emailRegex = /^[^\s@]+@(gmail\.com|example\.com)$/;
        if (!emailRegex.test(emailInput.value)) {
            errorMessage += "Email must be in the format xyz@gmail.com or xyz@example.com.\n";
        }

        // Password Validation
        const password = passwordInput.value;
        const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!passwordRegex.test(password)) {
            errorMessage += "Password must be at least 8 characters long and include alphabets, at least one number, and one special character.\n";
        }

        // If there's an error, prevent form submission and alert the user
        if (errorMessage) {
            e.preventDefault();
            alert(errorMessage);
        }
    });
});
