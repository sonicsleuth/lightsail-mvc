<!doctype html>
<html>
<head>
    <meta charset="uft-8">
    <meta name="author" content="Richard Soares">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightsail MVC for PHP :: Creating API Interfaces</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <?php load_style(['reset','prism','main']) ?>
</head>
<body>
<?php extend_view(['common/header'], $data) ?>

<p><a href="/docs">Go Back</a></p>

<h2>Creating API Interfaces</h2>

<p>While it is possible to return JSON from any Controller endpoint a formal API interface is best
because you can protect the endpoints with AUTH-Tokens and check for proper HTTP-Protocols.</p>

<p>Using the API Template as a starting point for your APIs, all that remains for you to do is add the
    required database activities like Creating, Reading, Updating, and Deleting records.</p>

<p>Have a look at the API template file here <strong>/app/controllers/api/ApiTemplate.php</strong>, also shown below:</p>

<pre><code class="language-php">/*
 * API Template
 * Path: /app/controllers/api/ApiTemplate.php
 *
 * A template PHP class that processes API requests using CRUD operations (Create, Read, Update, Delete)
 * and token-based authentication.
 *
 * Calling the API endpoints:
 * CREATE  http://localhost/api/ApiTemplate/create (POST) Pass form data as "form-data"
 * READ    http://localhost/api/ApiTemplate/read (GET)
 * UPDATE  http://localhost/api/ApiTemplate/update/123 (PUT) Pass form data as "www-data-form-urlencoded"
 * DELETE  http://localhost/api/ApiTemplate/delete/123 (DELETE)
 *
 * Use header AUTH-TYPE:"API Key" with a Key:"Api-Token" and Value:"your_api_key" for all request types.
 */

class ApiTemplate extends Controller
{
    private $myModel;
    private $authToken;

    // The name of the header token key to find when receiving a request.
    private $authTokenKeyName = 'Api-Token';

    // DO NOT STORE REAL TOKENS HERE... as I have done here for this template example.
    // Insert them as secret environment values. eg: from a ".env" file.
    // We can store more than one in this array as you might want to have a token for each protocol.
    private $validAuthTokens = [API_TOKEN_TEMPLATE];

    public function __construct()
    {
        // Get the authorization token from headers
        $this->authToken = $this->getApiToken($this->getAllHeaders());

        // Database Models used in this API
        // $this->myModel = $this->model('MyModel');
    }

    // Function to authenticate a token
    public function authenticateToken($validTokens = []): void
    {
        if (!in_array($this->authToken, $validTokens)) {
            $this->jsonResponse(405, "Missing Authorization Token", "");
        }
    }

    // Private function to extract the API token from the request header
    private function getApiToken($headers): ?string
    {
        if (isset($headers[$this->authTokenKeyName])) {
            return $headers[$this->authTokenKeyName];
        }
        return null;
    }

    private function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                // Convert HTTP_HEADER_NAME to Header-Name
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    // Function to handle Create (POST) request with form data
    public function create(): string
    {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(405, "Invalid request method. Use POST.");
        }

        // Retrieve form data from POST request
        // Make sure the data sent by the form is using: "form-data" to ensure we get a key:value array as $data.
        $data = $_POST;

        if (empty($data)) {
            return $this->jsonResponse(400, "No form data provided.");
        }

        // TODO: Add Logic to handle the creation of resources here
        // Example: Insert data into the database

        // Return success or failure message (add your conditional blocks here)
        return $this->jsonResponse(201, "Resource created successfully", $data);
    }

    // Function to handle Read (GET) request
    public function read($id = null): string
    {
        // Validate AuthToken
        $this->authenticateToken($this->validAuthTokens);

        // TODO: Add Logic to handle reading resources here
        // Example: Fetch data from the database by ID

        // Return success or failure message (add your conditional blocks here)
        if ($id) {
            return $this->jsonResponse(200, "Resource retrieved successfully", "Resource with ID: $id");
        } else {
            return $this->jsonResponse(200, "All resources retrieved successfully", "All resources");
        }
    }

    // Function to handle Update (PUT) request
    public function update($id): string
    {
        // Check if the request method is PUT
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return $this->jsonResponse(405, "Invalid request method. Use PUT.");
        }

        // Retrieve data from PUT request
        // Make sure the data sent by the form is using: "x-www-form-urlencoded" to ensure we get a key:value array as $data.
        parse_str(file_get_contents("php://input"), $data);

        if (empty($data)) {
            return $this->jsonResponse(400, "No data provided.");
        }

        // TODO: Add Logic to handle updating resources here
        // Example: Update resource in the database by ID

        // Return success or failure message (add your conditional blocks here)
        return $this->jsonResponse(200, "Resource with ID: $id updated successfully", $data);
    }

    // Function to handle Delete (DELETE) request
    public function delete($id): string
    {
        // Check if the request method is DELETE
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $this->jsonResponse(405, "Invalid request method. Use DELETE.");
        }

        // TODO: Logic to handle deleting resources here
        // Example: Remove resource from the database by ID

        // Return success or failure message (add your conditional blocks here)
        return $this->jsonResponse(200, "Resource with ID: $id deleted successfully");
    }

    // Private function to send JSON response with appropriate headers
    private function jsonResponse($statusCode, $message, $data = null): void
    {
        // Set the HTTP response status code
        http_response_code($statusCode);

        // Set content type to application/json
        header('Content-Type: application/json');

        // Prepare the response array
        $response = [
            'status' => $statusCode,
            'message' => $message,
        ];

        // If there's data, include it in the response
        if ($data !== null) {
            $response['data'] = $data;
        }

        // Return the response as JSON
        echo json_encode($response);
        exit; // Stop further script execution
    }
}
</code></pre><br>

<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>
