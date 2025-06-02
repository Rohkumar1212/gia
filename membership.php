<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign input values
    $name = htmlspecialchars(trim($_POST['fullName']));
    $companyName = htmlspecialchars(trim($_POST['companyName']));
    $designation = htmlspecialchars(trim($_POST['designation']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $membershipType = htmlspecialchars(trim($_POST['membershipType']));
    $reason = htmlspecialchars(trim($_POST['reason']));
    $benefits = isset($_POST['benefits']) ? $_POST['benefits'] : [];
    $benefitsOther = htmlspecialchars(trim($_POST['benefitsOther'] ?? ''));

    // Validate required fields
    if (empty($name) || empty($companyName) || empty($designation) || empty($email) || empty($phone) || empty($membershipType) || empty($reason)) {
        echo '<script>
                alert("Please fill in all required fields.");
                window.location.href="membership_form.html";
              </script>';
        exit;
    }

    // Handle file upload (company profile/document)
    $file_name = 'No file uploaded';
    $file_path = '';
    if (isset($_FILES['companyProfile']) && $_FILES['companyProfile']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        $file_tmp = $_FILES['companyProfile']['tmp_name'];
        $file_name = basename($_FILES['companyProfile']['name']);
        $file_size = $_FILES['companyProfile']['size'];
        $file_type = mime_content_type($file_tmp);

        if (!in_array($file_type, $allowed_types)) {
            echo '<script>
                    alert("Invalid file type. Only JPEG, PNG, and PDF files are allowed.");
                    window.location.href="membership_form.html";
                  </script>';
            exit;
        }

        if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            echo '<script>
                    alert("File size exceeds 2MB limit.");
                    window.location.href="membership_form.html";
                  </script>';
            exit;
        }

        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_path = $upload_dir . $file_name;
        if (!move_uploaded_file($file_tmp, $file_path)) {
            echo '<script>
                    alert("Failed to upload file.");
                    window.location.href="membership_form.html";
                  </script>';
            exit;
        }
    }

    // Prepare benefits as a comma-separated string
    $benefits_list = implode(", ", $benefits);
    if (!empty($benefitsOther)) {
        $benefits_list .= ", Other: " . $benefitsOther;
    }

    // Compose the email message
    $message = "<div style='font-family: Arial, sans-serif;'>
                    <h3>New Membership Form Submission</h3>
                    <p><strong>Full Name:</strong> " . nl2br($name) . "</p>
                    <p><strong>Company Name:</strong> " . nl2br($companyName) . "</p>
                    <p><strong>Designation:</strong> " . nl2br($designation) . "</p>
                    <p><strong>Email Address:</strong> " . nl2br($email) . "</p>
                    <p><strong>Phone Number:</strong> " . nl2br($phone) . "</p>
                    <p><strong>Membership Type:</strong> " . nl2br($membershipType) . "</p>
                    <p><strong>Reason for Joining:</strong><br> " . nl2br($reason) . "</p>
                    <p><strong>Benefits Interested In:</strong> " . nl2br($benefits_list) . "</p>
                    <p><strong>File Uploaded:</strong> " . ($file_path ? $file_name : "None") . "</p>
                </div>";

    // Email headers
    $to = 'rohitkumar.cs999@gmail.com'; // Replace with your email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . $name . " <" . $email . ">" . "\r\n";
    $headers .= "Cc: technowhizz.rohit@gmail.com" . "\r\n";

    // Send email
    if (mail($to, "New Membership Form Submission", $message, $headers)) {
        echo '<script>
                alert("Form submitted successfully!");
                window.location.href="membership_form.html";
              </script>';
    } else {
        echo '<script>
                alert("Failed to submit the form. Please try again later.");
                window.location.href="membership_form.html";
              </script>';
    }
} else {
    echo '<script>
            alert("Invalid request method.");
            window.location.href="membership_form.html";
          </script>';
}
?>
