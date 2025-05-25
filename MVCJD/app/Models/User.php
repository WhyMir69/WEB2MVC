<?php

namespace Models;

class User {
    public $id;
    public $name;
    public $username;
    public $email;
    public $password;
    public $role;
    public $token;

    public function __construct($data) {
        $this->id = $data['uid'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->username = $data['username'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->role = $data['role'] ?? 'user';
        $this->token = $data['token'] ?? null;
    }
}
