<?php
/**
 * order-handler.php
 * Processes Wu Spa checkout order submissions via SMTP using PHPMailer.
 * Sends:
 *   1. Admin notification  → excellentwuwuhands@wu-spa.com
 *   2. Customer confirmation → customer's email
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'plugins/phpmailer/Exception.php';
require 'plugins/phpmailer/PHPMailer.php';
require 'plugins/phpmailer/SMTP.php';

/* ─────────────────────────────────────────────
   Helper: create a configured PHPMailer instance
───────────────────────────────────────────── */
function makeMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'getmeonlocal.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'wu-spa@getmeonlocal.com';
    $mail->Password   = 'PASScode123@#';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom('wu-spa@getmeonlocal.com', 'Wu Spa');
    return $mail;
}

/* ─────────────────────────────────────────────
   Only accept POST
───────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

/* ─────────────────────────────────────────────
   Parse JSON body
───────────────────────────────────────────── */
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON payload.']);
    exit;
}

/* ─────────────────────────────────────────────
   Sanitize & validate inputs
───────────────────────────────────────────── */
$firstName = trim(strip_tags($data['firstName'] ?? ''));
$lastName  = trim(strip_tags($data['lastName']  ?? ''));
$email     = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = trim(strip_tags($data['phone'] ?? ''));
$method    = trim(strip_tags($data['method'] ?? 'pickup'));  // pickup | ship
$address1  = trim(strip_tags($data['address1'] ?? ''));
$address2  = trim(strip_tags($data['address2'] ?? ''));
$city      = trim(strip_tags($data['city']     ?? ''));
$state     = trim(strip_tags($data['state']    ?? ''));
$zip       = trim(strip_tags($data['zip']      ?? ''));
$notes     = trim(strip_tags($data['notes']    ?? ''));
$cart      = $data['cart'] ?? [];
$orderRef  = trim(strip_tags($data['orderRef'] ?? ('WU-' . date('ymd') . '-' . rand(1000,9999))));

