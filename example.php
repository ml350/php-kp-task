<?php
class User {
    public string $email;
    public string $password;
    public string $password2;

    public function __construct(string $email, string $password, string $password2 ="") {
        $this->email      = $email;
        $this->password   = $password;
        $this->password2  = $password2;
    }
}

class Json {
    public static function format($a,$status){
        return json_encode([ 'success' => $status, 'error' => $a ]);
    }
}

class Validate {
    public Json $json;
    public User $user;

    public array $errors;

    public function __construct(User $user)
    {
        $this->json = new Json();
        $this->user = $user;

        $this->errors = []; //array for storing all error messages
    }

    //quick way to call full validation by passing a user object
    public static function now(User $user){
        $validate = new self($user);
        $validate->full();
    }

    // Do full validation
    public function full(){
        $this->email();
        $this->passwords();
        // echo json response with current errors
        $this->current();
    }

    //return a response after validation has been completed
    //called it current because calls reponse based on the current state of the object
    public function current(){
        if($this->_has_errors()){
            $message = $this->errors;
        }else{
            $message = "Success";
        }
        $this->_response($message,!$this->_has_errors());
    }

    private function _has_errors(){
        if(count($this->errors) > 0){
            return true;
        }else{
            return false;
        }
    }

    public function email()
    {
        if (empty($this->user->email)) {
            $this->errors[] = "No email set.";
            return false;
        }else{
            return true;
        }
    }

    //validates the passwords
    public function passwords(){
        $this->password();
        $this->password2();
        $this->password_match();
    }

    public function password(){
        if (!$this->_pasword_length($this->user->password)) {
            $this->errors[] = "Password not set or is too short.";
            return false;
        }else{
            return true;
        }
    }

    public function password2(){
        if (!$this->_pasword_length($this->user->password2)) {
            $this->errors[] = "Password 2 not set or is too short.";
            return false;
        }else{
            return true;
        }
    }

    //function to check a password length
    private function _pasword_length(string $password, int $lenght = 8){
        if (empty($password) || mb_strlen($password) < $lenght) {
            return false;
        }else{
            return true;
        }
    }

    public function password_match(){
        if ($this->user->password !== $this->user->password2) {
            $this->errors[] = "Passwords do not match";
            return false;
        }else{
            return true;
        }
    }

    // Function to echo json response and exit
    private function _response($message, bool $status){
        echo Json::format($message,$status);
        exit;
    }
}

//Example usage
$user = new User($_REQUEST['email'], $_REQUEST['password'], $_REQUEST['password2']);
Validate::now($user);
// OR
$validate = new Validate($user);
$validate->full();
//OR partial validation (passwords only)
$validate->passwords();
$validate->current();
