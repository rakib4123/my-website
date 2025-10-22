// This is a very simple example. Real-world validation is more complex.
document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.querySelector('form');
    if (contactForm) {
        contactForm.addEventListener('submit', (event) => {
            const name = document.getElementById('name').value;
            const message = document.getElementById('message').value;

            if (name.trim() === '' || message.trim() === '') {
                alert('Please fill out all fields.');
                event.preventDefault(); // Stop the form from submitting
            }
        });
    }
});