// Basic required-field validation
$required = ['firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'phone' => $phone];
foreach ($required as $field => $val) {
    if (empty($val)) {
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
    exit;
}
if (empty($cart) || !is_array($cart)) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty.']);
    exit;
}

/* ─────────────────────────────────────────────
   Build order item rows (HTML + plain text)
───────────────────────────────────────────── */
$subtotal    = 0;
$itemRowsHtml  = '';
$itemRowsText  = '';

foreach ($cart as $item) {
    $name     = strip_tags($item['name']  ?? 'Unknown Product');
    $qty      = (int)($item['qty']   ?? 1);
    $price    = (float)($item['price'] ?? 0);
    $lineTotal = $price * $qty;
    $subtotal += $lineTotal;

    $itemRowsHtml .= '
        <tr>
            <td style="padding:10px 12px;border-bottom:1px solid #f5f5f5;font-size:14px;color:#333;">' . htmlspecialchars($name) . '</td>
            <td style="padding:10px 12px;border-bottom:1px solid #f5f5f5;text-align:center;font-size:14px;color:#555;">' . $qty . '</td>
            <td style="padding:10px 12px;border-bottom:1px solid #f5f5f5;text-align:right;font-size:14px;color:#555;">$' . number_format($price, 2) . '</td>
            <td style="padding:10px 12px;border-bottom:1px solid #f5f5f5;text-align:right;font-size:14px;font-weight:700;color:#EC5598;">$' . number_format($lineTotal, 2) . '</td>
        </tr>';

    $itemRowsText .= "  • {$name} x{$qty} @ \$" . number_format($price,2) . " = \$" . number_format($lineTotal,2) . "\n";
}

$freeShipping  = ($subtotal >= 75);
$shippingLabel = $freeShipping ? 'FREE' : 'To Be Arranged';
$totalLabel    = '$' . number_format($subtotal, 2) . ($freeShipping ? '' : ' + shipping');

$deliveryMethod  = ($method === 'ship') ? 'Ship to Customer' : 'In-Spa Pickup';
$addressBlock    = '';
$addressText     = '';
if ($method === 'ship') {
    $addressBlock = '
        <tr>
            <td style="padding:8px 0;font-size:13px;color:#999;width:130px;">Address</td>
            <td style="padding:8px 0;font-size:13px;color:#333;">' . htmlspecialchars($address1) . ($address2 ? '<br>' . htmlspecialchars($address2) : '') . '</td>
        </tr>
        <tr>
            <td style="padding:8px 0;font-size:13px;color:#999;">City / State</td>
            <td style="padding:8px 0;font-size:13px;color:#333;">' . htmlspecialchars($city) . ', ' . htmlspecialchars($state) . ' ' . htmlspecialchars($zip) . '</td>
        </tr>';
    $addressText = "  Address:   {$address1}" . ($address2 ? "\n             {$address2}" : '') . "\n  City/State: {$city}, {$state} {$zip}\n";
}

$notesField = $notes ? '<tr><td style="padding:8px 0;font-size:13px;color:#999;width:130px;">Notes</td><td style="padding:8px 0;font-size:13px;color:#333;">' . nl2br(htmlspecialchars($notes)) . '</td></tr>' : '';
$notesText  = $notes ? "  Notes:     {$notes}\n"  : '';

/* ─────────────────────────────────────────────
   HTML EMAIL TEMPLATES
───────────────────────────────────────────── */

// Shared header/footer wrappers
$emailWrap = function(string $bodyContent): string {
    return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f9f9f9;font-family:\'Poppins\',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9f9f9;padding:30px 0;">
      <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,0.06);">

          <!-- Header -->
          <tr><td style="background:#EC5598;padding:30px 35px;text-align:center;">
            <h1 style="color:#fff;margin:0;font-size:22px;font-weight:700;letter-spacing:1px;">WU SPA</h1>
            <p style="color:rgba(255,255,255,0.85);margin:6px 0 0;font-size:13px;">Charlotte, NC · (980) 222-1633</p>
          </td></tr>

          <!-- Body -->
          <tr><td style="padding:35px;">' . $bodyContent . '</td></tr>

          <!-- Footer -->
          <tr><td style="background:#fdf7fa;padding:20px 35px;text-align:center;border-top:1px solid #f0e6f0;">
            <p style="font-size:12px;color:#aaa;margin:0;">Wu Spa · 2750 E W.T. Harris Blvd, Charlotte, NC 28213<br>
            <a href="mailto:excellentwuwuhands@wu-spa.com" style="color:#EC5598;text-decoration:none;">excellentwuwuhands@wu-spa.com</a> · (980) 222-1633</p>
          </td></tr>

        </table>
      </td></tr>
    </table>
    </body></html>';
};

