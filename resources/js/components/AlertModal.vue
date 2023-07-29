<template>
  <div class="fixed z-10 inset-0 overflow-y-auto" id="modal">
    <div class="flex items-center justify-center min-h-screen">
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
      ></div>
      <span class="hidden"></span>
      <div
        class="inline-block align-bottom w-[600px] bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all"
      >
        <div class="bg-white px-4">
          <div class="flex items-center space-x-4">
            <div
              class="flex items-center justify-center bg-red-100 rounded-full p-2"
            >
              <ExclamationTriangleIcon class="w-6 h-6 text-red-700" />
            </div>
            <h3 class="text-xl py-4 text-gray-700">Doublon</h3>
          </div>
          <p class="pl-14 py-6">
            {{ message }}<br />
            Etes-vous sûr de vouloir ajouter ce contact ?
          </p>
        </div>
        <div
          class="flex items-center justify-end bg-gray-100 px-4 py-4 space-x-3"
        >
          <button
            class="text-gray-700 px-2 py-1 font-medium rounded-md border border-gray-500 text-sm"
            @click="emit('closeAlertModal')"
          >
            Annuler
          </button>
          <button
            class="bg-red-700 text-white font-medium px-2 py-1 rounded-md border border-red-700 text-sm"
            @click="confirmer"
          >
            Confirmer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
  contact: {
    type: Object,
    default: {},
  },
  message: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(["closeAlertModal", "reloadData"]);

function confirmer() {
  props.contact.confirmation= true;
  axios
    .post("/add-contacts", props.contact)
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