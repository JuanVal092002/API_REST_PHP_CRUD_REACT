<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "FronBack";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM records WHERE id=$id";
            $result = $conn->query($sql);
            echo json_encode($result->fetch_assoc());
        } else {
            $sql = "SELECT * FROM records";
            $result = $conn->query($sql);
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = $data['nombre'];
        $descripcion = $data['descripcion'];

        // Esta es la funcionalidad para que se busque el Id mas bajo que haya disponible.
        // Con esto se soluciona el problema de que queden espacios entre los Ids. Cuando borro un dato 
        $sql = "SELECT id FROM records ORDER BY id ASC";
        $result = $conn->query($sql);
        $ids = array();
        while ($row = $result->fetch_assoc()) {
            $ids[] = $row['id'];
        }
        $new_id = 1;
        for ($i = 0; $i < count($ids); $i++) {
            if ($new_id < $ids[$i]) {
                break;
            }
            $new_id++;
        }

        $sql = "INSERT INTO records (id, nombre, descripcion) VALUES ($new_id, '$nombre', '$descripcion')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("id" => $new_id));
        } else {
            echo json_encode(array("error" => $conn->error));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id']);
        $nombre = $data['nombre'];
        $descripcion = $data['descripcion'];
        $sql = "UPDATE records SET nombre='$nombre', descripcion='$descripcion' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Record updated successfully"));
        } else {
            echo json_encode(array("error" => $conn->error));
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "DELETE FROM records WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(array("message" => "Record deleted successfully"));
            } else {
                echo json_encode(array("error" => $conn->error));
            }
        }
        break;

    default:
        echo json_encode(array("error" => "Invalid request method"));
        break;
}

$conn->close();
?>
