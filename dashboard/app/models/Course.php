<?php
namespace App\Models;

class Course {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        return $this->db->query("SELECT * FROM courses ORDER BY course_code")->get();
    }
    
    public function search($keyword = null, $credits = null) {
        $sql = "SELECT * FROM courses WHERE 1=1";
        $params = [];
        
        if (!empty($keyword)) {
            $sql .= " AND (course_code LIKE ? OR title LIKE ? OR description LIKE ?)";
            $keyword = "%$keyword%";
            $params = [$keyword, $keyword, $keyword];
        }
        
        if (!empty($credits)) {
            $sql .= " AND credits = ?";
            $params[] = $credits;
        }
        
        $sql .= " ORDER BY course_code";
        
        return $this->db->query($sql, $params)->get();
    }
    
    public function find($id) {
        return $this->db->query("SELECT * FROM courses WHERE id = ?", [$id])->find();
    }
    
    public function create($data) {
        $this->db->query(
            "INSERT INTO courses (course_code, title, description, credits) 
             VALUES (?, ?, ?, ?)",
            [
                $data['course_code'], 
                $data['title'], 
                $data['description'] ?? null, 
                $data['credits']
            ]
        );
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $this->db->query(
            "UPDATE courses 
             SET course_code = ?, title = ?, description = ?, credits = ? 
             WHERE id = ?",
            [
                $data['course_code'], 
                $data['title'], 
                $data['description'] ?? null, 
                $data['credits'],
                $id
            ]
        );
    }
    
    public function delete($id) {
        $this->db->query("DELETE FROM courses WHERE id = ?", [$id]);
    }
    
    public function getCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM courses")->find();
        return $result ? $result['count'] : 0;
    }
    
    public function getRecent($limit = 5) {
        return $this->db->query(
            "SELECT * FROM courses ORDER BY created_at DESC LIMIT ?", 
            [$limit]
        )->get();
    }
    
    public function getStudents($courseId) {
        return $this->db->query(
            "SELECT s.* FROM students s
             JOIN enrollments e ON s.id = e.student_id
             WHERE e.course_id = ?
             ORDER BY s.last_name, s.first_name",
            [$courseId]
        )->get();
    }
    
    public function hasEnrollments($courseId) {
        $result = $this->db->query(
            "SELECT COUNT(*) as count FROM enrollments WHERE course_id = ?",
            [$courseId]
        )->find();
        
        return $result && $result['count'] > 0;
    }
}
