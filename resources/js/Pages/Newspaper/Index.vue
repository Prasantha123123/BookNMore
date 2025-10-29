<template>
  <Head title="Newspapers" />
  <Banner />
  <div
    class="flex flex-col items-center justify-start min-h-screen py-8 space-y-8 bg-gray-100 md:px-36 px-16"
  >
    <!-- Include the Header -->
    <Header />
    <div class="w-full md:w-5/6 py-12 space-y-16">
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-center space-x-4"></div>
        <p class="text-3xl italic font-bold text-black">
          <span class="px-4 py-1 mr-3 text-white bg-black rounded-xl">{{
            totalNewspapers
          }}</span>
          <span class="text-xl">/ Total Newspapers</span>
        </p>
      </div>
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-center space-x-4">
          <Link href="/">
            <img src="/images/back-arrow.png" class="w-14 h-14" />
          </Link>
          <p class="text-4xl font-bold tracking-wide text-black uppercase">
            Newspapers
          </p>
        </div>
        <p
          @click="
            () => {
              if (HasRole(['Admin'])) {
                isCreateModalOpen = true;
              }
            }
          "
          :class="
            HasRole(['Admin'])
              ? 'md:px-12 py-4 px-4 md:text-2xl font-bold tracking-wider text-white uppercase bg-blue-600 rounded-xl'
              : 'md:px-12 py-4 px-4 md:text-2xl font-bold tracking-wider text-white uppercase bg-blue-600 cursor-not-allowed rounded-xl'
          "
          :title="
            HasRole(['Admin'])
              ? ''
              : 'You do not have permission to add more Newspapers'
          "
        >
          <i class="md:pr-4 ri-add-circle-fill"></i> Add More Newspaper
        </p>
      </div>

      <div class="flex items-center space-x-4">
        <!-- Search Input on the Left -->
        <div class="md:w-1/4 w-full">
          <input
            v-model="search"
            @input="performSearch"
            type="text"
            placeholder="Search ..."
            class="w-full custom-input"
          />
        </div>
      </div>

      <div class="grid md:grid-cols-4 grid-cols-1 gap-8">
        <template v-if="newspapers && newspapers.data && newspapers.data.length > 0">
          <div
            v-for="newspaper in newspapers.data"
            :key="newspaper.id"
            class="space-y-4 text-white transition-transform duration-300 transform bg-black border-4 border-black shadow-lg hover:-translate-y-4"
          >
            <div class="px-2 py-4 space-y-4">
              <div
                class="flex items-start space-x-3 justify-between text-[11px] font-bold tracking-wide"
              >
                <p class="text-justify">{{ newspaper.name || "N/A" }}</p>
                <p
                  class="px-3 text-white bg-green-700 py-2 rounded-full flex items-center"
                >
                  {{ newspaper.price || "N/A" }}
                </p>
              </div>

              <div class="flex items-center justify-center w-full space-x-4">
                <p
                  class="flex items-center space-x-2 text-justify text-gray-400"
                >
                  Publisher :

                  <b> &nbsp; {{ newspaper.publisher || "N/A" }} </b>
                </p>
              </div>
              <div class="flex items-center justify-between">
                <p
                  v-if="newspaper.stock_quantity > 0"
                  class="text-xl font-bold tracking-wider text-green-500"
                >
                  <i class="ri-checkbox-blank-circle-fill"></i> In Stock ({{ newspaper.stock_quantity }})
                </p>
                <p v-else class="text-xl font-bold tracking-wider text-red-500">
                  <i class="ri-checkbox-blank-circle-fill"></i> Out of Stock
                </p>
              </div>

              <div class="flex items-center justify-right space-x-2">
  <button
    :disabled="!HasRole(['Admin'])"
    @click="
      () => {
        if (HasRole(['Admin'])) {
          editNewspaper(newspaper);
        }
      }
    "
    :class="{
      'cursor-not-allowed opacity-50': !HasRole(['Admin']),
      'cursor-pointer hover:bg-green-600': HasRole(['Admin']),
    }"
    class="flex items-center justify-center w-10 h-10 text-gray-800 transition duration-200 bg-gray-100 rounded-full hover:text-white"
    :title="!HasRole(['Admin']) ? 'You do not have permission to edit newspapers' : 'Edit newspaper'"
  >
    <i class="ri-pencil-line"></i>
  </button>