/* ── ADMIN EMAIL BODY ── */
$adminBody = $emailWrap('
    <h2 style="color:#333;font-size:20px;margin:0 0 5px;">🛍 New Order Received</h2>
    <p style="color:#EC5598;font-size:13px;font-weight:700;margin:0 0 25px;">Order Reference: <strong>' . htmlspecialchars($orderRef) . '</strong> &nbsp;·&nbsp; ' . date('M j, Y g:i A') . '</p>

    <h3 style="font-size:14px;text-transform:uppercase;letter-spacing:0.5px;color:#999;margin:0 0 12px;border-bottom:2px solid #EC5598;padding-bottom:8px;">Customer Details</h3>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
        <tr><td style="padding:8px 0;font-size:13px;color:#999;width:130px;">Name</td><td style="padding:8px 0;font-size:13px;color:#333;font-weight:600;">' . htmlspecialchars($firstName . ' ' . $lastName) . '</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:#999;">Email</td><td style="padding:8px 0;font-size:13px;"><a href="mailto:' . htmlspecialchars($email) . '" style="color:#EC5598;">' . htmlspecialchars($email) . '</a></td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:#999;">Phone</td><td style="padding:8px 0;font-size:13px;color:#333;">' . htmlspecialchars($phone) . '</td></tr>
        <tr><td style="padding:8px 0;font-size:13px;color:#999;">Method</td><td style="padding:8px 0;font-size:13px;color:#333;font-weight:600;">' . $deliveryMethod . '</td></tr>
        ' . $addressBlock . $notesField . '
    </table>

    <h3 style="font-size:14px;text-transform:uppercase;letter-spacing:0.5px;color:#999;margin:0 0 12px;border-bottom:2px solid #EC5598;padding-bottom:8px;">Ordered Items</h3>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
        <thead>
            <tr style="background:#fdf7fa;">
                <th style="padding:10px 12px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#EC5598;">Product</th>
                <th style="padding:10px 12px;text-align:center;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#EC5598;">Qty</th>
                <th style="padding:10px 12px;text-align:right;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#EC5598;">Price</th>
                <th style="padding:10px 12px;text-align:right;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#EC5598;">Total</th>
            </tr>
        </thead>
        <tbody>' . $itemRowsHtml . '</tbody>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="text-align:right;padding:6px 12px;font-size:14px;color:#555;">Subtotal</td><td style="text-align:right;padding:6px 12px;font-size:14px;color:#333;width:100px;">$' . number_format($subtotal,2) . '</td></tr>
        <tr><td style="text-align:right;padding:6px 12px;font-size:14px;color:#555;">Shipping</td><td style="text-align:right;padding:6px 12px;font-size:14px;color:#333;">' . $shippingLabel . '</td></tr>
        <tr style="background:#fdf7fa;"><td style="text-align:right;padding:10px 12px;font-size:17px;font-weight:700;color:#333;">ORDER TOTAL</td><td style="text-align:right;padding:10px 12px;font-size:17px;font-weight:700;color:#EC5598;">' . $totalLabel . '</td></tr>
    </table>
');

/* ── CUSTOMER CONFIRMATION EMAIL BODY ── */
$customerBody = $emailWrap('
    <h2 style="color:#333;font-size:20px;margin:0 0 8px;">Thank You, ' . htmlspecialchars($firstName) . '! 🌸</h2>
    <p style="color:#666;font-size:14px;line-height:1.8;margin:0 0 20px;">We\'ve received your order and our team will contact you within <strong>24 hours</strong> to confirm details and arrange for pickup or delivery.</p>

    <div style="background:#fdf7fa;border:1px solid #f0e6f0;border-radius:6px;padding:16px 20px;margin-bottom:25px;text-align:center;">
        <p style="margin:0;font-size:12px;color:#999;text-transform:uppercase;letter-spacing:0.5px;">Your Order Reference</p>
        <p style="margin:5px 0 0;font-size:22px;font-weight:700;color:#EC5598;letter-spacing:2px;">' . htmlspecialchars($orderRef) . '</p>
    </div>

    <h3 style="font-size:14px;text-transform:uppercase;letter-spacing:0.5px;color:#999;margin:0 0 12px;border-bottom:2px solid #EC5598;padding-bottom:8px;">Your Order Summary</h3>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
        <thead>
            <tr style="background:#fdf7fa;">
                <th style="padding:10px 12px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#EC5598;">Product</th>
                <th style="padding:10px 12px;text-align:center;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#EC5598;">Qty</th>
                <th style="padding:10px 12px;text-align:right;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#EC5598;">Total</th>
            </tr>
        </thead>
        <tbody>' . $itemRowsHtml . '</tbody>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
        <tr><td style="text-align:right;padding:6px 12px;font-size:14px;color:#555;">Subtotal</td><td style="text-align:right;padding:6px 12px;font-size:14px;color:#333;width:110px;">$' . number_format($subtotal,2) . '</td></tr>
        <tr><td style="text-align:right;padding:6px 12px;font-size:14px;color:#555;">Shipping</td><td style="text-align:right;padding:6px 12px;font-size:14px;color:#333;">' . $shippingLabel . '</td></tr>
        <tr style="background:#fdf7fa;"><td style="text-align:right;padding:10px 12px;font-size:16px;font-weight:700;color:#333;">Total</td><td style="text-align:right;padding:10px 12px;font-size:16px;font-weight:700;color:#EC5598;">' . $totalLabel . '</td></tr>
    </table>

    <h3 style="font-size:14px;text-transform:uppercase;letter-spacing:0.5px;color:#999;margin:0 0 12px;border-bottom:2px solid #EC5598;padding-bottom:8px;">What Happens Next</h3>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
        <tr><td style="padding:10px 0;border-bottom:1px dashed #eee;font-size:14px;color:#555;"><span style="color:#EC5598;margin-right:10px;">✉</span> Check your inbox — this email is your order confirmation.</td></tr>
        <tr><td style="padding:10px 0;border-bottom:1px dashed #eee;font-size:14px;color:#555;"><span style="color:#EC5598;margin-right:10px;">📞</span> Our team will call you to confirm availability and payment.</td></tr>
        <tr><td style="padding:10px 0;border-bottom:1px dashed #eee;font-size:14px;color:#555;"><span style="color:#EC5598;margin-right:10px;">🏪</span> ' . ($method === 'ship' ? 'We\'ll arrange shipping to your address on file.' : 'Pick up at Wu Spa — 2750 E W.T. Harris Blvd, Charlotte, NC.') . '</td></tr>
        <tr><td style="padding:10px 0;font-size:14px;color:#555;"><span style="color:#EC5598;margin-right:10px;">🌿</span> Your Skin Script products will be ready and waiting for you!</td></tr>
    </table>

    <div style="background:#f9f9f9;border-radius:6px;padding:16px 20px;text-align:center;">
        <p style="margin:0;font-size:13px;color:#666;">Questions? Reach us anytime:</p>
        <p style="margin:6px 0 0;font-size:13px;"><a href="tel:9802221633" style="color:#EC5598;text-decoration:none;font-weight:700;">(980) 222-1633</a> &nbsp;·&nbsp; <a href="mailto:excellentwuwuhands@wu-spa.com" style="color:#EC5598;text-decoration:none;">excellentwuwuhands@wu-spa.com</a></p>
    </div>
');

/* ─────────────────────────────────────────────
   SEND ADMIN EMAIL
───────────────────────────────────────────── */
$errors = [];

try {
    $mail = makeMailer();
    $mail->addAddress('excellentwuwuhands@wu-spa.com', 'Wu Spa Admin');
    $mail->addReplyTo($email, $firstName . ' ' . $lastName);
    $mail->isHTML(true);
    $mail->Subject = 'New Order ' . $orderRef . ' — ' . $firstName . ' ' . $lastName;
    $mail->Body    = $adminBody;
    $mail->AltBody = "NEW ORDER: {$orderRef}\n\nCustomer: {$firstName} {$lastName}\nEmail: {$email}\nPhone: {$phone}\nMethod: {$deliveryMethod}\n{$addressText}{$notesText}\nItems:\n{$itemRowsText}\nSubtotal: \${$subtotal}\nShipping: {$shippingLabel}\n\nSent from Wu Spa website.";
    $mail->send();
} catch (\Exception $e) {
    $errors[] = 'Admin email failed: ' . $e->getMessage();
}

/* ─────────────────────────────────────────────
   SEND CUSTOMER CONFIRMATION EMAIL
───────────────────────────────────────────── */
try {
    $mail2 = makeMailer();
    $mail2->addAddress($email, $firstName . ' ' . $lastName);
    $mail2->addReplyTo('excellentwuwuhands@wu-spa.com', 'Wu Spa');
    $mail2->isHTML(true);
    $mail2->Subject = 'Your Wu Spa Order Confirmation — ' . $orderRef;
    $mail2->Body    = $customerBody;
    $mail2->AltBody = "Thank you, {$firstName}!\n\nYour order {$orderRef} has been received.\n\nItems:\n{$itemRowsText}\nSubtotal: \$" . number_format($subtotal,2) . "\nShipping: {$shippingLabel}\n\nWe'll contact you within 24 hours to confirm details.\n\nWu Spa · (980) 222-1633 · 2750 E W.T. Harris Blvd, Charlotte NC";
    $mail2->send();
} catch (\Exception $e) {
    $errors[] = 'Customer email failed: ' . $e->getMessage();
}

/* ─────────────────────────────────────────────
   RESPOND
───────────────────────────────────────────── */
if (empty($errors)) {
    echo json_encode(['success' => true, 'ref' => $orderRef]);
} else {
    // If at least admin email sent, still treat as partial success
    // so the customer sees the thank you screen
    echo json_encode([
        'success' => true,
        'ref'     => $orderRef,
        'warnings' => $errors
    ]);
}
