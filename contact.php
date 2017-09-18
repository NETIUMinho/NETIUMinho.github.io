<?php
//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// message that will be displayed when everything is OK :)
$okMessage = 'Mensagem submetida com sucesso. Iremos responder-lhe brevemente!';

// If something goes wrong, we will display this message.
$errorMessage = 'Ocorreu um erro no envio da mensagem. Por favor tente novamente.';

// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('name' => 'Nome', 'surname' => 'Sobrenome', 'phone' => 'Telemóvel/Telefone', 'email' => 'Email', 'message' => 'Mensagem');

$mail = new PHPMailer(true);
try {
  
    //Compose the mail message
    $emailTextHtml = "<h1>Website NETIUM - Nova mensagem do formulário Contactos</h1><hr>";
    $emailTextHtml .= "<table>";
    
    foreach ($_POST as $key => $value) {
        // If the field exists in the $fields array, include it in the email
        if (isset($fields[$key])) {
            $emailTextHtml .= "<tr><th>$fields[$key]</th><td>$value</td></tr>";
        }
    }
    $emailTextHtml .= "</table><hr>";
    $emailTextHtml .= "<p>NETIUM - Núcleo de Estudantes de Engenharia de Telecomunicações e Informática da Universidade do Minho,<br>Sala C3.009, Campus de Azurém, Guimarães - 4800-058</p>";
    
    //Server settings
    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'geral@eng.uminho.pt';                 // SMTP username
    $mail->Password = 'direcaonetiummieti';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('geral@netium.eng.uminho.pt', 'NETIUM');
    $mail->addAddress('geral@eng.uminho.pt', 'User');     // Add a recipient


    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Website NETIUM - Mensagem do formulário de Contacto';
    $mail->Body    = $emailTextHtml;
    $mail->AltBody = strip_tags($emailTextHtml);
  
    if(!$mail->send()) {
        throw new Exception('I could not send the email.' . $mail->ErrorInfo);
    }
      $responseArray = array('type' => 'success', 'message' => $okMessage);
    
} catch (Exception $e) {
     $responseArray = array('type' => 'danger', 'message' => $errorMessage);
}

// if requested by AJAX request return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);
    
    header('Content-Type: application/json');
    
    echo $encoded;
}
// else just display the message
else {
    echo $responseArray['message'];
}

