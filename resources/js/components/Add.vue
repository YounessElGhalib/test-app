<template>
  <div class="fixed z-10 inset-0 overflow-y-auto" id="modal">
    <div class="flex items-center justify-center min-h-screen">
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
      ></div>
      <span class="hidden"></span>
      <div
        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all"
      >
        <div class="bg-white px-4">
          <h3 class="text-xl py-4 text-gray-700">Détail du contact</h3>
          <div class="mt-2 w-[600px] flex flex-wrap">
            <div class="w-1/2 pr-4 pb-4">
              <label>Prénom</label>
              <input
                class="input w-full"
                placeholder="Prénom"
                v-model="contact.prenom"
                :disabled="action == 'show'"
              />
            </div>
            <div class="w-1/2 pb-4">
              <label>Nom</label>
              <input
                class="input w-full"
                placeholder="Nom"
                v-model="contact.nom"
                :disabled="action == 'show'"
              />
            </div>
            <div class="w-full pb-4">
              <label>E-mail</label>
              <input
                class="input w-full"
                placeholder="E-mail"
                v-model="contact.email"
                :disabled="action == 'show'"
              />
            </div>
            <div class="w-full pb-4">
              <label>Entreprise</label>
              <input
                class="input w-full"
                placeholder="Entreprise"
                v-model="contact.nom_entreprise"
                :disabled="action == 'show'"
              />
            </div>
            <div class="w-full pb-4">
              <label>Adresse</label>
              <textarea
                class="input w-full"
                placeholder="Adresse"
                v-model="contact.adresse"
                :disabled="action == 'show'"
              ></textarea>
            </div>
            <div class="w-1/3 pb-4">
              <label>Code postal</label>
              <input
                class="input w-full"
                placeholder="Code postal"
                v-model="contact.code_postal"
                :disabled="action == 'show'"
              />
            </div>
            <div class="w-2/3 pl-4 pb-4">
              <label>Ville</label>
              <input
                class="input w-full"
                placeholder="Ville"
                v-model="contact.ville"
                :disabled="action == 'show'"
              />
            </div>
            <div class="w-1/2 pb-4">
              <label>Statut</label>
              <select
                class="input w-full"
                v-model="contact.statut"
                :disabled="action == 'show'"
              >
                <option value="Lead" selected>Lead</option>
                <option value="Client">Client</option>
                <option value="Prospect">Prospect</option>
              </select>
            </div>
          </div>
        </div>
        <div
          class="flex items-center justify-end bg-gray-100 px-4 py-4 space-x-3"
        >
          <button
            class="text-gray-700 px-2 py-1 font-medium rounded-md border border-gray-500 text-sm"
            @click="emit('close')"
          >
            Annuler
          </button>
          <button
            v-if="action == 'add'"
            class="bg-cyan-500 text-white font-medium px-2 py-1 rounded-md border border-gray-500 text-sm"
            @click="addContact"
          >
            Valider
          </button>
          <button
            v-if="action == 'update'"
            class="bg-cyan-500 text-white font-medium px-2 py-1 rounded-md border border-gray-500 text-sm"
            @click="updateContact"
          >
            Modifier
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  action: {
    type: String,
    default: "add",
  },
  contact: {
    type: Object,
    default: {},
  },
});

const emit = defineEmits(["close", "reloadData", "showAlertModal"]);

function addContact() {
  axios
    .post("/add-contacts", props.contact)
    .then((res) => {
      if (res.status === 203 && res.data === "exist contact") {
        emit("showAlertModal", {
          message: "Un contact existe déjà avec le même prénom et le même nom.",
          contact: props.contact,
        });
      } else if (res.status === 203 && res.data === "exist organisation") {
        emit("showAlertModal", {
          message: "Un contact existe déjà avec le même nom de entreprise.",
          contact: props.contact,
        });
      }
      if (res.status === 200) {
        emit("reloadData");
      }
    })
    .catch((error) => {
      console.log(error.res.data);
    });
}

function updateContact() {
  axios
    .put(`/update-contacts/${props.contact.id}`, props.contact)
    .then((res) => {
      emit("reloadData");
    })
    .catch((error) => {
      console.log(error.res.data);
    });
}
</script>

<style>
</style>