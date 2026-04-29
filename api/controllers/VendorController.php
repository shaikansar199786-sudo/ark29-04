<?php
// api/controllers/VendorController.php

class VendorController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        try {
            $stmt = $this->conn->prepare("SELECT vendor_name FROM vendors");
            $stmt->execute();
            $vendors = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return ["status" => "success", "data" => $vendors];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function create($data) {
        $vendor = $data['vendor'] ?? '';
        $user_id = $data['user_id'] ?? 1;

        if ($vendor == '') {
            return ["status" => "error", "message" => "Vendor name required"];
        }

        try {
            // check duplicate
            $check = $this->conn->prepare("SELECT vendor_id FROM vendors WHERE vendor_name = ?");
            $check->execute([$vendor]);
            if ($check->rowCount() > 0) {
                return ["status" => "success", "message" => "Already exists"];
            }

            $code = "VND-" . strtoupper(substr($vendor, 0, 3)) . "-" . rand(100, 999);
            $sql = "INSERT INTO vendors (vendor_name, vendor_code, status, category, contact_person, phone, email, address, gst_number, created_by) 
                    VALUES (?, ?, 'active', 'General', '', '', '', '', '', ?)";
            
            $stmt = $this->conn->prepare($sql);
            if ($stmt->execute([$vendor, $code, $user_id])) {
                return ["status" => "success", "message" => "Vendor added"];
            } else {
                return ["status" => "error", "message" => "Insert failed"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Database Error: " . $e->getMessage()];
        }
    }
}

