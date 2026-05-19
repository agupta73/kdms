<?php

declare(strict_types=1);

namespace KdmsRegistration;

use PDO;

/**
 * Identical logic to Devotee::generateId() in kdms-api/api/Interface/devotees.php
 */
final class GenerateId
{
    public static function generate(PDO $conn): string
    {
        $result = ['1'];
        while (!empty($result)) {
            $id = 'P' . date('y') . date('m') . date('d') . rand(0, 999);
            $sql = "SELECT * FROM devotee where devotee_key = '" . $id . "'";
            $result = [];
            foreach ($conn->query($sql) as $row) {
                if (!empty($row)) {
                    array_push($result, $row);
                }
            }
        }

        return strtoupper($id);
    }
}
