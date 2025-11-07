<template>
  <div class="content-wrapper">
    <div class="content-container">
      <div class="page-header">
        <h2>Printout Services</h2>
      </div>
      <div class="page-content">
        <!-- Add Button and Search Bar in One Row -->
        <div class="controls-row">
          <div class="search-bar">
            <input v-model="search" type="text" placeholder="Search printout services..." />
          </div>
          <button @click="openCreateForm" class="add-button">Add New Printout Service</button>
          <button @click="openRefillPopup" class="add-button">Refill</button>
        </div>

        <table class="service-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Size</th>
              <th>Side</th>
              <th>Pages</th>
              <th>Color</th>
              <th>Price</th>
              <th>Service Charge</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(service, index) in filteredServices" :key="service.id">
              <td>{{ index + 1 }}</td>
              <td>{{ service.name }}</td>
              <td>{{ service.size }}</td>
              <td>{{ service.side }}</td>
              <td>{{ service.pages }}</td>
              <td>{{ service.color }}</td>
              <td>{{ service.price }}</td>
              <td>{{ service.service_charge }}</td>
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
              <h2>Create Printout Service</h2>
              <button @click="closeCreateModal" class="close-button">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="name">Name</label>
                <input v-model="createForm.name" type="text" id="name" placeholder="Enter service name" />
                <span v-if="createForm.errors.name" class="error">{{ createForm.errors.name }}</span>
              </div>
              <div class="form-group">
                <label for="size">Size</label>
                <select v-model="createForm.size" id="size">
                  <option value="">Select Size</option>
                  <option value="A4">A4</option>
                  <option value="A3">A3</option>
                  <option value="LG">LG</option>
                  
                </select>
                <span v-if="createForm.errors.size" class="error">{{ createForm.errors.size }}</span>
              </div>
              <div class="form-group">
                <label for="side">Side</label>
                <select v-model="createForm.side" id="side">
                  <option value="">Select Side</option>
                  <option value="Single">Single</option>
                  <option value="Double">Double</option>
                </select>
                <span v-if="createForm.errors.side" class="error">{{ createForm.errors.side }}</span>
              </div>
              <div class="form-group">
                <label for="pages">Pages</label>
                <select v-model="createForm.pages" id="pages">
                   <option value="">Select Pages</option>
                 <option value="one">One</option>
                  <option value="1-20">1-20</option>
                  <option value="20-50">20-50</option>
                  <option value="More than 50">More than 50</option>
                </select>
                <span v-if="createForm.errors.pages" class="error">{{ createForm.errors.pages }}</span>
              </div>
              <div class="form-group">
                <label for="color">Color</label>
                <select v-model="createForm.color" id="color">
                  <option value="">Select Color</option>
                  <option value="Black & White">Black & White</option>
                  <option value="Color">Color</option>
                </select>
                <span v-if="createForm.errors.color" class="error">{{ createForm.errors.color }}</span>
              </div>
              <div class="form-group">
                <label for="price">Price</label>
                <input v-model="createForm.price" type="number" id="price" placeholder="Enter price" step="0.01" />
                <span v-if="createForm.errors.price" class="error">{{ createForm.errors.price }}</span>
              </div>
              <div class="form-group">
                <label for="service_charge">Service Charge</label>
                <input v-model="createForm.service_charge" type="number" id="service_charge" placeholder="Enter service charge" step="0.01" />
                <span v-if="createForm.errors.service_charge" class="error">{{ createForm.errors.service_charge }}</span>
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
              <h2>Edit Printout Service</h2>
              <button @click="closeEditModal" class="close-button">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="edit-name">Name</label>
                <input v-model="editForm.name" type="text" id="edit-name" placeholder="Enter service name" />
                <span v-if="editForm.errors.name" class="error">{{ editForm.errors.name }}</span>
              </div>
              <div class="form-group">
                <label for="edit-size">Size</label>
                <select v-model="editForm.size" id="edit-size">
                  <option value="">Select Size</option>
                  <option value="A4">A4</option>
                  <option value="A3">A3</option>
                  <option value="LG">LG</option>
                </select>
                <span v-if="editForm.errors.size" class="error">{{ editForm.errors.size }}</span>
              </div>
              <div class="form-group">
                <label for="edit-side">Side</label>
                <select v-model="editForm.side" id="edit-side">
                  <option value="">Select Side</option>
                  <option value="Single">Single</option>
                  <option value="Double">Double</option>
                </select>
                <span v-if="editForm.errors.side" class="error">{{ editForm.errors.side }}</span>
              </div>
              <div class="form-group">
                <label for="edit-pages">Pages</label>
                <select v-model="createForm.pages" id="pages">
                   <option value="">Select Pages</option>
                 <option value="one">One</option>
                  <option value="1-20">1-20</option>
                  <option value="20-50">20-50</option>
                  <option value="More than 50">More than 50</option>
                </select>
                <span v-if="editForm.errors.pages" class="error">{{ editForm.errors.pages }}</span>
              </div>
              <div class="form-group">
                <label for="edit-color">Color</label>
                <select v-model="editForm.color" id="edit-color">
                  <option value="">Select Color</option>
                  <option value="Black & White">Black & White</option>
                  <option value="Color">Color</option>
                </select>
                <span v-if="editForm.errors.color" class="error">{{ editForm.errors.color }}</span>
              </div>
              <div class="form-group">
                <label for="edit-price">Price</label>
                <input v-model="editForm.price" type="number" id="edit-price" placeholder="Enter price" step="0.01" />
                <span v-if="editForm.errors.price" class="error">{{ editForm.errors.price }}</span>
              </div>
              <div class="form-group">
                <label for="edit-service_charge">Service Charge </label>
                <input v-model="editForm.service_charge" type="number" id="edit-service_charge" placeholder="Enter service charge" step="0.01" />
                <span v-if="editForm.errors.service_charge" class="error">{{ editForm.errors.service_charge }}</span>
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

        <!-- Refill Modal -->
        <PrintoutRefillPopup 
          v-if="isRefillPopupOpen" 
          @close="handleCloseRefillPopup" 
          :isOpen="isRefillPopupOpen" 
          :modelValue="isRefillPopupOpen" 
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import PrintoutRefillPopup from './PrintoutRefillPopup.vue';

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isRefillPopupOpen = ref(false);
const search = ref("");
const editingService = ref(null);

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
  size: "",
  side: "",
  pages: "",
  color: "",
  price: "",
  service_charge: "",
});

