<script setup>
import { ref, computed, watch, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import axios from "axios";
import API from "@/api";

const headers = [
  { text: "Name", value: "name" },
  { text: "Email", value: "email", sortable: false },
  { text: "Logo", value: "logo", sortable: false, width: 20 },
  { text: "Website", value: "website", sortable: false },
];

const items = ref([]);
const showModal = ref(false);
const serverItemsLength = ref(0);
const serverOptions = ref({
  page: 1,
  rowsPerPage: 5,
});

const loading = ref(false);

const loadFromServer = async () => {
  loading.value = true;
  const data = serverOptions.value;
  await API.getCompanies(
    data,
    (data) => {
      items.value = data.companies;
      serverItemsLength.value = data.companiesCount;
      loading.value = false;
    },
    (err) => {}
  );
};

onMounted(() => {
  loadFromServer();
});

watch(
  serverOptions,
  (value) => {
    loadFromServer();
  },
  { deep: true }
);

function openModal() {
  showModal.value = true;
}
function closeModal() {
  showModal.value = false;
}
</script>

<template>
  <AuthenticatedLayout title="Companies">
    <template #header>
      <div
        class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
      >
        <h2 class="text-sm font-semibold leading-tight">Companies</h2>
        <button
          @click="openModal"
          class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-1 px-2 rounded-md shadow-md text-sm"
        >
          Add Company
        </button>
      </div>
      <v-dialog v-model="showModal" max-width="600px">
        <v-card>
          <v-card-title class="text-h6">Add Company</v-card-title>
          <v-card-text>
          </v-card-text>
            <p>succ</p>
          <v-card-actions>
            <v-btn @click="closeModal" color="blue" dark> Close </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </template>
    <EasyDataTable
      alternating
      v-model:server-options="serverOptions"
      :headers="headers"
      show-index
      fixed-index
      :index-column-width="20"
      :items="items"
      :server-items-length="serverItemsLength"
      :loading="loading"
      buttons-pagination
    >
      <template #item-logo="item">
        <div class="player-wrapper">
          <img class="logo" :src="item.logo" alt="" width="50" height="50" />
        </div>
      </template>
      <template #item-website="item">
        <div class="player-wrapper">
          <a target="_blank" :href="item.website">{{ item.website }}</a>
        </div>
      </template>
    </EasyDataTable>
  </AuthenticatedLayout>
</template>
