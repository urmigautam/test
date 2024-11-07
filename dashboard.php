<?php 
include('header.php');

session_start();

// Database connection
$con = new mysqli("localhost", "root", "", "crud1");

// Check for connection errors
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Class to handle user session management
class Session {
    public static function checkLogin() {
        if (!isset($_SESSION['loggedin'])) {
            header('Location: login.php');
            exit;
        }
    }

    public static function logout() {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

// Class to handle database interactions
class Database {
    private $con;
    public function __construct($dbConnection) {
        $this->con = $dbConnection;
    }

    public function fetchAllUsers() {
        $result = $this->con->query("SELECT * FROM users");
        return $result;
    }

    
}

// print_r($con); die;


class UserList {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    // Method to display the users list in a table
    public function displayUsers() {
        $result = $this->db->fetchAllUsers();
        $sno = 1; // Initialize Sno counter

        echo '<div class="header-section container">
                <h3>Registered Users List</h3>
                <div>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                    <a href="register.php" class="btn btn-success">Register</a>
                </div>
              </div>';

        echo '<table class="table table-striped container">
                <thead>
                    <tr>
                        <th scope="col">Sno</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Message</th>
                        <th scope="col">Profile</th>
                    </tr>
                </thead>
                <tbody>';

        // Display all users from the database
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$sno}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['contact']}</td>
                    <td>{$row['Message']}</td>
                    <td><img src='{$row['profile']}' width='100' height='100' style='object-fit: cover;' /></td>
                  </tr>";
            $sno++;
        }

        echo '</tbody>
              </table>';
    }
}

// Check if the user is logged in
Session::checkLogin();

// Create the database object and fetch users
$db = new Database($con);
$userList = new UserList($db);
$userList->displayUsers();

include('footer.php');
?>
