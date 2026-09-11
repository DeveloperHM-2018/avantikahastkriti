<?php
class Cart extends CI_Controller
{
    public function index()
    {
        redirect(base_url());
    }
    public function add()
    {
        // Retrieve the raw POST data
        $post = json_decode(file_get_contents("php://input"), true);  // Decode JSON input

        // Debugging output
        if (empty($post) || !isset($post['cartStore'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No data received',
            ]);
        } else {
            // If data is received successfully, you can process it further
            $cartStore = $post['cartStore'];  // Access the cartStore
            // Optionally log the received data for debugging
            log_message('info', 'Cart data received: ' . json_encode($cartStore));

            // You can add your database saving logic here

            // Send a success response
            echo json_encode([
                'status' => 'success',
                'message' => 'Cart data received',
                'data' => $cartStore, // returning the POST data back
            ]);
        }
    }
}
