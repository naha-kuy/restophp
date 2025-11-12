"<?php session_start(); header('Content-Type: application/json'); $input = file_get_contents('php://input'); echo json_encode(['received' => strlen($input), 'data' => $input]); ?>" 
