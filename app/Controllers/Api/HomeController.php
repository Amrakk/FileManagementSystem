<?php

    namespace App\Controllers\Api;
    
    use App\Models\Auth\Account;
    use App\Models\Auth\Profile;

    class HomeController {
        
        public static function getProfile($id) {
            $result = Profile::getProfile($id);
            if($result == null) return json_encode(array('code' => 1, 'message' => 'Profile not found'));

            $data = array('code' => 0, 'message' => 'Profile found', 
                          'data' => array(
                            'name' => $result->getName(),
                            'email' => $result->getEmail(),
                            'phone_number' => $result->getPhone_number(),
                            'role' => $result->getRole()
                          ));

            return json_encode($data);
        }
    }

        
?>