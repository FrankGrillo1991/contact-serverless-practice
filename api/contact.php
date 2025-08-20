<?php
// api/contact.php
// (A) Optional CORS (same-origin? you can remove these)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: https://your-site.example'); // or "*"
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, Authorization');
  exit;
}
header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: https://your-site.example'); // if needed

// (B) Method guard
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
  exit;
}

// (C) Parse JSON body (fallback to form-encoded)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !is_array($data)) {
  // if the form posts application/x-www-form-urlencoded
  $data = $_POST;
}

// (D) Validate input
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$message = trim($data['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
  exit;
}

// (E) Send email via an API (Resend example)
$apiKey = getenv('RESEND_API_KEY'); // set in Vercel -> Settings -> Environment Variables
if ($apiKey) {
  $payload = [
    'from' => 'Contact <contact@yourdomain.com>',
    'to' => ['you@yourdomain.com'],
    'subject' => "New contact from $name",
    'text' => "Name: $name\nEmail: $email\n\n$message"
  ];

  $ch = curl_init('https://api.resend.com/emails');
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json',
  ]);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $resp = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err  = curl_error($ch);
  curl_close($ch);

  if ($err || $http >= 400) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Email send failed', 'details' => $resp]);
    exit;
  }
}

// (F) Success
echo json_encode(['ok' => true]);
