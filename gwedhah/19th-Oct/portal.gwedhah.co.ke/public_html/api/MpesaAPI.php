<?php
class MpesaAPI {
    private $consumerKey = 'mR31pPIr0bVLG0AtHWIGn9K6xhdQIjDXPsXbf0RdhqCwrf6d';
    private $consumerSecret = 'x7svUW25VaGEYITOs8ua6OGkJyigXIGutj60TK1CuKqwGBvmbAdWcpADuNdUfVqh';
    private $shortCode = '174379'; // Keep as a string
    private $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    private $baseUrl = 'https://sandbox.safaricom.co.ke/mpesa/'; // Change to production if live

    public function getAccessToken() {
        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
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
            'CallBackURL' => 'https://portal.gwedhah.co.ke/api/callback.php',
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
}

?>