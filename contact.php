<?php include 'header.php'; 
$feedback_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $feedback_message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">Thank you, ' . $name . '! Your message has been received.</div>';
}
?>
<div class="container mx-auto px-6 py-16">
    <div class="text-center mb-12 animate-on-scroll">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900">Get In Touch</h1>
        <p class="mt-4 text-lg text-gray-600">Have a question or feedback? We'd love to hear from you.</p>
    </div>
    <div class="max-w-xl mx-auto card p-8 animate-on-scroll">
        <?php if($feedback_message) echo $feedback_message; ?>
        <form action="contact.php" method="POST" class="space-y-6 mt-6">
            <div><label for="name" class="block text-sm font-medium text-gray-700">Full Name</label><input type="text" id="name" name="name" class="mt-1 form-input" required></div>
            <div><label for="email" class="block text-sm font-medium text-gray-700">Email Address</label><input type="email" id="email" name="email" class="mt-1 form-input" required></div>
            <div><label for="message" class="block text-sm font-medium text-gray-700">Message</label><textarea id="message" name="message" rows="5" class="mt-1 form-input" required></textarea></div>
            <div><button type="submit" class="w-full btn btn-primary">Send Message</button></div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>