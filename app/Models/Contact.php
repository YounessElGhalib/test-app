<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contact';

    protected $fillable = ["prenom", "nom", "e_mail", "cle", "telephone_fixe", "service", "fonction"];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Contact $contact) {
            $contact->cle = Str::random(32);
            $contact->telephone_fixe = '+33600000000';
            $contact->service = '';
            $contact->fonction = 'fonction';
        });
    }

    public function setNomAttribute($value)
    {
        $this->attributes["nom"] = ucwords($value);
    }

    public function setPrenomAttribute($value)
    {
        $this->attributes["prenom"] = ucwords($value);
    }

    public function setEMailAttribute($value)
    {
        $this->attributes["e_mail"] = Str::lower($value);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function scopeSearch(Builder $builder, $search)
    {
        return $builder->when($search, function ($builder) use ($search) {
            $builder->whereHas('organisation', function ($builder) use ($search) {
                $builder->where('nom', 'like', '%' . $search . '%');
            })
                ->orWhere('contact.nom', 'like', '%' . $search . '%')
                ->orWhere('contact.prenom', 'like', '%' . $search . '%');
        });
    }

    public function scopeOrderByKey($query, $key, $order)
    {
        return $query->join('organisation', 'organisation.id', '=', 'contact.organisation_id')
            ->orderBy($key, $order)
            ->select('organisation.*', 'contact.*');
    }

    public static function checkExistContact($nom, $prenom)
    {
        return Contact::where('nom', $nom)->where('prenom', $prenom)->exists();
    }
}
