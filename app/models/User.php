<?php
// models/User.php
class User extends Model {
    protected $table = 'user';
    protected $primaryKey = 'userId';

    public function authenticate($username, $password) {
        $query = "SELECT * FROM user WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function register($data) {
        // Check if username or email exists
        $query = "SELECT COUNT(*) FROM user WHERE username = :username OR email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            return false; // User already exists
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role'] = 'User';
        
        return $this->create($data);
    }

    public function changePassword($userId, $oldPassword, $newPassword) {
        $user = $this->getById($userId);
        if (!$user) {
            return false;
        }

        if (password_verify($oldPassword, $user['password'])) {
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            return $this->update($userId, ['password' => $newPasswordHash]);
        }

        return false; 
    }
}