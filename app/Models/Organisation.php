<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organisation extends Model
{
    use HasFactory;

    protected $table = 'organisation';

    protected $fillable = ["nom", "adresse", "cle", "code_postal", "ville", "statut"];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Organisation $organisation) {
            $organisation->cle = Str::random(32);
        });
    }

    public function setNomAttribute($value)
    {
        $this->attributes["nom"] = ucwords($value);
    }

    public function setVilleAttribute($value)
    {
        $this->attributes["ville"] = ucwords($value);
    }

    public function setStatutAttribute($value)
    {
        $this->attributes["statut"] = Str::upper($value);
    }

    public function getStatutAttribute($value)
    {
        return Str::lower($value);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public static function checkExistOrganisation($nom)
    {
        return Organisation::where('nom', $nom)->exists();
    }
}
