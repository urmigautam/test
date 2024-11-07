<?php

include('header.php'); 

// Database connection
$con = new mysqli("localhost", "root", "", "crud1");

// Check for connection errors
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Register class
class Register {
    private $name;
    private $email;
    private $mobile;
    private $message;
    private $file;
    private $con;
    private $errors = []; // To store validation errors

    // Constructor to initialize properties
    public function __construct($data, $fileData, $dbConnection) {
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->email = isset($data['email']) ? $data['email'] : null;
        $this->mobile = isset($data['mobile']) ? $data['mobile'] : null;
        $this->message = isset($data['message']) ? $data['message'] : null;
        $this->file = $fileData;
        $this->con = $dbConnection;
    }

    // Method to validate form data
    public function validateForm() {
        if (empty($this->name)) {
            $this->errors['name'] = "Name is required.";
        }

        if (empty($this->email)) {
            $this->errors['email'] = "Email is required.";
        } 

        if (empty($this->mobile)) {
            $this->errors['mobile'] = "Mobile number is required.";
        } elseif (!preg_match("/^\d{10}$/", $this->mobile)) {
            $this->errors['mobile'] = "Mobile number must be exactly 10 digits.";
        }

        return empty($this->errors); // Return true if no errors
    }

    // Method to handle file upload
    public function handleFileUpload() {
        if (empty($this->file['name'])) {
            $this->errors['file'] = "No file uploaded.";
            return false;
        }

        $fileName = $this->file['name'];
        $fileTmpName = $this->file['tmp_name'];
        $fileSize = $this->file['size'];
        $fileError = $this->file['error'];

        // Validate file extension and size
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExt !== 'jpeg') {
            $this->errors['file'] = "Invalid file type. Please upload a JPEG file.";
            return false;
        }

        if ($fileSize > 500 * 1024) { // Max size: 500KB
            $this->errors['file'] = "File size exceeds the 500KB limit.";
            return false;
        }

        if ($fileError !== 0) {
            $this->errors['file'] = "There was an error uploading the file.";
            return false;
        }

        // Move the file to the destination folder
        $fileDestination = 'uploads/' . uniqid('', true) . '.' . $fileExt;
        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            return $fileDestination; // Return the file path after successful upload
        } else {
            $this->errors['file'] = "Failed to move the uploaded file.";
            return false;
        }
    }

    // Method to process the form and file upload
    public function processForm() {
        $validationResult = $this->validateForm();
        if (!$validationResult) {
            return false; // Return false if validation fails
        }

        $fileUploadResult = $this->handleFileUpload();
        if ($fileUploadResult === false) {
            return false; // Return false if file upload fails
        }

        // If everything is successful, insert the data into the database
        $stmt = $this->con->prepare("INSERT INTO users (name, email, contact, message, profile) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $this->name, $this->email, $this->mobile, $this->message, $fileUploadResult);

        if ($stmt->execute()) {
            return "Registration successful! File uploaded to: " . $fileUploadResult;
        } else {
            return "Error: " . $stmt->error;
        }
    }

    // Get errors for display
    public function getErrors() {
        return $this->errors;
    }
}

// Process the form when submitted
$result = null;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $register = new Register($_POST, $_FILES['file'], $con);
    $result = $register->processForm();
    $errors = $register->getErrors(); // Get validation errors
}

?>

<!-- HTML Form for submitting data -->
<div class="container">
    <h2>Submit Your Information</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
        <?php if (isset($errors['name'])): ?>
            <div class="text-danger"><?php echo $errors['name']; ?></div>
        <?php endif; ?>

        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
        <?php if (isset($errors['email'])): ?>
            <div class="text-danger"><?php echo $errors['email']; ?></div>
        <?php endif; ?>

        <label for="mobile">Mobile:</label>
        <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo isset($_POST['mobile']) ? $_POST['mobile'] : ''; ?>" pattern="\d{10}" maxlength="10">
        <?php if (isset($errors['mobile'])): ?>
            <div class="text-danger"><?php echo $errors['mobile']; ?></div>
        <?php endif; ?>

        <label for="message">Message:</label>
        <textarea id="message" class="form-control" name="message"><?php echo isset($_POST['message']) ? $_POST['message'] : ''; ?></textarea>

        <label for="file">Upload JPEG File:</label>
        <input type="file" class="form-control" id="file" name="file" accept=".jpeg">
        <?php if (isset($errors['file'])): ?>
            <div class="text-danger"><?php echo $errors['file']; ?></div>
        <?php endif; ?>

        <button type="submit" class="btn btn-success btn-sm my-3">Submit</button>
        <a href="login.php" class="btn btn-primary btn-sm">Login</a>
    </form>

    <?php if ($result): ?>
        <div class="alert alert-info mt-3" id="id">
            <?php echo $result; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>
