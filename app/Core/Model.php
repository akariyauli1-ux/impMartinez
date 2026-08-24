<?php
class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_null($param)) {
                    $types .= 's';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt;
    }
    
    protected function insertBlob($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        $types = '';
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_null($value)) {
                $types .= 's';
            } else {
                $types .= 's';
            }
        }
        
        $stmt->bind_param($types, ...$values);
        $stmt->send_long_data(0, 0, $values[0]);
        $stmt->execute();
        return $this->db->insert_id;
    }
    
    protected function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    protected function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    protected function insert($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ($placeholders)";
        $stmt = $this->query($sql, $values);
        return $this->db->insert_id;
    }
    
    protected function update($data, $where, $whereParams = []) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values = array_merge($values, $whereParams);
        
        $sql = "UPDATE {$this->table} SET " . implode(',', $fields) . " WHERE $where";
        $stmt = $this->query($sql, $values);
        return $stmt->affected_rows;
    }
    
    protected function delete($where, $params = []) {
        $sql = "DELETE FROM {$this->table} WHERE $where";
        $stmt = $this->query($sql, $params);
        return $stmt->affected_rows;
    }
}
