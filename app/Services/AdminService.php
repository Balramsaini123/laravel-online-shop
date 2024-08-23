<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AdminService {

    public function __construct(
        protected UserRepository $userRepository
    ){
    }

}
