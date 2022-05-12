<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Invalid Request Method. HTTP method should be PUT',
    ]);
    exit;
endif;

require __DIR__.'/classes/Database.php';
$database = new Database();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(['success' => 0, 'message' => 'Please provide the comment ID.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM `comments` WHERE id=:comment_id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':comment_id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :

        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $comment_body = isset($data->body) ? $data->body : $row['body'];
       // $comment_user_id = isset($data->user_id) ? $data->user_id : $row['user_id'];
       // $comment_post_id = isset($data->post_id) ? $data->post_id : $row['post_id'];
//, user_id = :user_id, post_id = :post_id 
        $update_query = "UPDATE `comments` SET body = :body
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':body', htmlspecialchars(strip_tags($comment_body)), PDO::PARAM_STR);
        $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);


        if ($update_stmt->execute()) {

            echo json_encode([
                'success' => 1,
                'message' => 'Comment updated successfully'
            ]);
            exit;
        }

        echo json_encode([
            'success' => 0,
            'message' => 'Comment Not updated. Something is going wrong.'
        ]);
        exit;

    else :
        echo json_encode(['success' => 0, 'message' => 'Invalid ID. No comment found by the ID.']);
        exit;
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}