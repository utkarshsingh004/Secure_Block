<?php
// Database configuration
$host = 'vultr-prod-cfbaea5c-14b5-4c87-b3e2-9929eed22e05-vultr-prod-4489.vultrdb.com';
$port = '16751';
$dbname = 'defaultdb';
$username = 'vultradmin';
$password = 'special_password';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='output-message'>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>");
}

$outputMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = htmlspecialchars(strip_tags($_POST['category']));
    $description = htmlspecialchars(strip_tags($_POST['description']));
    $dateOfIncident = htmlspecialchars(strip_tags($_POST['date']));
    $evidenceFilename = null;

    // File upload handling
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $fileType = mime_content_type($_FILES['evidence']['tmp_name']);
        
        if (in_array($fileType, $allowedTypes)) {
            $evidenceFilename = 'uploads/' . basename($_FILES['evidence']['name']);
            if (!move_uploaded_file($_FILES['evidence']['tmp_name'], $evidenceFilename)) {
                die("<div class='output-message'>Failed to upload file.</div>");
            }
        } else {
            die("<div class='output-message'>Invalid file type.</div>");
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO complaints (category, description, incident_date, evidence, user_id) 
                               VALUES (:category, :description, :dateOfIncident, :evidence, 1)");
        
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':dateOfIncident', $dateOfIncident);
        $stmt->bindParam(':evidence', $evidenceFilename);

        $stmt->execute();

        // Run Python model
        $pythonScriptPath = '/path/to/model.py';
        $command = escapeshellcmd("python3 $pythonScriptPath");
        $output = shell_exec($command);

        $outputMessage = "<div class='success-message'>Python model executed successfully!<br>Complaint submitted successfully!</div>";
    } catch (Exception $e) {
        $outputMessage = "<div class='error-message'>Error submitting complaint: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File a Cyber Complaint</title>
    <link rel="icon" href="images/shield.png">
    <style>
   /* Style for header */
   body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* Style for header */
        header {
            background-color: #1d3557;
            padding: 10px 0;
            color: white;
            width: 100%;
            height: 40px;
            border: 2px solid white;
            display: flex;
            flex-direction: row;
        }

        .logo {
            width: 70%;
        }

        .logo img {
            width: 40px;
            padding-left: 4%;
        }

        header nav {
            width: 30%;
        }

        header nav ul {
            width: 100%;
            display: flex;
            flex: row;
            justify-content: space-evenly;
            margin-top: 0px;
        }

        header nav ul li {
            list-style: none;
        }

        header nav ul li a {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-weight: bolder;
            font-size: larger;
        }


        /* Style for main */
        /* Complaint Container Styling */
        .complaint-container {
            width: 100%;
            max-width: 500px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Form Heading */
        h2 {
            text-align: center;
            color: #1d3557;
            margin-bottom: 20px;
        }

        /* Form Labels */
        .complaint-container label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        /* Form Inputs and Textarea */
        .complaint-container input[type="text"],
        .complaint-container textarea,
        .complaint-container input[type="date"],
        .complaint-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .complaint-container input[type="file"] {
            margin: 10px 0;
        }

        /* Submit Button Styling */
        .complaint-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #1d3557;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        /* Submit Button Hover Effect */
        .complaint-container input[type="submit"]:hover {
            background-color: #457b9d;
        }

        /* Other Category Input */
        .other-category-input {
            display: none;
        }


        /* Style for footer */
        footer {
            background-color: #1d3557;
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-top: 3%;
        }

        .footer-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .footer-column {
            flex: 1;
            margin: 20px;
            min-width: 250px;
        }

        h4 {
            margin-bottom: 15px;
            font-size: 18px;
            color: #ffb703;
        }

        p,
        a {
            color: white;
            font-size: 14px;
            line-height: 1.8;
            text-decoration: none;
        }

        a:hover {
            color: #ffb703;
        }

        .social-icons {
            list-style-type: none;
            padding: 0;
        }

        .social-icons li {
            display: inline;
            margin: 0 10px;
        }

        .social-icons a {
            font-size: 20px;
            color: white;
        }

        .social-icons a:hover {
            color: #ffb703;
        }

        .footer-bottom {
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
            font-size: 12px;
            color: #ccc;
        }

        /* Responive for max-width: 630px */
       @media screen and (max-width:1000px){
          body{
            display: none;
          }
       }        /* Success Message */
        .success-message {
            color: green;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }

        /* Error Message */
        .error-message {
            color: red;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <header>
    <a href="#" class="logo"><img src="images/shield.png" alt=""></a>
        <nav>
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="log_in.html">Log out</a></li>
            </ul>
        </nav>    </header>

    <main>
        <div class="complaint-container">
            <h2>File a Cyber Complaint</h2>

            <!-- Display success or error message -->
            <?php if (!empty($outputMessage)) echo $outputMessage; ?>

            <form action="complaint.php" method="POST" enctype="multipart/form-data">
                <label for="category">Category of Complaint</label>
                <select id="category" name="category" required onchange="toggleOtherCategoryInput()">
                    <option value="">Select Category</option>
                    <option value="Fraud">Fraud</option>
                    <option value="Hacking">Hacking</option>
                    <option value="Identity Theft">Identity Theft</option>
                    <option value="Harassment">Harassment</option>
                    <option value="Other">Other</option>
                </select>

                <!-- Hidden Other Category Text Input -->
                <div id="other-category" class="other-category-input">
                    <label for="other-category-text">Please Specify Other Category</label>
                    <input type="text" id="other-category-text" name="other-category-text"
                        placeholder="Enter other category">
                </div>

                <label for="description">Description of the Incident</label>
                <textarea id="description" name="description" rows="5" placeholder="Describe the incident in detail"
                    required></textarea>

                <label for="date">Date of Incident</label>
                <input type="date" id="date" name="date" required>

                <label for="evidence">Upload Evidence (Optional)</label>
                <input type="file" id="evidence" name="evidence">

                <input type="submit" value="Submit Complaint">
            </form>
        </div>
    </main>

    <footer>
    <div class="footer-container">
            <!-- Contact Info -->
            <div class="footer-column">
                <h4>Contact Us</h4>
                <p>123 Cyber Street, Security City</p>
                <p>Email: contact@cybercomplaint.com</p>
                <p>Phone: +91 1234 567 890</p>
            </div>

            <!-- Quick Links -->
            <div class="footer-column">
                <h4>Quick Links</h4>
                <p><a href="#home">Home</a></p>
                <p><a href="#about">About Us</a></p>
                <p><a href="#services">Services</a></p>
                <p><a href="#privacy">Privacy Policy</a></p>
            </div>

            <!-- Social Media Links -->
            <div class="footer-column">
                <h4>Follow Us</h4>
                <ul class="social-icons">
                    <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                    <li><a href="#"><i class="fab fa-linkedin"></i> LinkedIn</a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>&copy; 2024 Cyber Complaint. All Rights Reserved.</p>
        </div>    </footer>
</body>
</html>
