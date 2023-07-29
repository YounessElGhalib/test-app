<template>
  <div>
    <div class="flex items-center justify-between">
      <input
        class="input"
        name="search"
        v-model="search"
        placeholder="Recherche..."
      />
      <button
        class="bg-cyan-500 text-white text-sm font-medium px-2 py-1 rounded-md border border-gray-500"
        @click="(show_modal = true), (action_modal = 'add')"
      >
        + Ajouter
      </button>
    </div>
    <table class="table-auto text-sm w-full my-6 shadow-sm rounded-lg">
      <thead>
        <tr class="border border-gray-300 bg-gray-100">
          <th class="py-1 pl-2 text-left w-1/3">
            <div
              class="flex items-center space-x-4 cursor-pointer"
              @click="sortContacts('contact.nom')"
            >
              <p>NOM DU CONTACT</p>
              <ChevronUpIcon
                v-if="sort_by === 'contact.nom' && order_by === 'asc'"
                class="w-5 h-5 text-gray-700"
              />
              <ChevronDownIcon v-else class="w-5 h-5 text-gray-700" />
            </div>
          </th>
          <th class="pl-2 text-left w-1/3">
            <div
              class="flex items-center space-x-4 cursor-pointer"
              @click="sortContacts('organisation.nom')"
            >
              <p>SOCIETE</p>
              <ChevronUpIcon
                v-if="sort_by === 'organisation.nom' && order_by === 'asc'"
                class="w-5 h-5 text-gray-700"
              />
              <ChevronDownIcon v-else class="w-5 h-5 text-gray-700" />
            </div>
          </th>
          <th class="pl-2 text-left w-1/6">
            <div
              class="flex items-center space-x-4 cursor-pointer"
              @click="sortContacts('organisation.statut')"
            >
              <p>STATUT</p>
              <ChevronUpIcon
                v-if="sort_by === 'organisation.statut' && order_by === 'asc'"
                class="w-5 h-5 text-gray-700"
              />
              <ChevronDownIcon v-else class="w-5 h-5 text-gray-700" />
            </div>
          </th>
          <th class="w-1/6"></th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="contact in contacts"
          :key="contact"
          class="border border-gray-300 bg-white"
        >
          <td class="py-2 pl-2 w-1/3">
            {{ contact.nom }} {{ contact.prenom }}
          </td>
          <td class="py-2 pl-2 w-1/3">
            {{ contact.nom_entreprise }}
          </td>
          <td class="py-2 pl-2 text-xs w-1/6">
            <div :class="`${contact.statut}-statut`">{{ contact.statut }}</div>
          </td>
          <td class="pl-2 w-1/6">
            <div class="flex items-center justify-center space-x-4">
              <EyeIcon
                class="w-5 h-5 text-gray-700 cursor-pointer"
                @click="
                  (updating_contact = contact),
                    (action_modal = 'show'),
                    (show_modal = true)
                "
              />
              <PencilIcon
                class="w-5 h-5 text-gray-700 cursor-pointer"
                @click="
                  (updating_contact = contact),
                    (action_modal = 'update'),
                    (show_modal = true)
                "
              />
              <TrashIcon
                class="w-5 h-5 text-red-700 cursor-pointer"
                @click="
                  (show_confirmation_modal = true),
                    (deleting_contact_id = contact.id)
                "
              />
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="!contacts.length" class="w-full text-center">
      There are no data.
    </div>

    <div class="flex justify-end">
      <div
        class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 cursor-pointer"
        :class="{
          'rounded-l-md': page == 1,
          'rounded-r-md': page == last_page,
          'text-gray-500': page == current_page,
        }"
        v-for="page in last_page"
        :key="page"
        @click="getContacts(page, search)"
      >
        {{ page }}
      </div>
    </div>
  </div>

  <Add
    v-show="show_modal"
    :contact="updating_contact"
    :action="action_modal"
    @close="
      (show_modal = false), (updating_contact = {}), (action_modal = 'add')
    "
    @reloadData="
      getContacts(current_page, search),
        (show_modal = false),
        (updating_contact = {}),
        (action_modal = 'add')
    "
    @showAlertModal="showAlertModal"
  />

  <ConfirmationModal
    v-show="show_confirmation_modal"
    :id="deleting_contact_id"
    @closeConfirmationModal="
      (show_confirmation_modal = false), (deleting_contact_id = 0)
    "
    @reloadData="
      getContacts(current_page, search),
        (show_confirmation_modal = false),
        (deleting_contact_id = 0)
    "
  />

  <AlertModal
    v-show="show_alert_modal"
    :contact="updating_contact"
    :message="alert_message"
    @closeAlertModal="show_alert_modal = false"
    @reloadData="
      getContacts(current_page, search),
        (show_alert_modal = false),
        (updating_contact = {}),
        (show_modal = false)
    "
  />
</template>

<script setup>
import Add from "./Add.vue";
import ConfirmationModal from "./ConfirmationModal.vue";
import AlertModal from "./AlertModal.vue";
import { ref, watch, onMounted } from "vue";
import {
  EyeIcon,
  PencilIcon,
  TrashIcon,
  ChevronDownIcon,
  ChevronUpIcon,
} from "@heroicons/vue/24/outline";

const contacts = ref({});
const search = ref("");
const current_page = ref(1);
const last_page = ref(1);
const show_modal = ref(false);
const updating_contact = ref({});
const action_modal = ref("add");
const show_confirmation_modal = ref(false);
const deleting_contact_id = ref(0);
const sort_by = ref(null);
const order_by = ref("desc");
const show_alert_modal = ref(false);
const alert_message = ref("");

onMounted(() => {
  getContacts();
});

function sortContacts(key) {
  order_by.value =
    sort_by.value === key && order_by.value != "asc" ? "asc" : "desc";
  sort_by.value = key;
  getContacts(1, search.value.toString());
}

function getContacts(page = 1, str = "") {
  axios
    .get("/contacts", {
      params: {
        page: page,
        search: str.toString(),
        key: sort_by.value,
        order: order_by.value,
      },
    })
    .then((res) => {
      contacts.value = res.data.data;
      current_page.value = res.data.meta.current_page;
      last_page.value = res.data.meta.last_page;
    })
    .catch((error) => {
      console.log(error.res.data);
    });
}

function showAlertModal(preload) {
  alert_message.value = preload.message;
  updating_contact.value = preload.contact;
  show_alert_modal.value = true;
}

watch(search, (val) => {
  getContacts(1, val.toString());
});
</script>

<style>
</style>