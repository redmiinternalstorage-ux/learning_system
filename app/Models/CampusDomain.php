<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CampusDomain extends Model
{
    protected $fillable = ['domain', 'college_name', 'is_active'];

    public static function isAllowed(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, '@'), 1));
        return static::where('domain', $domain)->where('is_active', true)->exists();
    }

    public static function findByEmail(string $email): ?static
    {
        $domain = strtolower(substr(strrchr($email, '@'), 1));
        return static::where('domain', $domain)->where('is_active', true)->first();
    }
}
