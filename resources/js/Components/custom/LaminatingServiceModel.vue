<template>
  <div v-if="modalOpen" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Laminating Services</h2>
        <button @click="closeModal" class="close-button">Close</button>
      </div>
      <div class="modal-body">
        <!-- Add Button and Search Bar in One Row -->
        <div class="controls-row">
          <div class="search-bar">
            <input v-model="search" type="text" placeholder="Search laminating services..." />
          </div>
          <button @click="openCreateForm" class="add-button">Add New Laminating Service</button>
        </div>

        <table class="service-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Pouch Size</th>
              <th>Price</th>
              <th>Service Amount</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(service, index) in filteredServices" :key="service.id">
              <td>{{ index + 1 }}</td>
              <td>{{ service.name }}</td>
              <td>{{ service.pouch_size }}</td>
              <td>${{ service.price }}</td>
              <td>${{ service.service_amount }}</td>
              <td>
                <button @click="editService(service)" class="edit-button">Edit</button>
                <button @click="deleteService(service.id)" class="delete-button">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Create Modal -->
        <div v-if="isCreateModalOpen" class="modal-overlay">
          <div class="create-modal-content">
            <div class="modal-header">
              <h2>Create Laminating Service</h2>
              <button @click="closeCreateModal" class="close-button">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="name">Name</label>
                <input v-model="createForm.name" type="text" id="name" placeholder="Enter service name" />
                <span v-if="createForm.errors.name" class="error">{{ createForm.errors.name }}</span>
              </div>
              <div class="form-group">
                <label for="pouch_size">Pouch Size</label>
                <select v-model="createForm.pouch_size" id="pouch_size">
                  <option value="">Select Pouch Size</option>
                  <option value="Id">Id</option>
                  <option value="4R">4R</option>
                  <option value="A5">A5</option>
                  <option value="A4">A4</option>
                  <option value="LG">LG</option>
                  <option value="Certificate">Certificate</option>
                  <option value="A3">A3</option>
                </select>
                <span v-if="createForm.errors.pouch_size" class="error">{{ createForm.errors.pouch_size }}</span>
              </div>
              <div class="form-group">
                <label for="price">Price ($)</label>
                <input v-model="createForm.price" type="number" id="price" placeholder="Enter price" step="0.01" />
                <span v-if="createForm.errors.price" class="error">{{ createForm.errors.price }}</span>
              </div>
              <div class="form-group">
                <label for="service_amount">Service Amount ($)</label>
                <input v-model="createForm.service_amount" type="number" id="service_amount" placeholder="Enter service amount" step="0.01" />
                <span v-if="createForm.errors.service_amount" class="error">{{ createForm.errors.service_amount }}</span>
              </div>
              <div class="form-actions">
                <button @click="submitForm" class="submit-button" :disabled="createForm.processing">
                  {{ createForm.processing ? 'Creating...' : 'Create Service' }}
                </button>
                <button @click="closeCreateModal" class="cancel-button">Cancel</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="isEditModalOpen" class="modal-overlay">
          <div class="create-modal-content">
            <div class="modal-header">
              <h2>Edit Laminating Service</h2>
              <button @click="closeEditModal" class="close-button">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="edit-name">Name</label>
                <input v-model="editForm.name" type="text" id="edit-name" placeholder="Enter service name" />
                <span v-if="editForm.errors.name" class="error">{{ editForm.errors.name }}</span>
              </div>
              <div class="form-group">
                <label for="edit-pouch_size">Pouch Size</label>
                <select v-model="editForm.pouch_size" id="edit-pouch_size">
                  <option value="">Select Pouch Size</option>
                  <option value="Id">Id</option>
                  <option value="4R">4R</option>
                  <option value="A5">A5</option>
                  <option value="A4">A4</option>
                  <option value="LG">LG</option>
                  <option value="Certificate">Certificate</option>
                  <option value="A3">A3</option>
                </select>
                <span v-if="editForm.errors.pouch_size" class="error">{{ editForm.errors.pouch_size }}</span>
              </div>
              <div class="form-group">
                <label for="edit-price">Price ($)</label>
                <input v-model="editForm.price" type="number" id="edit-price" placeholder="Enter price" step="0.01" />
                <span v-if="editForm.errors.price" class="error">{{ editForm.errors.price }}</span>
              </div>
              <div class="form-group">
                <label for="edit-service_amount">Service Amount ($)</label>
                <input v-model="editForm.service_amount" type="number" id="edit-service_amount" placeholder="Enter service amount" step="0.01" />
                <span v-if="editForm.errors.service_amount" class="error">{{ editForm.errors.service_amount }}</span>
              </div>
              <div class="form-actions">
                <button @click="updateForm" class="submit-button" :disabled="editForm.processing">
                  {{ editForm.processing ? 'Updating...' : 'Update Service' }}
                </button>
                <button @click="closeEditModal" class="cancel-button">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, defineProps, defineEmits, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  open: Boolean,
});

