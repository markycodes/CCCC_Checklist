<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'program'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ROLE_STUDENT = 'student';

    public const ROLE_PROGRAM_HEAD = 'program_head';

    public const ROLE_ADMINISTRATOR = 'administrator';

    public const ROLE_REGISTRAR = 'registrar';

    public const PROGRAM_BPA = 'BPA';

    public const PROGRAM_BSCRIM = 'BSCRIM';

    public const PROGRAM_BSISM = 'BSISM';

    public const PROGRAM_BSENTREP = 'BSENTREP';

    public const PROGRAM_BSOA = 'BSOA';

    public static function prospectusPrograms(): array
    {
        return [
            self::PROGRAM_BPA,
            self::PROGRAM_BSCRIM,
            self::PROGRAM_BSISM,
            self::PROGRAM_BSENTREP,
            self::PROGRAM_BSOA,
        ];
    }

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isProgramHead(): bool
    {
        return $this->role === self::ROLE_PROGRAM_HEAD;
    }

    public function isAdministrator(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    public function isRegistrar(): bool
    {
        return $this->role === self::ROLE_REGISTRAR;
    }

    public function canManageStudentRecords(): bool
    {
        return $this->isAdministrator()
            || $this->isRegistrar()
            || $this->isProgramHead();
    }

    public function canViewAllPrograms(): bool
    {
        return $this->isAdministrator() || $this->isRegistrar();
    }

    public function canEnterGrades(): bool
    {
        return $this->isAdministrator() || $this->isRegistrar() || $this->isProgramHead();
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