// Edit form
const editForm = useForm({
  _method: 'PUT',  // Required for Laravel to recognize this as a PUT request
  name: "",
  size: "",
  side: "",
  pages: "",
  color: "",
  price: "",
  service_charge: "",
});

// Mock data - replace with actual data from API
const services = ref([]);

// Fetch services from API
const fetchServices = async () => {
  try {
    const response = await fetch('/printout-services', {
      headers: {
        'Accept': 'application/json',
      },
    });
    const data = await response.json();
    services.value = data;
  } catch (error) {
    console.error('Error fetching services:', error);
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
  createForm.post("/printout-services", {
    onSuccess: () => {
      fetchServices(); // Refresh the list
      closeCreateModal();
      createForm.reset();
    },
  });
};

const editService = (service) => {
  editingService.value = service;
  editForm.name = service.name;
  editForm.size = service.size;
  editForm.side = service.side;
  editForm.pages = service.pages;
  editForm.color = service.color;
  editForm.price = service.price;
  editForm.service_charge = service.service_charge;
  isEditModalOpen.value = true;
};

const updateForm = () => {
  if (!editingService.value) return;
  
  editForm.post(`/printout-services/${editingService.value.id}`, {
    onSuccess: () => {
      fetchServices(); // Refresh the list
      closeEditModal();
    },
    preserveScroll: true,
  });
};

const deleteService = (id) => {
  if (confirm('Are you sure you want to delete this service?')) {
    useForm({}).delete(`/printout-services/${id}`, {
      onSuccess: () => {
        fetchServices(); // Refresh the list
      },
    });
  }
};

// Add debugging logs to openRefillPopup and @close event
const openRefillPopup = () => {
  console.log("Opening refill popup...");
  isRefillPopupOpen.value = true;
};

const handleCloseRefillPopup = () => {
  console.log("Closing refill popup...");
  isRefillPopupOpen.value = false;
};
</script>

<style scoped>
.content-wrapper {
  width: 100%;
  min-height: 100%;
  background-color: #fff;
  margin-bottom: 400px;
}

.content-container {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  width: 100%;
}

.page-header {
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

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 2px solid #e0e0e0;
}

.page-header h2 {
  font-size: 28px;
  font-weight: bold;
  color: #333;
  margin: 0;
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

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #ddd;
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