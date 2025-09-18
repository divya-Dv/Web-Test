<!--?php

// Capture form data safely
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$city = isset($_POST['city']) ? $_POST['city'] : '';
$messageOne = isset($_POST['message']) ? $_POST['message'] : '';


// Receiver email
$to = "prashanth@grank.co.in";
$subject = "Enquiry for VLHS";

// Create HTML email body
$message = "
<html--><html><head>
<title>New Enquiry</title>
</head>
<body>
<h2>New Enquiry Details</h2>
<table border="1" cellpadding="5" cellspacing="0">
<tbody><tr>
<th align="left">Name</th>
<td>$name</td>
</tr>
<tr>
<th align="left">Email</th>
<td>$email</td>
</tr>
<tr>
<th align="left">Phone</th>
<td>$phone</td>
</tr>
<tr>
<th align="left">Product</th>
<td>$product</td>
</tr>
<tr>
<th align="left">Message</th>
<td>$messageOne</td>
</tr>
</tbody></table>


";

// Headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: noreply@vlhsglove.com' . "\r\n"; // &lt;&lt;&lt; Important: Must be your domain
$headers .= 'Reply-To: ' . $email . "\r\n";
$headers .= 'cc:punnaaprashanth@gmail.com' . "\r\n";

// Send Email and Redirect
if (mail($to, $subject, $message, $headers)) {
    header("Location: thankyou.php");
    exit();
} else {
    echo "Sorry, there was a problem sending your enquiry. Please try again later.";
}

?&gt;
</body></html>