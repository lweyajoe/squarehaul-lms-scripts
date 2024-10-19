<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("contact-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create FormData object with form data

        // Send AJAX request
        fetch("contact-submit.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Thank you for your submission!");
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
});
</script>