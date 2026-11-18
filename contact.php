<?php
// Header include karein
include_once 'includes/header.php';

$success_msg = '';
$error_msg = '';

// ---- Handle Contact Form Submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'])) {
    
    // Data sanitization
    $name = addslashes(trim($_POST['name'] ?? ''));
    $company = addslashes(trim($_POST['company'] ?? ''));
    $email = addslashes(trim($_POST['email'] ?? ''));
    $phone = addslashes(trim($_POST['phone'] ?? ''));
    $subject = addslashes(trim($_POST['subject'] ?? 'Bulk Enquiry'));
    
    $raw_message = trim($_POST['message'] ?? '');
    $quantity = trim($_POST['quantity'] ?? 'Not specified');
    $destination = trim($_POST['destination'] ?? 'Not specified');
    
    // Combine fields for the message column
    $full_message = "Company: $company\n\nRequirements:\n$raw_message\n\nOrder Size: $quantity\nDelivery City/Port: $destination";
    $full_message = addslashes($full_message);

    // Assuming your custom q() function can execute INSERT queries.
    // Replace with your exact connection method if q() is SELECT only.
    $insert = q("INSERT INTO inquiries (name, email, phone, subject, message, status) 
                 VALUES ('$name', '$email', '$phone', '$subject', '$full_message', 0)");
    
    // Setting success message
    $success_msg = "Thank you! Your enquiry has been sent successfully. Our trade desk will reply with a rate shortly.";
}

// ---- Fetch Dynamic Contact Details ----
$C = q("SELECT * FROM contacts ORDER BY id ASC LIMIT 1");
$c_address = txt($C[0]['address'] ?? '');
$c_phone = txt($C[0]['phone'] ?? '');
$c_email = txt($C[0]['contact_email'] ?? $C[0]['email'] ?? '');
$c_hours = txt($C[0]['working_hours'] ?? '');
?>
<main id="main">

    <!-- Inner Page Hero Section -->
    <section class="hero" style="padding: 60px 0; text-align: center;">
        <div class="wrap">
            <div class="hero-txt">
                <p class="greet"><span class="hi">Contact</span> Our Trade Desk</p>
                <h1>Get Today’s Bulk Rate</h1>
                <p class="lead">Reach out to us for product rates, samples, or any trade inquiries.</p>
            </div>
        </div>
    </section>

    <!-- Contact Info & Form Section (Reusing .rg layout from index.php) -->
    <section class="sec rq" id="rfq">
        <div class="wrap rg">
            
            <!-- Left Side: Dynamic Contact Details -->
            <div>
                <div class="sh">
                    <h2>Reach out to us</h2><span class="hi">We're here to help</span>
                    <p>Tell us the product, grade, quantity and where it should go. We will reply within business hours.</p>
                </div>
                
                <ul class="pts" style="margin-top: 30px;">
                    <?php if($c_address): ?>
                        <li><b>Registered Address</b><?= e($c_address) ?></li>
                    <?php endif; ?>
                    
                    <?php if($c_phone): ?>
                        <li><b>WhatsApp / Call</b><a href="tel:<?= e(str_replace(' ', '', $c_phone)) ?>" style="color: inherit; text-decoration: none;"><?= e($c_phone) ?></a></li>
                    <?php endif; ?>
                    
                    <?php if($c_email): ?>
                        <li><b>Trade Desk Email</b><a href="mailto:<?= e($c_email) ?>" style="color: inherit; text-decoration: none;"><?= e($c_email) ?></a></li>
                    <?php endif; ?>
                    
                    <?php if($c_hours): ?>
                        <li><b>Working Hours</b><?= e($c_hours) ?></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Right Side: Contact Form -->
            <form class="fm" action="contact.php" method="POST">
                <h3>Submit an Enquiry</h3>
                
                <!-- Display Success Message if submitted -->
                <?php if($success_msg): ?>
                    <div style="background: #e6f4ea; color: #1e4620; padding: 15px; margin-bottom: 20px; border-radius: 5px; font-size: 0.95rem;">
                        <b>&#10003;</b> <?= $success_msg ?>
                    </div>
                <?php endif; ?>

                <label>Contact person *<input name="name" placeholder="Rakesh Gupta" autocomplete="name" required></label>
                <label>Company *<input name="company" placeholder="Gupta Dry Fruit Traders" autocomplete="organization" required></label>
                <label>Email *<input type="email" name="email" placeholder="you@company.com" autocomplete="email" required></label>
                <label>WhatsApp / phone *<input type="tel" name="phone" placeholder="+91 98765 43210" autocomplete="tel" required></label>
                <label>Product *<input id="pi" name="subject" placeholder="Almonds, red chilli, tomatoes..." required></label>
                
                <label>Order size<select name="quantity">
                        <option>Trial order (100 to 500 kg)</option>
                        <option>1 to 5 MT</option>
                        <option>Full truck load (about 10 MT)</option>
                        <option>Container load (export)</option>
                        <option>Monthly repeat requirement</option>
                    </select></label>
                    
                <label class="w">Delivery city or port<input name="destination" placeholder="Mumbai / Jebel Ali, UAE"></label>
                <label class="w">Specifications *<textarea name="message" rows="3" placeholder="Grade, size, pack (10 / 25 / 50 kg), rate basis (ex-Delhi, FOB, CIF)..." required></textarea></label>
                
                <div class="w"><button type="submit" class="btn">Request rate and sample</button></div>
            </form>
            
        </div>
    </section>

</main>
<?php include 'includes/footer.php'; ?>