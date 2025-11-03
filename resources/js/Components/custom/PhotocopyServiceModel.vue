<template>
  <div class="page-content">
    <div class="content-wrapper">
      <div class="page-header">
        <h2>Photocopy Services</h2>
      </div>
      <div class="page-body">
        <!-- Add Button and Search Bar in One Row -->
        <div class="controls-row">
          <div class="search-bar">
            <input v-model="search" type="text" placeholder="Search photocopy services..." />
          </div>
          <button @click="openCreateForm" class="add-button">Add New Photocopy Service</button>
          <button @click="openRefillModal" class="add-button">Refill</button>
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
              <h2>Create Photocopy Service</h2>
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
                  <option value="Single">Single Side</option>
                  <option value="Double">Double Side</option>
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
              <h2>Edit Photocopy Service</h2>
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
                  <option value="LG">Lg</option>
                 
                </select>
                <span v-if="editForm.errors.size" class="error">{{ editForm.errors.size }}</span>
              </div>
              <div class="form-group">
                <label for="edit-side">Side</label>
                <select v-model="editForm.side" id="edit-side">
                  <option value="">Select Side</option>
                  <option value="Single">Single Side</option>
                  <option value="Double">Double Side</option>
                </select>
                <span v-if="editForm.errors.side" class="error">{{ editForm.errors.side }}</span>
              </div>
              <div class="form-group">
                <label for="edit-pages">Pages</label>
                <select v-model="editForm.pages" id="edit-pages">
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
                <label for="edit-service_charge">Service Charge</label>
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
        <RefillPopup
          :isRefillModalOpen="isRefillModalOpen"
          @close="closeRefillModal"
          @refill-submitted="handleRefillSubmit"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import RefillPopup from "./RefillPopup.vue";

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isRefillModalOpen = ref(false);
const search = ref("");
const services = ref([]);
const editingService = ref(null);

const openCreateForm = () => {
  isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
  isCreateModalOpen.value = false;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};

const closeRefillModal = () => {
  isRefillModalOpen.value = false;
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
  name: "",
  size: "",
  side: "",
  pages: "",
  color: "",
  price: "",
  service_charge: "",
});

const products = ref([]);
const selectedProductId = ref(null);
const showError = ref(false);
const refillForm = ref({
  product_id: null,
  product_name: '',
  quantity: '',
  current_stock: null
});
const pagination = ref({});

// Computed property to get the selected product details
const selectedProduct = computed(() => {
  return products.value.find(p => p.id === selectedProductId.value) || null;
});

// Fetch services from API
const fetchServices = async () => {
  try {
    const response = await fetch('/photocopy-services', {
      headers: {
        'Accept': 'application/json',
      },
    });
    const data = await response.json();

    // Handle response with `photocopyServices` property
    if (data?.success && Array.isArray(data.photocopyServices)) {
      services.value = data.photocopyServices;
    } else if (Array.isArray(data)) {
      services.value = data;
    } else if (Array.isArray(data.data)) {
      services.value = data.data;
    } else {
      console.error('Invalid data format received:', data);
      services.value = []; // Reset to empty array if invalid data
    }
  } catch (error) {
    console.error('Error fetching services:', error);
    services.value = []; // Reset to empty array on error
  }
};

// Fetch products for refill
const fetchProducts = async (url = '/api/products') => {
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token:', token); // Debugging CSRF token

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({
        search: search.value,
        sort: '',
        color: '',
        size: '',
        stockStatus: '',
        selectedCategory: ''
      })
    });

    if (!response.ok) {
      console.error('HTTP Error:', response.status); // Debugging response status
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log('API Response:', data); // Debugging API response

    if (data && data.products) {
      products.value = data.products.data || [];
      pagination.value = {
        prev_page_url: data.products.prev_page_url,
        next_page_url: data.products.next_page_url
      };
    }
  } catch (error) {
    console.error('Error fetching products:', error);
    alert('Failed to load products. Please try refreshing the page.');
  }
};

onMounted(() => {
  fetchServices();
  fetchProducts();
});

const filteredServices = computed(() => {
  if (!Array.isArray(services.value)) {
    return []; // Return an empty array if services is not an array
  }
  return services.value.filter((service) =>
    service?.name?.toLowerCase().includes(search.value.toLowerCase()) || false
  );
});

const submitForm = () => {
  createForm.post("/photocopy-services", {
    onSuccess: () => {
      fetchServices();
      closeCreateModal();
      createForm.reset();
    },
  });
};

const editService = async (service) => {
  try {
    const response = await fetch(`/photocopy-services/${service.id}`, {
      headers: {
        'Accept': 'application/json',
      },
    });
    const data = await response.json();

    editingService.value = data;
    editForm.name = data.name;
    editForm.size = data.size;
    editForm.side = data.side;
    editForm.pages = data.pages;
    editForm.color = data.color;
    editForm.price = data.price;
    editForm.service_charge = data.service_charge;

    isEditModalOpen.value = true;
  } catch (error) {
    console.error('Error fetching service details:', error);
  }
};

const updateForm = () => {
  if (!editingService.value) return;
  
  editForm.put(`/photocopy-services/${editingService.value.id}`, {
    onSuccess: () => {
      fetchServices();
      closeEditModal();
    },
  });
};

