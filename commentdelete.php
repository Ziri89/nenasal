<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Invalid Request Method. HTTP method should be DELETE',
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

    $fetch_comm = "SELECT * FROM `comments` WHERE id=:comment_id";
    $fetch_stmt = $conn->prepare($fetch_comm);
    $fetch_stmt->bindValue(':comment_id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :

        $delete_comm = "DELETE FROM `comments` WHERE id=:comment_id";
        $delete_comm_stmt = $conn->prepare($delete_comm);
        $delete_comm_stmt->bindValue(':comment_id', $data->id,PDO::PARAM_INT);

        if ($delete_comm_stmt->execute()) {

            echo json_encode([
                'success' => 1,
                'message' => 'Comment Deleted successfully'
            ]);
            exit;
        }

        echo json_encode([
            'success' => 0,
            'message' => 'Comment Not Deleted. Something is going wrong.'
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