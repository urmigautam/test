<?php 
include('header.php'); 

session_start();

// Define the Login class
class Login {

    private $username;
    private $password;
    private $errorMessage;

    // Constructor to initialize the username and password
    public function __construct($username = null, $password = null) {
        $this->username = $username;
        $this->password = $password;
        $this->errorMessage = '';
    }

    // Method to validate user credentials
    public function validateCredentials() {
        // Replace with actual user validation logic (e.g. query a database)
        if ($this->username === 'admin' && $this->password === 'password') {
            $_SESSION['loggedin'] = true;
            header('Location: dashboard.php');
            exit;
        } else {
            $this->errorMessage = 'Invalid credentials.';
        }
    }

    // Method to get the error message
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    // Method to check if the form was submitted and handle it
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->username = $_POST['username'];
            $this->password = $_POST['password'];
            $this->validateCredentials();
        }
    }
}

// Create an instance of the Login class
$login = new Login();
$login->handleLogin();
?>

<div class="container">
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" class="form-control" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" class="form-control" id="password" name="password" required>

        <button type="submit" class="btn btn-success btn-sm my-3">Login</button>
        <a href="./register.php" class="btn btn-primary btn-sm">Register</a>

        <?php if ($login->getErrorMessage()) : ?>
            <div class="text-danger mt-3">
                <?php echo $login->getErrorMessage(); ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include('footer.php'); ?>
