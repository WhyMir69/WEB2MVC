<?php
interface DataRepositoryInterface {
    public function getAll();
    public function getById($id);
    public function getByEmail($email);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}
