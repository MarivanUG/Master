<?php
// send_mail.php
header('Content-Type: application/json');

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (empty($_POST["name"]) || empty($_POST["phone"]) || empty($_POST["message"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Please fill in all required fields."]);
        exit;
    }

    $name = sanitize_input($_POST["name"]);
    $phone = sanitize_input($_POST["phone"]);
    $email = !empty($_POST["email"]) ? sanitize_input($_POST["email"]) : "Not provided";
    $interest = !empty($_POST["interest"]) ? sanitize_input($_POST["interest"]) : "General Inquiry";
    $message = sanitize_input($_POST["message"]);

    // Recipient email address
    $to = "masterindustriesug@yahoo.com"; 

    // Email subject
    $subject = "Website Inquiry: $interest - $name";

    // Email body
    $body = "You have received a new message from the website contact form.\n\n";
    $body .= "Name: $name\n";
    $body .= "Phone: $phone\n";
    $body .= "Email: $email\n";
    $body .= "Interested In: $interest\n\n";
    $body .= "Message:\n$message\n";

    // Headers
    // Use a generic server email if a valid sender email is not provided to prevent mail rejection
    $senderEmail = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : "noreply@" . $_SERVER['HTTP_HOST'];
    $headers = "From: $senderEmail\r\n";
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $headers .= "Reply-To: $email\r\n";
    }
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Message sent successfully! We will get back to you soon."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to send the message due to a server error. Please try again later."]);
    }
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
}
?>
