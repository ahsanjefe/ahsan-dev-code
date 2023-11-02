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
  { text: "First Name", value: "first_name" },
  { text: "Last Name", value: "last_name", sortable: false },
  { text: "Company Id", value: "company_id", sortable: false },
  { text: "email", value: "email", sortable: false },
  { text: "phone", value: "phone", sortable: false },
  { text: "Action", value: "action", width: 40 },
];

const items = ref([]);
const companiesList = ref([]);
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
  await API.getEmployees(
    data,
    (data) => {
      items.value = data.employees;
      serverItemsLength.value = data.employeesCount;
      loading.value = false;
      isEditing.value = false;
    },
    (err) => {
      toast.error(err.data.message);
    }
  );
};
const getCompaniesList = async () => {
  await API.getCompaniesList(
    (data) => {
      companiesList.value = data.companiesList;
    },
    (err) => {
      toast.error(err.data.message);
    }
  );
};

onMounted(() => {
  loadFromServer();
  getCompaniesList();
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
  first_name: "",
  last_name: "",
  company_id: "",
  email: "",
  phone: "",
});

const { form, submitting, validating, errors, hasError, validateFields } =
  useValidation<any>({
    id: {
      $value: computed(() => {
        return formData.value.id;
      }),
    },
    first_name: {
      $value: computed(() => {
        return formData.value.first_name;
      }),
      $rules: [rules.required("First Name is required")],
    },
    last_name: {
      $value: computed(() => {
        return formData.value.last_name;
      }),
    },
    email: {
      $value: computed(() => {
        return formData.value.email;
      }),
    },
    company_id: {
      $value: computed(() => {
        return formData.value.company_id;
      }),
    },
    phone: {
      $value: computed(() => {
        return formData.value.phone;
      }),
    },
  });

const reInitialize = () => {
  showModal.value = false;
  formData.value.id = "";
  formData.value.first_name = "";
  formData.value.last_name = "";
  formData.value.email = "";
  formData.value.company_id = "";
  formData.value.phone = "";
};

async function saveEvent() {
  const postData = await validateFields();
  if (isEditing.value === true) {
    await updateEmployee(postData);
  } else {
    await saveEmployee(postData);
  }
}

async function saveEmployee(postData) {
  try {
    loading.value = true;
    await API.saveEmployee(
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

async function deleteEmployee(id) {
  try {
    API.deleteEmployee(
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

async function updateEmployee(postData) {
  try {
    const postData = await validateFields();
    API.updateEmployee(
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
  formData.value.first_name = item.first_name;
  formData.value.last_name = item.last_name;
  formData.value.email = item.email;
  formData.value.company_id = item.company_id;
  formData.value.phone = item.phone;
  isEditing.value = true;
  showModal.value = true;
}
</script>

<template>
  <AuthenticatedLayout title="Employees">
    <template #header>
      <div
        class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
      >
        <h2 class="text-sm font-semibold leading-tight">Employees</h2>
        <button
          @click="openModal"
          class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-1 px-2 rounded-md shadow-md text-sm"
        >
          Add Employee
        </button>
      </div>
      <v-dialog v-model="showModal" max-width="450px">
        <v-card>
          <v-card-title class="text-h6">Add Employee</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  variant="outlined"
                  label="First Name"
                  required
                  v-model="formData.first_name"
                  :rules="formData.first_name.$errors"
                  @blur="formData.first_name.$validate()"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  variant="outlined"
                  label="Last Name"
                  required
                  v-model="formData.last_name"
                  :rules="formData.last_name.$errors"
                  @blur="formData.last_name.$validate()"
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
                <v-select
                  variant="outlined"
                  v-model="formData.company_id"
                  :items="companiesList"
                  item-title="name"
                  item-value="id"
                  label="Select a company"
                ></v-select>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  variant="outlined"
                  label="phone"
                  required
                  v-model="formData.phone"
                ></v-text-field>
              </v-col>
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
      <template #item-action="item">
        <div class="operation-wrapper">
          <TrashIcon
            class="flex-shrink-0 w-6 h-6 text-red"
            aria-hidden="true"
            @click="deleteEmployee(item.id)"
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