<?php
namespace App\Models;

class Student {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        return $this->db->query("SELECT * FROM students ORDER BY last_name, first_name")->get();
    }
    
    public function search($keyword = null) {
        $sql = "SELECT * FROM students WHERE 1=1";
        $params = [];
        
        if (!empty($keyword)) {
            $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $keyword = "%$keyword%";
            $params = [$keyword, $keyword, $keyword, $keyword];
        }
        
        $sql .= " ORDER BY last_name, first_name";
        
        return $this->db->query($sql, $params)->get();
    }
    
    public function find($id) {
        return $this->db->query("SELECT * FROM students WHERE id = ?", [$id])->find();
    }
    
    public function create($data) {
        $this->db->query(
            "INSERT INTO students (first_name, last_name, email, phone, address, date_of_birth) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['first_name'], 
                $data['last_name'], 
                $data['email'], 
                $data['phone'],
                $data['address'] ?? null,
                $data['date_of_birth'] ?? null
            ]
        );
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $this->db->query(
            "UPDATE students 
             SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, date_of_birth = ? 
             WHERE id = ?",
            [
                $data['first_name'], 
                $data['last_name'], 
                $data['email'], 
                $data['phone'],
                $data['address'] ?? null,
                $data['date_of_birth'] ?? null,
                $id
            ]
        );
    }
    
    public function delete($id) {
        $this->db->query("DELETE FROM students WHERE id = ?", [$id]);
    }
    
    public function getCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM students")->find();
        return $result ? $result['count'] : 0;
    }
    
    public function getRecent($limit = 5) {
        return $this->db->query(
            "SELECT * FROM students ORDER BY created_at DESC LIMIT ?", 
            [$limit]
        )->get();
    }
    
    public function getCourses($studentId) {
        return $this->db->query(
            "SELECT c.* FROM courses c
             JOIN enrollments e ON c.id = e.course_id
             WHERE e.student_id = ?
             ORDER BY c.course_code",
            [$studentId]
        )->get();
    }
    
    public function enrollInCourse($studentId, $courseId) {
        try {
            $this->db->query(
                "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)",
                [$studentId, $courseId]
            );
            return true;
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { 
                return false;
            }
            throw $e;
        }
    }
    
    public function unenrollFromCourse($studentId, $courseId) {
        $this->db->query(
            "DELETE FROM enrollments WHERE student_id = ? AND course_id = ?",
            [$studentId, $courseId]
        );
    }
}
