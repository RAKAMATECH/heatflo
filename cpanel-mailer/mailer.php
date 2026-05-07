<?php
// Heatflo lead-notification relay.
// Receives an authenticated HTTPS POST from the Render-hosted Laravel API
// and re-sends the message via the local cPanel mail server.
//
// Before uploading to public_html on the cPanel host, edit the two
// constants below. Do not commit real values to source control.

const RELAY_SECRET = 'PASTE_THE_RANDOM_SECRET_HERE';
const ALLOWED_RECIPIENTS = ['info@heatflo.co.zw'];
const FROM_ADDRESS = 'info@heatflo.co.zw';
const FROM_NAME = 'Heatflo Website';
const MAX_BODY_BYTES = 65536;

header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method not allowed']);
    exit;
}

$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (stripos($auth, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'missing bearer token']);
    exit;
}
$provided = substr($auth, 7);
if (RELAY_SECRET === 'PASTE_THE_RANDOM_SECRET_HERE' || ! hash_equals(RELAY_SECRET, $provided)) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'invalid bearer token']);
    exit;
}

$raw = file_get_contents('php://input', false, null, 0, MAX_BODY_BYTES + 1);
if ($raw === false || strlen($raw) > MAX_BODY_BYTES) {
    http_response_code(413);
    echo json_encode(['ok' => false, 'error' => 'body too large']);
    exit;
}

$payload = json_decode($raw, true);
if (! is_array($payload)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid json']);
    exit;
}

$to = trim((string) ($payload['to'] ?? ''));
$subject = trim((string) ($payload['subject'] ?? ''));
$html = (string) ($payload['html'] ?? '');

if ($to === '' || $subject === '' || $html === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'to, subject, html are required']);
    exit;
}
if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'invalid recipient']);
    exit;
}
if (! in_array(strtolower($to), array_map('strtolower', ALLOWED_RECIPIENTS), true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'recipient not allowed']);
    exit;
}
if (strlen($subject) > 255) {
    $subject = substr($subject, 0, 255);
}

$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
$fromHeader = sprintf('%s <%s>', addslashes(FROM_NAME), FROM_ADDRESS);

$headers = implode("\r\n", [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'From: ' . $fromHeader,
    'Reply-To: ' . FROM_ADDRESS,
    'X-Mailer: heatflo-relay',
]);

$ok = @mail($to, $encodedSubject, $html, $headers, '-f' . FROM_ADDRESS);

if (! $ok) {
    $err = error_get_last();
    http_response_code(502);
    echo json_encode([
        'ok' => false,
        'error' => 'mail() returned false',
        'detail' => $err['message'] ?? null,
    ]);
    exit;
}

http_response_code(200);
echo json_encode(['ok' => true]);
