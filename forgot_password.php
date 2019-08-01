<?php
// core configuration
include_once "config/core.php";
 
// set page title
$page_title = "Forgot Password";
 
// include login checker
include_once "login_checker.php";
 
// include classes
include_once "config/database.php";
include_once 'objects/user.php';
include_once "libs/php/utils.php";

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// initialize objects
$user = new User($db);
$utils = new Utils();
 
// include page header HTML
include_once "layout_head.php";
 
// if the login form was submitted
if($_POST){
 
    echo "<div class='col-sm-12'>";
        
        // check if username and password are in the database
        $user->email=$_POST['email'];
 
        if($user->emailExists()){
 
            // update access code for user
            $access_code=$utils->getToken();
 
            $user->access_code=$access_code;
            if($user->updateAccessCode()){
                $send_to_email=$_POST['email'];
                try {
                    //Server settings
                    $mail->SMTPDebug = false;                                       // Enable verbose debug output
                    $mail->isSMTP();                                            // Set mailer to use SMTP
                    $mail->Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                    $mail->Username   = 'dnaz09.dn@gmail.com';                     // SMTP username
                    $mail->Password   = 'snnp@2018';                               // SMTP password
                    $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
                    $mail->Port       = 587;                                    // TCP port to connect to
                
                    //Recipients
                    $mail->setFrom('admin@orderingsystem.com', 'Ordering System');
                    $mail->addAddress($send_to_email);     // Add a recipient
                    // $mail->addAddress('ellen@example.com');               // Name is optional
                    // $mail->addReplyTo('info@example.com', 'Information');
                    // $mail->addCC('cc@example.com');
                    // $mail->addBCC('bcc@example.com');
                
                    // Attachments
                    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                    
                    // Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = "Reset Password";
                    $mail->Body    = "Hi there.<br /><br />";
                    $mail->Body    .= "Please click the following link to reset your password: {$home_url}reset_password/?access_code={$access_code}";
                    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                    
    
                    $mail->send();
                    echo "<div class='alert alert-info'>
                            Password reset link was sent to your email.
                            Click that link to reset your password.
                        </div>";
                    
                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger'>ERROR: Unable to send reset link.</div>";
                    }
            }
 
            // message if unable to update access code
            else{ echo "<div class='alert alert-danger'>ERROR: Unable to update access code.</div>"; }
 
        }
 
        // message if email does not exist
        else{ echo "<div class='alert alert-danger'>Your email cannot be found.</div>"; }
 
    echo "</div>";
}
 
// show reset password HTML form
echo "<div class='col-md-4'></div>";
echo "<div class='col-md-4'>";
 
    echo "<div class='account-wall'>
        <div id='my-tab-content' class='tab-content'>
            <div class='tab-pane active' id='login'>
                <img class='profile-img' src='images/login-icon.png'>
                <form class='form-signin' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='post'>
                    <input type='email' name='email' class='form-control' placeholder='Your email' required autofocus>
                    <input type='submit' class='btn btn-lg btn-primary btn-block' value='Send Reset Link' style='margin-top:1em;' />
                </form>
            </div>
        </div>
    </div>";
 
echo "</div>";
echo "<div class='col-md-4'></div>";
 
// footer HTML and JavaScript codes
include_once "layout_foot.php";
?>