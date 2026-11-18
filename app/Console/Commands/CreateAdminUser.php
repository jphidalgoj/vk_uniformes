<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {--name= : Nombre del administrador} {--email= : Correo electrónico} {--password= : Contraseña}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea o actualiza un usuario administrador para el panel de VK Uniformes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nombre del administrador', 'Administrador VK');
        $email = $this->option('email') ?: $this->ask('Correo electrónico');

        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Debes proporcionar un correo electrónico válido.');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Contraseña (mínimo 8 caracteres)');

        if (! $password || strlen($password) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        $this->info("¡Usuario administrador [{$user->email}] configurado correctamente!");

        return self::SUCCESS;
    }
}
