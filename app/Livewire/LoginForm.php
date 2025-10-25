<?php


namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginForm extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function authenticate()
    {
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (! Auth::guard(Filament::getAuthGuard())->attempt($credentials, $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function render()
    {
        return view('livewire.login-form');
    }
}
