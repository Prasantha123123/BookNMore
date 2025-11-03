<template>
  <Head title="Services" />
  <Banner />
  <div
    class="flex flex-col items-center justify-start min-h-screen py-8 space-y-8 bg-gray-100 md:px-36 px-16"
  >
    <Header />
    <div class="w-full md:w-5/6 py-12 space-y-16">
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-center space-x-4"></div>
        <p class="text-3xl italic font-bold text-black">
          <span class="px-4 py-1 mr-3 text-white bg-black rounded-xl">{{ totalServices }}</span>
          <span class="text-xl">/ Total Services</span>
        </p>
      </div>
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-center space-x-4">
          <Link href="/">
            <img src="/images/back-arrow.png" class="w-14 h-14" />
          </Link>
          <p class="text-4xl font-bold tracking-wide text-black uppercase">
            Services
          </p>
        </div>
        
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
        <Link
          href="/services/photocopy"
          class="flex flex-col items-center justify-center px-6 py-8 text-xl font-bold text-white bg-blue-500 rounded-lg hover:bg-blue-600 shadow-lg"
        >
          <i class="ri-file-copy-line text-4xl mb-2"></i>
          Photocopy
        </Link>
        <Link
          href="/services/printout"
          class="flex flex-col items-center justify-center px-6 py-8 text-xl font-bold text-white bg-green-500 rounded-lg hover:bg-green-600 shadow-lg"
        >
          <i class="ri-printer-line text-4xl mb-2"></i>
          Printout
        </Link>
        <Link
          href="/services/binding"
          class="flex flex-col items-center justify-center px-6 py-8 text-xl font-bold text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 shadow-lg"
        >
          <i class="ri-bookmark-line text-4xl mb-2"></i>
          Binding
        </Link>
        <Link
          href="/services/laminating"
          class="flex flex-col items-center justify-center px-6 py-8 text-xl font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 shadow-lg"
        >
          <i class="ri-file-shield-line text-4xl mb-2"></i>
          Laminating
        </Link>
      </div>

      
      
      
    </div>
  </div>
  <Footer />
</template>

<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { Link, useForm, router } from "@inertiajs/vue3";
import Header from "@/Components/custom/Header.vue";
import Footer from "@/Components/custom/Footer.vue";
import Banner from "@/Components/Banner.vue";
import { HasRole } from "@/Utils/Permissions";

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isPhotocopyModalOpen = ref(false);
const isPrintoutModalOpen = ref(false);
const selectedService = ref(null);

const openEditModal = (service) => {
  selectedService.value = service;
  isEditModalOpen.value = true;
};

const openDeleteModal = (service) => {
  selectedService.value = service;
  isDeleteModalOpen.value = true;
};

const openPhotocopyModal = () => {
  console.log('Opening Photocopy Modal');
  console.log('isPhotocopyModalOpen:', isPhotocopyModalOpen.value);
  isPhotocopyModalOpen.value = true;
};

const openPrintoutModal = () => {
  console.log('Opening Printout Modal');
  isPrintoutModalOpen.value = true;
};

const openServiceModal = (serviceType) => {
  if (serviceType === 'Photocopy') {
    openPhotocopyModal();
  }
   else if (serviceType === 'Printout') {
    openPrintoutModal();
  }
  // Binding uses direct Link navigation now
  // Laminating uses direct Link navigation now
  // Add logic for other service types if needed
};

const props = defineProps({
  services: Object,
  totalServices: Number,
});

const search = ref("");

const performSearch = () => {
  router.get(
    route("services.index"),
    {
      search: search.value,
    },
    { preserveState: true }
  );
};

const deleteService = (id) => {
  const form = useForm({});
  form.delete(`/services/${id}`, {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
    },
    onError: (errors) => {
      console.error("Delete failed:", errors);
    },
  });
};
</script>

<style lang="css">
.pagination-disabled {
  color: rgb(37 99 235);
  transition: all 0.5s ease;
  background: rgb(229 231 235 / var(--tw-bg-opacity));
}
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
  font-size: 14px;
  float: right;
}

.pagination a:first-child,
.pagination a:last-child {
  padding: 8px 16px;
}
</style>