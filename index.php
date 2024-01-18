<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/middlewares/apiAuth.php';

include_once "db_connect.php";



// Include necessary files or initialize configurations here



if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}



switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        include_once 'functions/requestHandlers/handleGetRequest.php';

        // $user = authenticateUser();
        handleGetRequest($conn);
        break;

    case 'POST':
        include_once 'functions/requestHandlers/handlePostRequest.php';
        
        handlePostRequest($conn);
        break;

    case 'PUT':
        $user = authenticateUser();
        include_once 'functions/requestHandlers/handlePutRequest.php';

        handlePutRequest($conn);
        break;

    case "DELETE":
        $user = authenticateUser();
        include_once 'functions/requestHandlers/handleDeleteRequest.php';

        handleDeleteRequest($conn);
        break;

    default:
        // Handle other HTTP methods or unsupported routes
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}





// Additional functions for your logic can be added here
?>
