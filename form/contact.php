<?php
header('Content-type: application/json');

### Success Messages
$msg_success = "We have <strong>successfully</strong> received your message. We'll get back to you soon.";

if( $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["contact-email"]) && $_POST["contact-email"] != '' && isset($_POST["contact-name"]) && $_POST["contact-name"] != '') {
        ### Form Fields
        $cf_email = $_POST["contact-email"];
        $cf_name = $_POST["contact-name"];
        $cf_message = isset($_POST["contact-message"]) ? $_POST["contact-message"] : '';

        $honeypot   = isset($_POST["form-anti-honeypot"]) ? $_POST["form-anti-honeypot"] : 'bot';
        
        if ($honeypot == '') {

            $data = array (
                'email' => $cf_email,
                'name' => $cf_name,
                'message' => $cf_message
            );

            $postData = json_encode($data);
            
            $ch = curl_init('https://mail.catalystmx.com/mail');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            
            // Set HTTP Header for POST request 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData))
            );

            $server_output = curl_exec($ch);
            curl_close ($ch);

            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if( $code === 200 ) {
                $response = array ('result' => "success", 'message' => $msg_success);
            } else {
                $response = array ('result' => "error", 'message' => $mail->ErrorInfo);
            }

            echo json_encode($response);
            
        } else {
            echo json_encode(array ('result' => "error", 'message' => "Bot <strong>Detected</strong>.! Clean yourself Botster.!"));
        }
    } else {
        echo json_encode(array ('result' => "error", 'message' => "Please <strong>Fill up</strong> all required fields and try again."));
    }
}

?>