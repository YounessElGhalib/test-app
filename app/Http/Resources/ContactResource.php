<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->e_mail,
            'nom_entreprise' => $this->organisation->nom,
            'adresse' => $this->organisation->adresse,
            'code_postal' => $this->organisation->code_postal,
            'ville' => $this->organisation->ville,
            'statut' => ucwords($this->organisation->statut),
        ];
    }
}
