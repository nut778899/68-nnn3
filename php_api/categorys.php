
<?php
include 'condb.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // =========================
    // GET : ดึงข้อมูลหมวดหมู่
    // =========================
    if ($method === "GET") {

        $stmt = $conn->prepare("
            SELECT * FROM categorys
            ORDER BY category_id DESC
        ");

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "data" => $result
        ]);
    }

    // =========================
    // POST : เพิ่มหมวดหมู่
    // =========================
    elseif ($method === "POST") {

        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $_POST;
        }

        if (!is_array($data) || empty(trim($data["category_name"] ?? ""))) {
            echo json_encode([
                "success" => false,
                "message" => "กรุณากรอกชื่อหมวดหมู่"
            ]);
            exit;
        }

        $stmt = $conn->prepare("
            INSERT INTO categorys (category_name)
            VALUES (:category_name)
        ");

        $stmt->bindValue(
            ":category_name",
            trim($data["category_name"])
        );

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "เพิ่มหมวดหมู่เรียบร้อย"
            ]);
        }
    }

    // =========================
    // PUT : แก้ไขหมวดหมู่
    // =========================
    elseif ($method === "PUT") {

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !is_array($data) ||
            empty($data["category_id"]) ||
            empty(trim($data["category_name"] ?? ""))
        ) {
            echo json_encode([
                "success" => false,
                "message" => "กรุณาระบุ ID และชื่อหมวดหมู่"
            ]);
            exit;
        }

        $id = intval($data["category_id"]);

        $sql = "
            UPDATE categorys
            SET category_name = :category_name
            WHERE category_id = :id
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(
            ":category_name",
            trim($data["category_name"])
        );

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        echo json_encode([
            "success" => true,
            "message" => "แก้ไขหมวดหมู่เรียบร้อย"
        ]);
    }

    // =========================
    // DELETE : ลบหมวดหมู่
    // =========================
    elseif ($method === "DELETE") {

        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || empty($data["category_id"])) {
            echo json_encode([
                "success" => false,
                "message" => "ไม่พบ category_id"
            ]);
            exit;
        }

        $id = intval($data["category_id"]);

        $stmt = $conn->prepare("
            DELETE FROM categorys
            WHERE category_id = :id
        ");

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        echo json_encode([
            "success" => true,
            "message" => "ลบหมวดหมู่เรียบร้อย"
        ]);
    }

    // =========================
    // METHOD ไม่ถูกต้อง
    // =========================
    else {

        echo json_encode([
            "success" => false,
            "message" => "Method ไม่ถูกต้อง"
        ]);
    }

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>