const deleteService = (id) => {
  if (confirm('Are you sure you want to delete this service?')) {
    useForm({}).delete(`/photocopy-services/${id}`, {
      onSuccess: () => {
        fetchServices();
      },
    });
  }
};

const openRefillModal = async () => {
  isRefillModalOpen.value = true;
  try {
    await fetchProducts();
  } catch (error) {
    console.error('Error fetching products:', error);
  }
};

const submitRefill = async () => {
  if (!refillForm.value.quantity) {
    showError.value = true;
    return;
  }
  showError.value = false;

  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch('/refillphotocopy', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({
        product_id: selectedProduct.value.id,
        product_name: selectedProduct.value.name,
        quantity: parseInt(refillForm.value.quantity),
        current_stock: selectedProduct.value.stock
      })
    });

    if (response.ok) {
      await fetchProducts(); // Refresh products list
      selectedProductId.value = null;
      refillForm.value.quantity = '';
      closeRefillModal();
    } else {
      const errorData = await response.json();
      console.error('Error:', errorData.message);
    }
  } catch (error) {
    console.error('Error submitting refill:', error);
  }
};

const selectProduct = (product) => {
  selectedProduct.value = product;
  refillForm.value = {
    product_id: product.id,
    product_name: product.name,
    quantity: null,
    current_stock: product.stock
  };
};
</script>

<style scoped>
.page-content {
  width: 100%;
  margin-bottom: 200px;
}

.content-wrapper {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

.add-button {
  background-color: #4CAF50;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
}

.add-button:hover {
  background-color: #45a049;
}

.search-bar input {
  width: 100%;
  padding: 10px;
  margin-bottom: 0;
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

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #ddd;
}

.modal-header h2 {
  margin: 0;
  font-size: 22px;
}

.close-button {
  background: #ff4d4d;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 20px;
  line-height: 1;
  width: 35px;
  height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-button:hover {
  background: #ff3333;
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

.pos-modal-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  width: 95%;
  height: 90vh;
  max-width: 1200px;
  overflow-y: auto;
}

.pos-layout {
  display: flex;
  flex-direction: column;
  gap: 20px;
  height: 100%;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 15px;
  padding: 15px;
  overflow-y: auto;
}

.product-card {
  display: block;
  background-color: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  padding: 15px;
  cursor: pointer;
  position: relative;
  transition: all 0.2s ease;
}

.radio-input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.product-card:hover {
  border-color: #2196F3;
}

.product-card-content {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.product-card-content h3 {
  margin: 0;
  font-size: 16px;
  color: #333;
  font-weight: 500;
}

.product-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-line {
  color: #666;
  font-size: 14px;
}

.barcode {
  color: #888;
  font-size: 13px;
}

.select-circle {
  position: absolute;
  top: 50%;
  right: 15px;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  border: 2px solid #ccc;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.radio-input:checked + .product-card-content .select-circle {
  border-color: #2196F3;
}

.radio-input:checked + .product-card-content .select-circle .inner-circle {
  width: 12px;
  height: 12px;
  background-color: #2196F3;
  border-radius: 50%;
}

/* Search input styling */
.search-bar input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

/* Pagination styling */
.pos-pagination {
  display: flex;
  justify-content: center;
  gap: 10px;
  padding: 15px;
}

.pagination-button {
  padding: 8px 16px;
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  border-radius: 4px;
  color: #333;
  cursor: pointer;
}

.pagination-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-button:not(:disabled):hover {
  background-color: #e0e0e0;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #e0e0e0;
}

.modal-header h2 {
  margin: 0;
  font-size: 20px;
  color: #333;
}

.user-manual-button {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.close-button {
  background: none;
  border: none;
  font-size: 24px;
  color: #666;
  cursor: pointer;
  padding: 0 8px;
}

.selected-product-info {
  padding: 20px;
}

.selected-product-info h3 {
  margin: 0 0 20px 0;
  color: #333;
  font-size: 18px;
}

.info-grid {
  margin-bottom: 20px;
}

.info-row {
  display: grid;
  grid-template-columns: 120px 1fr;
  align-items: center;
  margin-bottom: 10px;
}

.info-label {
  color: #666;
  font-weight: 500;
}

.info-value {
  color: #333;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #333;
}

.form-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-input.error {
  border-color: #dc3545;
}

.error-message {
  color: #dc3545;
  font-size: 14px;
  margin-top: 5px;
  display: block;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 15px 20px;
  border-top: 1px solid #e0e0e0;
}

.add-stock-button {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.cancel-button {
  background-color: #6c757d;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.no-product-selected {
  text-align: center;
  padding: 30px;
  color: #666;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.pos-pagination {
  display: flex;
  justify-content: center;
  gap: 10px;
  padding: 10px;
  background-color: #f8f9fa;
  border-top: 1px solid #ddd;
}

.pagination-button {
  background-color: #2196F3;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s ease;
}

.pagination-button:hover:not(:disabled) {
  background-color: #1976D2;
}

.pagination-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.pagination {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.pagination button {
  background-color: #2196F3;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.pagination button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}
</style>