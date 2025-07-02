
(function () {
    [...document.querySelectorAll(".control")].forEach(button => {
        button.addEventListener("click", function() {
            document.querySelector(".active-btn").classList.remove("active-btn");
            this.classList.add("active-btn");
            document.querySelector(".active").classList.remove("active");
            document.getElementById(button.dataset.id).classList.add("active");
        })
    });
    document.querySelector(".theme-btn").addEventListener("click", () => {
        document.body.classList.toggle("light-mode");
    })
})();

// CV Download Modal functionality
(function() {
    const modal = document.getElementById('cvModal');
    const cvButtons = document.querySelectorAll('#cvDownloadBtn, #cvDownloadBtn2');
    const closeBtn = document.querySelector('.cv-close');
    const form = document.getElementById('cvEmailForm');
    const emailInput = document.getElementById('cvEmail');
    const message = document.getElementById('cvMessage');

    // Open modal when CV download buttons are clicked
    cvButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.style.display = 'block';
            emailInput.focus();
        });
    });

    // Close modal when X is clicked
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        clearForm();
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            clearForm();
        }
    });

    // Handle form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = emailInput.value.trim();
        if (!email) {
            showMessage('Please enter a valid email address.', 'error');
            return;
        }

        // Disable form while processing
        form.querySelector('.cv-submit-btn').disabled = true;
        form.querySelector('.btn-text').textContent = 'Sending...';

        try {
            // Send email via backend
            const response = await fetch('send_cv.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showMessage('CV sent successfully! Please check your email.', 'success');
                
                // Close modal after 2 seconds
                setTimeout(() => {
                    modal.style.display = 'none';
                    clearForm();
                }, 2000);
            } else {
                throw new Error(result.error || 'Failed to send CV');
            }

        } catch (error) {
            console.error('Error sending CV:', error);
            showMessage('Sorry, there was an error sending the CV. Please try again.', 'error');
        } finally {
            // Re-enable form
            form.querySelector('.cv-submit-btn').disabled = false;
            form.querySelector('.btn-text').textContent = 'Send CV';
        }
    });

    function showMessage(text, type) {
        message.textContent = text;
        message.className = `cv-message ${type}`;
        message.style.display = 'block';
    }

    function clearForm() {
        emailInput.value = '';
        message.style.display = 'none';
        form.querySelector('.cv-submit-btn').disabled = false;
        form.querySelector('.btn-text').textContent = 'Send CV';
    }

    // Handle escape key to close modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            modal.style.display = 'none';
            clearForm();
        }
    });
})();