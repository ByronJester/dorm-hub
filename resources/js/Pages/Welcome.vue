<!-- <script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);

const isMobileView = ref(false)

isMobileView.value = screen.width < 600;

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    dorms: Array
});

</script> -->
<script>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import DormList from "@/Components/DormList.vue";
import Hero from "@/Components/Hero.vue";

export default {
    components: {
        GuestLayout,
        DormList,
        Hero,
    },
    setup() {
        const page = usePage();

        const dorms = ref([]);
        const hero = page.props.hero;
        const searchQuery = ref("");
        const selectedPriceRange = ref(null); 

        onMounted(() => {
            dorms.value = page.props.dorms;
        });

        watch(searchQuery, () => {
            const query = searchQuery.value.toLowerCase();
            dorms.value = page.props.dorms.filter((dorm) => {
                return (
                    dorm.property_name.toLowerCase().includes(query) ||
                    dorm.detailed_address.toLowerCase().includes(query)
                );
            });
        });

        const openModal = () => {
            var modal = document.getElementById("filterModal");

            modal.style.display = "block";
        };

        const closeModal = () => {
            var modal = document.getElementById("filterModal");

            modal.style.display = "none";
        };
        

        return {
            hero,
            dorms,
            searchQuery,
            openModal,
            closeModal,
        };
    },
};
</script>

<template>
    <GuestLayout>
        <div class="flex items-center mx-5 justify-center">
            <div
                class="absolute z-20 mt-[860px] sm:mt-[960px] w-full py-3 px-10 lg:px-96 xl:px-[300px] 2xl:px-[500px] gap-4 text-center"
            >
                <form>
                    <label
                        for="default-search"
                        class="mb-2 text-sm font-medium text-gray-900 sr-only"
                        >Search</label
                    >
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                                />
                            </svg>
                        </div>
                        <input
                            type="search"
                            v-model="searchQuery"
                            id="default-search"
                            class="block w-full p-4 pl-10 text-sm text-gray-900 border shadow-md border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Search Location..."
                            required
                        />
                    </div>
                </form>
            </div>
        </div>

        <Hero
            :title="hero.title"
            :subtitle="hero.subtitle"
            :description="hero.description"
            :heroImage="hero.image_path"
        />

        <div
            class="max-w-[2520px] mt-10 mx-auto xl:px-20 md:px-10 sm:px-2 px-4"
        >
            <div class="flex flex-row items-center py-3 justify-between">
                <h1 class="text-3xl text-gray-900 mb-2 font-bold">
                    All Domitories
                </h1>

                <button
                    @click="openModal()"
                    class="border px-5 py-2.5 ites-center justify-center rounded-xl border-gray-300 flex flex-row gap-2"
                >
                    <span>
                        <i
                            class="fa-solid fa-sliders fa-lg"
                            style="color: #000000"
                        ></i>
                    </span>

                    <span class="text-md"> Filters </span>
                </button>
            </div>

            <div
                className="
                        grid
                        grid-cols-1
                        sm:grid-cols-1
                        md:grid-cols-2
                        lg:grid-cols-3
                        xl:grid-cols-4
                        2xl:grid-cols-5
                        gap-8
                    "
            >
                <DormList :dorms.sync="dorms" :user.sync="null" />
            </div>
        </div>

        <div
            id="filterModal"
            tabindex="-1"
            aria-hidden="true"
            style="background-color: rgba(0, 0, 0, 0.7)"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
        >
            <div class="h-screen flex justify-center items-center">
                <div class="relative w-full max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow">
                        <!-- Modal header -->
                        <div
                            class="flex items-start justify-between p-4 border-b rounded-t"
                        >
                            <h3 class="text-xl font-semibold text-black">
                                Filter Dormitories
                            </h3>
                            <button
                                type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                @click="closeModal()"
                            >
                                <svg
                                    class="w-3 h-3"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 14 14"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                                    />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-6">
                            <div>
                                <h1 class="text-2xl font-bold">Dorm Price</h1>
                                <div class="mt-4">
                                <div class="flex items-center gap-5">
                                <input
                                    type="number"
                                    
                                    class="w-1/2 p-2 text-sm text-gray-900 border shadow-md border-gray-300 rounded-lg bg-white focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Min"
                                />
                                <span>-</span>
                                <input
                                    type="number"
                              
                                    class="w-1/2 p-2 text-sm text-gray-900 border shadow-md border-gray-300 rounded-lg bg-white focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Max"
                                />
                                </div>
                            </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex flex-row items-center justify-between p-6 space-x-2 border-t border-gray-200 rounded-b"
                        >
                            <button
                                @click="closeModal()"
                                type="button"
                                class="hover:underline"
                            >
                                Clear all
                            </button>
                            <button
                                @click="closeModal()"
                                type="button"
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                            >
                                Show Dorms
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
