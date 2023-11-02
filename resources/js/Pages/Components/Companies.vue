<script setup lang='ts'>
import { ref, computed, watch, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Field, useValidation } from "vue3-form-validation";
import { rules } from "@/types";
import axios from "axios";
import API from "@/api";
import { TrashIcon, PencilIcon } from "@heroicons/vue/outline";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const headers = [
  { text: "Name", value: "name" },
  { text: "Email", value: "email", sortable: false },
  { text: "Logo", value: "logo", sortable: false, width: 20 },
  { text: "Website", value: "website", sortable: false },
  { text: "Action", value: "action", width: 40 },
];

const items = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const serverItemsLength = ref(0);
const serverOptions = ref({
  page: 1,
  rowsPerPage: 10,
});

const loading = ref(false);

const loadFromServer = async () => {
  if (!loading.value) {
    loading.value = true;
  }
  const data = serverOptions.value;
  await API.getCompanies(
    data,
    (data) => {
      items.value = data.companies;
      serverItemsLength.value = data.companiesCount;
      loading.value = false;
      isEditing.value = false;
    },
    (err) => {
      toast.error(err.data.message);
    }
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

const openModal = () => {
  showModal.value = true;
};
const closeModal = () => {
  showModal.value = false;
};

var formData = ref({
  id: "",
  name: "",
  email: "",
  website: "",
  logo: null,
});

const { form, submitting, validating, errors, hasError, validateFields } =
  useValidation<any>({
    id: {
      $value: computed(() => {
        return formData.value.id;
      }),
    },
    name: {
      $value: computed(() => {
        return formData.value.name;
      }),
      $rules: [rules.required("Name is required")],
    },
    email: {
      $value: computed(() => {
        return formData.value.email;
      }),
    },
    website: {
      $value: computed(() => {
        return formData.value.website;
      }),
    },
    logo: {
      $value: computed(() => {
        return formData.value.logo;
      }),
    },
    _method: {
      $value: "PUT",
    },
  });

const reInitialize = () => {
  showModal.value = false;
  formData.value.id = "";
  formData.value.name = "";
  formData.value.email = "";
  formData.value.website = "";
  formData.value.logo = "";
};

async function saveEvent() {
  const postData = await validateFields();
  if (isEditing.value === true) {
    postData._method = 'PUT'
    await updateCompany(postData);
  } else {
    delete postData._method
    await saveCompany(postData);
  }
}

async function saveCompany(postData) {
  try {
    loading.value = true;
    await API.saveCompany(
      postData,
      (data) => {
        if (data.status === 200) {
          toast.success(data.message);
          reInitialize();
          loadFromServer();
        } else {
          toast.error(data.message);
        }
      },
      (err) => {
        toast.error(err.data.message);
      }
    );
  } catch (error) {}
}

async function deleteCompany(id) {
  try {
    API.deleteCompany(
      { id: id },
      (data) => {
        if (data.status === 200) {
          toast.success(data.message);
          loadFromServer();
        } else {
          toast.error(data.message);
        }
      },
      (err) => {
        toast.error(err.data.message);
      }
    );
  } catch (error) {}
}

async function updateCompany(postData) {
  try {
    const postData = await validateFields();
    API.updateCompany(
      postData,
      (data) => {
        if (data.status === 200) {
          toast.success(data.message);
          reInitialize();
          loadFromServer();
        } else {
          toast.error(data.message);
        }
      },
      (err) => {
        toast.error(err.data.message);
      }
    );
  } catch (error) {}
}

async function openEditModal(item) {
  formData.value.id = item.id;
  formData.value.name = item.name;
  formData.value.email = item.email;
  formData.value.website = item.website;
  isEditing.value = true;
  showModal.value = true;
}

const getLogoUrl = (filename) => {
  if (!filename.includes("company_logo")) {
    return filename;
  } else {
    return `http://localhost:8000/storage/${filename}`;
  }
};
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
      <v-dialog v-model="showModal" max-width="450px">
        <v-card>
          <v-card-title class="text-h6">Add Company</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  variant="outlined"
                  label="Name"
                  required
                  v-model="formData.name"
                  :rules="formData.name.$errors"
                  @blur="formData.name.$validate()"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  variant="outlined"
                  label="Email"
                  required
                  v-model="formData.email"
                  :rules="formData.email.$errors"
                  @blur="formData.email.$validate()"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  variant="outlined"
                  label="Website"
                  required
                  v-model="formData.website"
                  :rules="formData.website.$errors"
                  @blur="formData.website.$validate()"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-file-input
              label="Upload Logo"
              variant="outlined"
              v-model="formData.logo"
              accept="image/png, image/jpeg, image/bmp"
              prepend-icon=""
            ></v-file-input>
            <v-row>
              <v-col cols="12"> </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-row align="left" class="mx-0 mt-4 mb-2">
              <v-spacer></v-spacer>
              <v-btn @click="closeModal" color="blue" dark> Close </v-btn>
              <v-btn
                class="text-white text-none mr-3"
                color="blue-darken-3"
                min-width="92"
                variant="flat"
                @click="saveEvent()"
              >
                Save
              </v-btn>
            </v-row>
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
          <img
            class="logo"
            :src="getLogoUrl(item.logo)"
            alt=""
            width="100"
            height="100"
          />
        </div>
      </template>
      <template #item-website="item">
        <div class="player-wrapper">
          <a target="_blank" :href="item.website">{{ item.website }}</a>
        </div>
      </template>
      <template #item-action="item">
        <div class="operation-wrapper">
          <TrashIcon
            class="flex-shrink-0 w-6 h-6 text-red"
            aria-hidden="true"
            @click="deleteCompany(item.id)"
          />

          <PencilIcon
            class="flex-shrink-0 w-6 h-6 ml-2 text-primary"
            aria-hidden="true"
            @click="openEditModal(item)"
          />
        </div>
      </template>
    </EasyDataTable>
  </AuthenticatedLayout>
</template>

<style scoped lang="scss">
.v-col-6,
.v-col-3,
.v-col-12 {
  padding-bottom: 0px !important;
  padding-top: 0px !important;
}

.operation-wrapper {
  display: flex;
  & svg {
    cursor: pointer;
  }
}
</style>