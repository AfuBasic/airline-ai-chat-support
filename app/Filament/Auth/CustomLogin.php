<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login;

class CustomLogin extends Login
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
            'user_type' => 'admin',
        ];
    }
}