<button
  :disabled="!HasRole(['Admin'])"
  @click="() => {
    if (HasRole(['Admin'])) {
      console.log('Setting delete target:', newspaper);
      deleteTargetNewspaper = newspaper;
      isDeleteModalOpen = true;
      console.log('Modal should open:', isDeleteModalOpen);
    }
  }"
  :class="{
    'cursor-not-allowed opacity-50': !HasRole(['Admin']),
    'cursor-pointer hover:bg-red-600': HasRole(['Admin']),
  }"
  class="flex items-center justify-center w-10 h-10 text-gray-800 transition duration-200 bg-gray-100 rounded-full hover:text-white"
  :title="!HasRole(['Admin']) ? 'You do not have permission to delete newspapers' : 'Delete newspaper'"
>
  <i class="ri-delete-bin-line"></i>
</button>
</div>
            </div>
          </div>
        </template>
        <template v-else>
          <div class="col-span-4 text-center text-gray-500">
            <p class="text-center text-red-500 text-[17px]">
              No Newspapers Available
            </p>
          </div>
        </template>
      </div>
    </div>
  </div>
  <NewspaperCreateModel v-model:open="isCreateModalOpen" />
  <NewspaperUpdateModel
    v-model:open="isUpdateModalOpen"
    :newspaper="selectedNewspaper"
    @close="isUpdateModalOpen = false"
    @update="handleUpdate"
  />

 <NewspaperDeleteModel
  v-model:open="isDeleteModalOpen"
  :selectedNewspaper="deleteTargetNewspaper"
  @update:open="isDeleteModalOpen = $event"
/>

  <Footer />
</template>

<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { Link, router } from "@inertiajs/vue3";
import Header from "@/Components/custom/Header.vue";
import Footer from "@/Components/custom/Footer.vue";
import Banner from "@/Components/Banner.vue";
import { debounce } from "lodash";
import { HasRole } from "@/Utils/Permissions";
import NewspaperCreateModel from "@/Components/custom/NewspaperCreateModel.vue";
import NewspaperUpdateModel from "@/Components/custom/NewspaperUpdateModel.vue";
import NewspaperDeleteModel from "@/Components/custom/DeleteNewspaperModal.vue";

const isCreateModalOpen = ref(false);
const isUpdateModalOpen = ref(false);
const selectedNewspaper = ref(null);
const isDeleteModalOpen = ref(false);
const deleteTargetNewspaper = ref(null);

const props = defineProps({
  newspapers: Object,
  totalNewspapers: Number,
  search: String,
});

const search = ref(props.search || "");

const performSearch = debounce(() => {
  applyFilters();
}, 500);

const applyFilters = (page) => {
  router.get(
    route("newspapers.index"),
    {
      search: search.value,
    },
    { preserveState: true }
  );
};

const editNewspaper = (newspaper) => {
  selectedNewspaper.value = newspaper;
  isUpdateModalOpen.value = true;
};

const handleUpdate = (updatedData) => {
  router.put(route('newspapers.update', selectedNewspaper.value.id), updatedData, {
    onSuccess: () => {
      isUpdateModalOpen.value = false;
      selectedNewspaper.value = null;
    },
  });
};

const deleteNewspaper = (newspaperId) => {
  router.delete(route('newspapers.destroy', newspaperId), {
    onSuccess: () => {
      console.log('Newspaper deleted successfully');
    },
    onError: (error) => {
      console.error('Failed to delete newspaper:', error);
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