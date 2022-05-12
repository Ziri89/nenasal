<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Invalid Request Method. HTTP method should be POST',
    ]);
    exit;
endif;

require __DIR__.'/classes/Database.php';
$database = new Database();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->body) || !isset($data->user_id) || !isset($data->post_id)) :

    echo json_encode([
        'success' => 0,
        'message' => 'Please fill all the fields.',
    ]);
    exit;

elseif (empty(trim($data->body)) || empty(trim($data->user_id)) || empty(trim($data->post_id))) :

    echo json_encode([
        'success' => 0,
        'message' => 'Oops! empty field detected. Please fill all the fields.',
    ]);
    exit;

endif;

try {

    $body = htmlspecialchars(trim($data->body));
    $user_id = htmlspecialchars(trim($data->user_id));
    $post_id = htmlspecialchars(trim($data->post_id));

    $query = "INSERT INTO `comments`(body,user_id,post_id) VALUES(:body,:user_id,:post_id)";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':body', $body, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_STR);

    if ($stmt->execute()) {

        http_response_code(201);
        echo json_encode([
            'success' => 1,
            'message' => 'Data Inserted Successfully.'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => 0,
        'message' => 'Data not Inserted.'
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}