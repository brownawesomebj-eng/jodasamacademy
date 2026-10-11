<?php include 'config.php';
$msg_success = "";
if($_SERVER['REQUEST_METHOD']=='POST'){
  $name = $conn->real_escape_string($_POST['name']);
  $email = $conn->real_escape_string($_POST['email']);
  $message = $conn->real_escape_string($_POST['message']);
  $conn->query("INSERT INTO messages (name,email,message) VALUES ('$name','$email','$message')");
  $msg_success = "Thank you $name! Your message has been saved in MySQL database.";
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact - JODASAM</title><link rel="stylesheet" href="style.css"></head>
<body>
<header><div class="logo">JODASAM <span>Academy</span></div>
<nav><a href="index.php">Home</a><a href="about.php">About</a><a href="contact.php" class="active">Contact</a><a href="portal.php">Portal</a></nav></header>
<div class="page-header"><h1>Contact Us</h1><p>Messages are stored in MySQL messages table</p></div>
<section><div class="grid">
<div class="card"><h3>Address</h3><p>Gbagalape, Nyanya, Abuja<br>0813 410 7108<br>samuelogbake@icloud.com</p></div>
<div class="form-box">
<h3 style="text-align:center;color:var(--blue)">Send Message (PHP)</h3>
<?php if($msg_success) echo "<p style='color:green;text-align:center'>$msg_success</p>"; ?>
<form method="POST">
<input type="text" name="name" placeholder="Your Name" required>
<input type="email" name="email" placeholder="Your Email" required>
<textarea name="message" rows="4" placeholder="Your Message" required></textarea>
<button class="btn" style="width:100%">Send to Database</button>
</form>
</div>
</div></section>
<footer>&copy; 2026 JODASAM Academy</footer>
</body></html>