<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'category',
        'description'
    ];

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('setting_type', $type);
    }

    // Static methods for easy access
    public static function getValue($key, $default = null)
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function setValue($key, $value, $type = 'string', $category = 'General', $description = null)
    {
        return static::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_type' => $type,
                'category' => $category,
                'description' => $description
            ]
        );
    }

    public static function getByCategory($category)
    {
        return static::where('category', $category)->get();
    }

    // Accessors
    public function getTypedValueAttribute()
    {
        switch ($this->setting_type) {
            case 'boolean':
                return (bool) $this->setting_value;
            case 'integer':
                return (int) $this->setting_value;
            case 'float':
                return (float) $this->setting_value;
            case 'array':
                return json_decode($this->setting_value, true);
            case 'json':
                return json_decode($this->setting_value, true);
            default:
                return $this->setting_value;
        }
    }
}
