<?php
class MpesaAPI {
    private $consumerKey = 'ENduVTk6v1BzAt7a7jjAUdlABz0mO9yz';
    private $consumerSecret = '81FmSlyqlGUlDiRd';
    private $shortCode = '4082073'; // Keep as a string
    private $passkey = '78409425fc00e3e806eb7840d00f3e2d212348f185abb682a165e15154ca3f9d';
    private $baseUrl = 'https://api.safaricom.co.ke/mpesa/'; // Change to production if live

    // Function to get access token
    public function getAccessToken() {
        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        
        // Log the response for debugging
        error_log("Access Token Response: " . $response);
        
        $json = json_decode($response, true);
        if (isset($json['access_token'])) {
            return $json['access_token'];
        } else {
            // Handle error in obtaining access token
            error_log("Failed to obtain access token: " . json_encode($json));
            return null; // Return null or handle the error accordingly
        }
    }

    // Function for STK Push request
    public function stkPush($phoneNumber, $amount, $loanId) {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['ResponseCode' => '1', 'Message' => 'Invalid Access Token'];
        }
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortCode . $this->passkey . $timestamp);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl . 'stkpush/v1/processrequest');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken, 'Content-Type: application/json'));
        
        $data = array(
            'BusinessShortCode' => $this->shortCode, // Keep as string
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)$amount, // Ensure amount is an integer
            'PartyA' => (int)$phoneNumber, // Ensure phoneNumber is an integer
            'PartyB' => $this->shortCode, // Keep as string
            'PhoneNumber' => (int)$phoneNumber, // Ensure phoneNumber is an integer
            'CallBackURL' => 'https://portal.omabracredit.co.ke/api/callback.php',
            'AccountReference' => $loanId,
            'TransactionDesc' => 'Loan Payment'
        );

        error_log("STK Push Payload: " . json_encode($data));

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    // Function for C2B Confirmation request
    public function registerConfirmationURL($confirmationURL) {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['ResponseCode' => '1', 'Message' => 'Invalid Access Token'];
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl . 'c2b/v1/registerurl');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken, 'Content-Type: application/json'));
        
        $data = array(
            'ShortCode' => $this->shortCode, // Keep as string
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $confirmationURL,
            'ValidationURL' => 'https://portal.omabracredit.co.ke/api/validation.php' // Use a placeholder
        );
    
        error_log("Register Confirmation URL Payload: " . json_encode($data));
    
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }  

}

?>
