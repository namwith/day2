<?php
class Customer {
    private $id;
    private $username;
    private $password;
    private $fullname;
    private $address;
    private $phone;
    private $gender;
    private $birthday;
    
    // Constructor
    public function __construct($id, $username, $password, $fullname, $address, $phone, $gender, $birthday) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->fullname = $fullname;
        $this->address = $address;
        $this->phone = $phone;
        $this->gender = $gender;
        $this->birthday = $birthday;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getPassword() { return $this->password; }
    public function getFullname() { return $this->fullname; }
    public function getAddress() { return $this->address; }
    public function getPhone() { return $this->phone; }
    public function getGender() { return $this->gender; }
    public function getBirthday() { return $this->birthday; }
    
    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUsername($username) { $this->username = $username; }
    public function setPassword($password) { $this->password = $password; }
    public function setFullname($fullname) { $this->fullname = $fullname; }
    public function setAddress($address) { $this->address = $address; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setGender($gender) { $this->gender = $gender; }
    public function setBirthday($birthday) { $this->birthday = $birthday; }
    
    // Convert to array for session storage
    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'fullname' => $this->fullname,
            'address' => $this->address,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birthday' => $this->birthday
        ];
    }
    
    // Create Customer from array
    public static function fromArray($data) {
        return new Customer(
            $data['id'],
            $data['username'],
            $data['password'],
            $data['fullname'],
            $data['address'],
            $data['phone'],
            $data['gender'],
            $data['birthday']
        );
    }
    
    // Display customer info (for debugging)
    public function displayInfo() {
        return "ID: {$this->id}, Username: {$this->username}, Fullname: {$this->fullname}";
    }
}
?>
