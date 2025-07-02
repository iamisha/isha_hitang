<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Valid email address is required']);
    exit;
}

$email = $input['email'];
$cvPath = 'CV/isha_hitang.pdf';

// Check if CV file exists
if (!file_exists($cvPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'CV file not found']);
    exit;
}

// Email configuration
$to = $email;
$subject = 'Isha Hitang - Software Engineer CV';
$message = "
Dear Recruiter,

Thank you for your interest in my profile. Please find attached my CV.

I am Isha Hitang, a passionate Software Engineering student at Nepal College of Information Technology (NCIT) with:
- GPA: 3.84/4.0 (Full Scholarship)
- Strong skills in C/C++, Java, Python, AI/ML
- Winner of AI4GROWTH Challenge
- Multiple hackathon awards
- Experience in software development and leadership

I would be delighted to discuss how my skills and enthusiasm can contribute to your organization.

Best regards,
Isha Hitang

Contact Information:
- Email: ishahitang09@gmail.com
- Website: https://ishahitang.com.np
- LinkedIn: https://www.linkedin.com/in/isha-hitang-bb3673252/
- GitHub: https://github.com/iamisha
";

// Create email headers
$headers = "From: Isha Hitang <ishahitang09@gmail.com>\r\n";
$headers .= "Reply-To: ishahitang09@gmail.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";

// Generate boundary
$boundary = md5(time());
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

// Email body with attachment
$emailBody = "--$boundary\r\n";
$emailBody .= "Content-Type: text/plain; charset=UTF-8\r\n";
$emailBody .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$emailBody .= $message . "\r\n";

// Attach CV
$fileContent = chunk_split(base64_encode(file_get_contents($cvPath)));
$emailBody .= "--$boundary\r\n";
$emailBody .= "Content-Type: application/pdf; name=\"Isha_Hitang_CV.pdf\"\r\n";
$emailBody .= "Content-Transfer-Encoding: base64\r\n";
$emailBody .= "Content-Disposition: attachment; filename=\"Isha_Hitang_CV.pdf\"\r\n\r\n";
$emailBody .= $fileContent . "\r\n";
$emailBody .= "--$boundary--";

// Send email
if (mail($to, $subject, $emailBody, $headers)) {
    // Log the email for tracking (optional)
    $logEntry = date('Y-m-d H:i:s') . " - CV sent to: $email\n";
    file_put_contents('cv_downloads.log', $logEntry, FILE_APPEND);
    
    echo json_encode(['success' => true, 'message' => 'CV sent successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send email']);
}
?>
