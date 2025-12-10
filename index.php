<?php
// index.php
// Simple, secure handling for registration form.
// Start session for flash messages (optional)
session_start();

function s($v){ return htmlspecialchars(trim($v), ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

$errors = [];
$data = [];

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize server-side
    $data['firstName'] = s($_POST['firstName'] ?? '');
    $data['lastName']  = s($_POST['lastName'] ?? '');
    $data['email']     = s($_POST['email'] ?? '');
    $data['phone']     = s($_POST['phone'] ?? '');
    $data['dob']       = s($_POST['dob'] ?? '');
    $data['gender']    = s($_POST['gender'] ?? '');
    $data['address']   = s($_POST['address'] ?? '');
    $data['course']    = s($_POST['course'] ?? '');

    // Basic server-side validation
    if (strlen($data['firstName']) < 1) $errors[] = "First name is required.";
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    $digits = preg_replace('/\D+/', '', $data['phone']);
    if (strlen($digits) < 7 || strlen($digits) > 15) $errors[] = "Phone number must be 7–15 digits.";
    if (strlen($data['course']) < 1) $errors[] = "Course applied for is required.";

    // Optional: handle photo upload (sanitized, small)
    $photoDataUrl = '';
    if (!empty($_FILES['photo']['name'])) {
        $f = $_FILES['photo'];
        if ($f['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg','image/png','image/webp'];
            if (in_array($f['type'], $allowed) && $f['size'] <= 1_000_000) {
                $bin = file_get_contents($f['tmp_name']);
                $b64 = base64_encode($bin);
                $photoDataUrl = "data:{$f['type']};base64,{$b64}";
            } else {
                $errors[] = "Photo must be JPG/PNG/WebP and <= 1MB.";
            }
        } else {
            $errors[] = "Error uploading photo.";
        }
    }

    // If no errors, mark success
    if (empty($errors)) {
        // You could save to DB or file here. For demo we just show the result.
        $submittedAt = date('d M Y, H:i');
        // Show the result below (rendered in HTML)
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Application / Registration Form — Modern</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="js/script.js" defer></script>
</head>
<body>
  <div class="app-shell">
    <header class="topbar">
      <div class="brand">
        <div class="logo">RA</div>
        <div>
          <h1>Registration App</h1>
          <p class="muted">Online application — HTML · jQuery · PHP</p>
        </div>
      </div>
      <div class="top-actions">
        <a class="btn ghost" href="index.php">New Application</a>
      </div>
    </header>

    <main class="main">
      <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)): ?>
        <!-- SUCCESS: display formatted result -->
        <section class="result-card">
          <div class="result-header">
            <div>
              <h2>Application Received</h2>
              <p class="muted">Submitted on <?php echo $submittedAt; ?></p>
            </div>
            <div>
              <button id="printBtn" class="btn">Print / Save PDF</button>
            </div>
          </div>

          <div class="app-grid">
            <div class="card-left">
              <?php if ($photoDataUrl): ?>
                <div class="avatar"><img src="<?php echo $photoDataUrl; ?>" alt="Applicant photo"></div>
              <?php else: ?>
                <div class="avatar placeholder"><?php echo strtoupper(substr($data['firstName'] ?: 'A',0,1)); ?></div>
              <?php endif; ?>

              <h3><?php echo $data['firstName'] . ' ' . $data['lastName']; ?></h3>
              <p class="muted"><?php echo $data['course']; ?></p>

              <dl class="info-list">
                <dt>Email</dt><dd><?php echo $data['email']; ?></dd>
                <dt>Phone</dt><dd><?php echo $data['phone']; ?></dd>
                <dt>DOB</dt><dd><?php echo $data['dob'] ?: 'N/A'; ?></dd>
                <dt>Gender</dt><dd><?php echo $data['gender'] ?: 'N/A'; ?></dd>
              </dl>
            </div>

            <div class="card-right">
              <h4>Address</h4>
              <p><?php echo nl2br($data['address'] ?: 'N/A'); ?></p>

              <hr>

              <h4>Application Summary</h4>
              <table class="summary-table">
                <tr><th>Field</th><th>Value</th></tr>
                <tr><td>Full name</td><td><?php echo $data['firstName'] . ' ' . $data['lastName']; ?></td></tr>
                <tr><td>Email</td><td><?php echo $data['email']; ?></td></tr>
                <tr><td>Phone</td><td><?php echo $data['phone']; ?></td></tr>
                <tr><td>Course</td><td><?php echo $data['course']; ?></td></tr>
              </table>

              <div class="actions-row">
                <a href="index.php" class="btn ghost">Submit Another</a>
                <button id="downloadBtn" class="btn">Download (PNG)</button>
              </div>
            </div>
          </div>
        </section>

      <?php else: ?>
        <!-- SHOW FORM (or show errors if present) -->
        <section class="form-card">
          <h2>Online Application Form</h2>
          <p class="muted">Fill the form and click <strong>Submit</strong> to view the formatted application.</p>

          <?php if (!empty($errors)): ?>
            <div class="errors">
              <strong>Fix these errors:</strong>
              <ul>
                <?php foreach($errors as $e) echo "<li>".s($e)."</li>"; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form id="regForm" method="POST" enctype="multipart/form-data" novalidate>
            <div class="grid">
              <div class="form-group">
                <label for="firstName">First name <span class="req">*</span></label>
                <input id="firstName" name="firstName" type="text" value="<?php echo s($_POST['firstName'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="lastName">Last name</label>
                <input id="lastName" name="lastName" type="text" value="<?php echo s($_POST['lastName'] ?? ''); ?>">
              </div>

              <div class="form-group">
                <label for="email">Email <span class="req">*</span></label>
                <input id="email" name="email" type="email" value="<?php echo s($_POST['email'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="phone">Phone <span class="req">*</span></label>
                <input id="phone" name="phone" type="tel" value="<?php echo s($_POST['phone'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input id="dob" name="dob" type="date" value="<?php echo s($_POST['dob'] ?? ''); ?>">
              </div>

              <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                  <option value="">-- Select --</option>
                  <option <?php if (($_POST['gender'] ?? '')==='Male') echo 'selected'; ?>>Male</option>
                  <option <?php if (($_POST['gender'] ?? '')==='Female') echo 'selected'; ?>>Female</option>
                  <option <?php if (($_POST['gender'] ?? '')==='Other') echo 'selected'; ?>>Other</option>
                </select>
              </div>

              <div class="form-group full">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?php echo s($_POST['address'] ?? ''); ?></textarea>
              </div>

              <div class="form-group">
                <label for="course">Course applied for <span class="req">*</span></label>
                <input id="course" name="course" type="text" value="<?php echo s($_POST['course'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="photo">Upload photo (optional, &lt;=1MB)</label>
                <input id="photo" name="photo" type="file" accept="image/*">
                <div id="photoPreview" class="photo-preview"></div>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn">Submit Application</button>
              <button type="reset" class="btn ghost">Reset</button>
            </div>
            <p class="muted small">Fields with <span class="req">*</span> are required.</p>
          </form>
        </section>
      <?php endif; ?>
    </main>

    <footer class="footer">
      <p>&copy; <?php echo date('Y'); ?> Registration App • Built with HTML · CSS · jQuery · PHP</p>
    </footer>
  </div>

  <!-- Optional client-side template data for print/export -->
  <script>
    // Pass server-side data to JS for potential client-side use (safely)
    <?php if (!empty($data) && empty($errors)): ?>
    window._APP_DATA = <?php echo json_encode($data); ?>;
    <?php else: ?>
    window._APP_DATA = null;
    <?php endif; ?>
  </script>
</body>
</html>
