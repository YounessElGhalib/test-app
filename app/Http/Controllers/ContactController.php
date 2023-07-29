<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\Organisation;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function list(Request $request)
    {
        $key = !empty($request['key']) ? $request['key'] : 'contact.created_at';
        $order = !empty($request['order']) ? $request['order'] : 'desc';
        $value = !empty($request['search']) ? $request['search'] : '';

        return ContactResource::collection(
            Contact::search($value)
                ->orderByKey($key, $order)->paginate(10)
        );
    }

    public function store(ContactRequest $request)
    {
        if (!$request->confirmation) {
            $exist_contact = Contact::checkExistContact($request->nom, $request->prenom);
            $exist_organisation = Organisation::checkExistOrganisation($request->nom_entreprise);

            if ($exist_contact)
                return response()->json('exist contact', 203);
            if ($exist_organisation)
                return response()->json('exist organisation', 203);
        }

        $organisation = Organisation::create([
            'nom' => $request->nom_entreprise,
            'adresse' => $request->adresse,
            'code_postal' => $request->code_postal,
            'ville' => $request->ville,
            'statut' => $request->statut
        ]);

        $organisation->contacts()->create([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'e_mail' => $request->email,
        ]);

        return response()->json('contact has been added successfully', 200);
    }

    public function update(Contact $contact, ContactRequest $request)
    {
        $contact->organisation->update([
            'nom' => $request->nom_entreprise,
            'adresse' => $request->adresse,
            'code_postal' => $request->code_postal,
            'ville' => $request->ville,
            'statut' => $request->statut
        ]);

        $contact->update([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'e_mail' => $request->email,
        ]);

        return response()->json('contact has been updated successfully', 200);
    }

    public function delete(Contact $contact)
    {
        $contact->organisation->delete();
        $contact->delete();

        return response()->json('contact has been deleted successfully', 200);
    }
}
