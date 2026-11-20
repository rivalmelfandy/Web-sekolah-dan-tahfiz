<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nip_nis' => fake()->unique()->numerify('######'),
            'nama_lengkap' => fake()->name(),
            'no_hp' => fake()->optional()->numerify('08##########'),
            'role' => UserRole::Siswa,
            'password' => static::$password ??= Hash::make('password'),
            'halaqah_id' => null,
        ];
    }
}
