<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Shiprocket Library
 * Handles authentication, order creation, and shipment management.
 */
class Shiprocket
{
    protected $CI;
    protected $token;
    protected $email;
    protected $password;
    protected $api_url;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->email = SHIPROCKET_EMAIL;
        $this->password = SHIPROCKET_PASSWORD;
        $this->api_url = SHIPROCKET_API_URL;
        $this->token = $this->get_token();
    }

    /**
     * Get Authentication Token
     */
    protected function get_token()
    {
        // Check if token exists in session and is not expired
        if ($this->CI->session->userdata('shiprocket_token')) {
            return $this->CI->session->userdata('shiprocket_token');
        }

        $response = $this->request('POST', 'auth/login', [
            'email' => $this->email,
            'password' => $this->password
        ], false);

        if (isset($response['token'])) {
            $this->CI->session->set_userdata('shiprocket_token', $response['token']);
            return $response['token'];
        }

        return null;
    }

    /**
     * Create Order in Shiprocket
     */
    public function create_order($data)
    {
        // Transform incoming $data to Shiprocket acceptable format if needed
        return $this->request('POST', 'orders/create/adhoc', $data);
    }

    /**
     * Create a Return (reverse pickup) Order in Shiprocket.
     *
     * NOTE: field names below follow Shiprocket's documented Return Order
     * contract (mirrors orders/create/adhoc but with pickup_* = customer's
     * address and shipping_* = the warehouse/RTO destination, since the
     * courier is picking up FROM the customer and delivering TO us). This
     * has not been exercised against a live Shiprocket account - verify the
     * exact field set against a real test order before relying on it in
     * production, since third-party API contracts can't be confirmed from
     * static docs alone.
     */
    public function create_return_order($data)
    {
        return $this->request('POST', 'orders/create/return', $data);
    }

    /**
     * Generate AWB for Shipment
     */
    public function generate_awb($shipment_id)
    {
        return $this->request('POST', 'courier/assign/awb', [
            'shipment_id' => $shipment_id
        ]);
    }

    /**
     * Request Pickup
     */
    public function request_pickup($shipment_id)
    {
        return $this->request('POST', 'courier/generate/pickup', [
            'shipment_id' => [$shipment_id]
        ]);
    }

    /**
     * Get Tracking Details
     */
    public function get_tracking($shipment_id)
    {
        return $this->request('GET', 'courier/track/shipment/' . $shipment_id);
    }

    protected function request($method, $endpoint, $params = [], $auth = true)
    {
        $url = $this->api_url . $endpoint;
        $headers = ['Content-Type: application/json'];

        if ($auth && $this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response = json_decode($result, true);

        // Logging for debugging (optional but recommended)
        $this->log_api_call($endpoint, $params, $response, $http_code);

        return $response;
    }

    protected function log_api_call($endpoint, $request, $response, $status)
    {
        // You can implement database logging here using CommonModel
        // Or simple file logging
        log_message('debug', "Shiprocket API: $endpoint | Status: $status | Request: " . json_encode($request) . " | Response: " . json_encode($response));
    }
}
