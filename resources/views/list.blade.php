<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

    @vite('resources/css/app.css')
</head>

<body>
    <div class="p-10 bg-gray-200 h-screen">
        <h1 class="text-3xl pb-4 text-gray-700">
            Liste des contacts
        </h1>

        <div class="flex items-center justify-between">
            <input type="search" class="input" name="search" id="search" value="{{ request('search') }}"
                placeholder="Recherche..." />
            <button class="bg-cyan-500 text-white text-sm font-medium px-2 py-1 rounded-md border border-gray-500"
                id="openModal">+
                Ajouter
            </button>
        </div>
        <table class="table-auto text-sm w-full my-6 shadow-sm">
            <thead>
                <tr class="border border-gray-300">
                    <th class="py-1 pl-2 text-left w-1/3">NOM DU CONTACT</th>
                    <th class="pl-2 text-left w-1/3">SOCIETE</th>
                    <th class="pl-2 text-left w-1/6">STATUT</th>
                    <th class="w-1/6"></th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($contacts) && $contacts->count())
                    @foreach ($contacts as $contact)
                        <tr class="border border-gray-300 bg-white">
                            <td class="py-2 pl-2 w-1/3">{{ $contact->nom }} {{ $contact->prenom }}</td>
                            <td class="pl-2 w-1/3">{{ $contact->organisation->nom }}</td>
                            <td class="pl-2 text-xs w-1/6">{{ $contact->organisation->statut }}</td>
                            <td class="pl-2  w-1/6">...</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">There are no data.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {!! $contacts->links('pagination::tailwind') !!}
    </div>

    <!-- Modal content -->
    <div class="fixed z-10 inset-0 invisible overflow-y-auto" id="modal">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <span class="hidden"></span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all">
                <form name="add-contact" id="add-contact" method="post" action="{{ url('add-contact') }}">
                    @csrf
                    <div class="bg-white px-4">
                        <h3 class="text-xl py-4 text-gray-700">
                            Détail du contact
                        </h3>
                        <div class="mt-2 w-[500px] flex flex-wrap">
                            <div class="w-1/2">
                                <label>Prénom</label>
                                <input class="input" name="prenom" placeholder="Prénom" />
                            </div>
                            <div class="w-1/2">
                                <label>Nom</label>
                                <input class="input" name="nom" placeholder="Nom" />
                            </div>
                            <div class="w-full">
                                <label>E-mail</label>
                                <input class="input w-full" name="email" placeholder="E-mail" />
                            </div>
                            <div class="w-full">
                                <label>Entreprise</label>
                                <input class="input w-full" name="entreprise" placeholder="Entreprise" />
                            </div>
                            <div class="w-full">
                                <label>Adresse</label>
                                <textarea class="input w-full" name="adresse" placeholder="Adresse"></textarea>
                            </div>
                            <div class="w-1/3">
                                <label>Code postal</label>
                                <input class="input w-full" name="cp" placeholder="Code postal" />
                            </div>
                            <div class="w-2/3">
                                <label>Ville</label>
                                <input class="input w-full" name="ville" placeholder="Ville" />
                            </div>
                            <div class="w-1/2">
                                <label>Statut</label>
                                <select name="statut" class="input w-full">
                                    <option value="Lead">LEAD</option>
                                    <option value="Client">CLIENT</option>
                                    <option value="Prospect">PROSPECT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end bg-gray-50 px-4 py-4 space-x-3">
                        <button id="closeModal"
                            class="text-gray-700 px-2 py-1 font-medium rounded-md border border-gray-500 text-sm">
                            Annuler
                        </button>
                        <button type="submit"
                            class="bg-cyan-500 text-white font-medium px-2 py-1 rounded-md border border-gray-500 text-sm">
                            Valider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
    var add_btn = document.getElementById("openModal");
    var close_btn = document.getElementById("closeModal");
    var modal = document.getElementById("modal");

    add_btn.onclick = function() {
        modal.classList.remove("invisible");
    }
    close_btn.onclick = function() {
        modal.classList.add("invisible");
    }


    document.querySelector('#search').addEventListener('input', searchValue)

    function searchValue(e) {
        $value = $(this).val();
        $.ajax({
            type: 'get',
            url: "{{ url('/') }}"+"?search="+$value,
            // data: {
            //     'search': $value
            // }
        });
    }
</script>

</html>