const emit = defineEmits(['update:open']);

const modalOpen = ref(props.open);
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const search = ref("");
const editingService = ref(null);

// Watch for changes in the open prop
watch(
  () => props.open,
  (newVal) => {
    modalOpen.value = newVal;
  }
);

// Also watch local modalOpen to sync with parent
watch(
  () => modalOpen.value,
  (newVal) => {
    emit('update:open', newVal);
  }
);

const closeModal = () => {
  modalOpen.value = false;
  emit('update:open', false);
};

const openCreateForm = () => {
  isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
  isCreateModalOpen.value = false;
  createForm.reset();
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
  editingService.value = null;
  editForm.reset();
};

// Create form
const createForm = useForm({
  name: "",
  pouch_size: "",
  price: "",
  service_amount: "",
});

// Edit form
const editForm = useForm({
  name: "",
  pouch_size: "",
  price: "",
  service_amount: "",
});

// Mock data - replace with actual data from API
const services = ref([]);

// Fetch services from API
const fetchServices = async () => {
  try {
    const response = await fetch('/laminating-services', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin', // Include cookies for authentication
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    services.value = data;
  } catch (error) {
    console.error('Error fetching services:', error);
    alert('Failed to load services. Please refresh the page.');
  }
};

onMounted(() => {
  fetchServices();
});

const filteredServices = computed(() => {
  return services.value.filter((service) =>
    service.name.toLowerCase().includes(search.value.toLowerCase())
  );
});
const submitForm = () => {
  createForm.post("/laminating-services", {
    onSuccess: () => {
      fetchServices(); // Refresh the list
      closeCreateModal();
      createForm.reset();
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
    },
  });
};

const editService = async (service) => {
  try {
    const response = await fetch(`/laminating-services/${service.id}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    editingService.value = data;
    editForm.name = data.name;
    editForm.pouch_size = data.pouch_size;
    editForm.price = data.price;
    editForm.service_amount = data.service_amount;

    isEditModalOpen.value = true;
  } catch (error) {
    console.error('Error fetching service details:', error);
    alert('Failed to load service details.');
  }
};

const updateForm = () => {
  if (!editingService.value) return;
  
  editForm.put(`/laminating-services/${editingService.value.id}`, {
    onSuccess: () => {
      fetchServices(); // Refresh the list
      closeEditModal();
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
    },
  });
};

const deleteService = (id) => {
  if (confirm('Are you sure you want to delete this service?')) {
    useForm({}).delete(`/laminating-services/${id}`, {
      onSuccess: () => {
        fetchServices(); // Refresh the list
      },
      onError: (error) => {
        console.error('Delete error:', error);
        alert('Failed to delete service.');
      },
    });
  }
};
</script>

<style scoped>
.modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

.modal-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 1000px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #ddd;
}

.close-button {
  background: #ff4d4d;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.close-button:hover {
  background: #ff3333;
}

.controls-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  gap: 20px;
}

.controls-row .search-bar {
  flex: 1;
}

.controls-row .add-button {
  margin-bottom: 0;
  flex-shrink: 0;
  padding: 10px 20px;
  font-size: 14px;
}

.add-button {
  background-color: #4CAF50;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
}

.add-button:hover {
  background-color: #45a049;
}

.search-bar input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.service-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.service-table th,
.service-table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: left;
}

.service-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}

.edit-button {
  background-color: #2196F3;
  color: white;
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 5px;
}

.edit-button:hover {
  background-color: #0b7dda;
}

.delete-button {
  background-color: #f44336;
  color: white;
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.delete-button:hover {
  background-color: #da190b;
}

/* Create/Edit Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}

.create-modal-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #333;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #4CAF50;
  box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
}

.error {
  color: #f44336;
  font-size: 12px;
  margin-top: 5px;
  display: block;
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 30px;
}

.submit-button {
  background-color: #4CAF50;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.submit-button:hover:not(:disabled) {
  background-color: #45a049;
}

.submit-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.cancel-button {
  background-color: #6c757d;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.cancel-button:hover {
  background-color: #5a6268;
}
</style>