<template>
<SidebarLayout>
        <div class="px-4 pt-20 lg:ml-64">
            <div class="w-full">
                <div class="flex items-center justify-start">
                    <span
                        class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black mr-3"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 0 640 512"
                        >
                            <path
                                d="M335.5 4l288 160c15.4 8.6 21 28.1 12.4 43.5s-28.1 21-43.5 12.4L320 68.6 47.5 220c-15.4 8.6-34.9 3-43.5-12.4s-3-34.9 12.4-43.5L304.5 4c9.7-5.4 21.4-5.4 31.1 0zM320 160a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM144 256a40 40 0 1 1 0 80 40 40 0 1 1 0-80zm312 40a40 40 0 1 1 80 0 40 40 0 1 1 -80 0zM226.9 491.4L200 441.5V480c0 17.7-14.3 32-32 32H120c-17.7 0-32-14.3-32-32V441.5L61.1 491.4c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l37.9-70.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c16.3 0 31.9 4.5 45.4 12.6l33.6-62.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c32.4 0 62.1 17.8 77.5 46.3l33.6 62.3c13.5-8.1 29.1-12.6 45.4-12.6h19.5c32.4 0 62.1 17.8 77.5 46.3l37.9 70.3c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8L552 441.5V480c0 17.7-14.3 32-32 32H472c-17.7 0-32-14.3-32-32V441.5l-26.9 49.9c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l36.3-67.5c-1.7-1.7-3.2-3.6-4.3-5.8L376 345.5V400c0 17.7-14.3 32-32 32H296c-17.7 0-32-14.3-32-32V345.5l-26.9 49.9c-1.2 2.2-2.6 4.1-4.3 5.8l36.3 67.5c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8z"
                            />
                        </svg>
                    </span>
                    <h3 class="text-3xl">Manage FAQs</h3>
                </div>

                <hr class="h-px my-5 bg-orange-400 border-1 " />
                <div class="w-full mt-2">
                    <div class="w-full mb-5 mt-5">
                        <button @click="openComplainModal()" class="bg-orange-400 py-2.5 px-4 text-white rounded-xl mb-5 hover:bg-orange-300 ">
                            Add FAQs
                        </button>
                        <div
                            class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                        >
                            <div class="rounded-t mb-0 px-4 py-3 border-0">
                                <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                                >
                                <p class="text-xl mb-5 font-bold">FAQs Records</p>
                                <div class="flex-row flex items-center justify-between">
                                    <form class="flex items-center">
                                        <label
                                            for="simple-search"
                                            class="sr-only"
                                            >Search</label
                                        >
                                        <div class="relative w-full">
                                                <input
                                                    type="text"
                                                    id="simple-search"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                                                    placeholder="Search in table..."
                                                    required
                                                />
                                        </div>
                                       
                                         
                                    </form> 
                                </div>
                                </div>
                            </div>
                                <div class="block w-full overflow-x-auto mt-5">
                                    <table
                                        class="items-center w-full bg-transparent border-collapse"
                                    >
                                    <thead>
                                            <tr>
                                                <th
                                                    class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                                    v-for="headers in header"
                                                    :key="headers"
                                                >
                                                    {{ headers }}
                                                </th>
                                                <th
                                                    class="px-6 align-middle border text-center border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                                >
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                            
                                                v-for="(
                                                    item, rowIndex
                                                ) in header"
                                                :key="rowIndex"
                                            >
                                                
                                                <td
                                                    class="border-t-0 px-6 align-middle text-center border-l-0 border-r-0 text-green-500 text-xs whitespace-nowrap p-4"
                                                >
                                                    <AppDropdown class="">
                                                        <button>
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                height="24"
                                                                viewBox="0 0 448 512"
                                                            >
                                                                <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                <path
                                                                    d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <AppDropdownContent
                                                            class="bg-white z-50"
                                                        >
                                                            <AppDropdownItem>
                                                            Edit
                                                             </AppDropdownItem>
                                                            <AppDropdownItem>
                                                                Remove
                                                            </AppDropdownItem>
                                                        </AppDropdownContent>
                                                    </AppDropdown>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div
                    class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800"
                >
                    <div class="block w-full overflow-x-auto">
                        <div class="justify-between items-center block md:flex">
                            <div
                                class="flex items-center justify-start flex-wrap mb-3"
                            >
                                <button
                                    @click="changePageReserve(-1)"
                                    :disabled="currentPageReserve == 1"
                                    :class="{
                                        hidden: currentPageReserve == 1,
                                    }"
                                    type="button"
                                    class="text-gray-500 bg-white mr-5 hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                >
                                    Previous
                                </button>
                                <button
                                    @click="changePageReserve(1)"
                                    :disabled="currentPageReserve === totalPagesReserve"
                                    type="button"
                                    class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                >
                                    Next
                                </button>
                            </div>
                            <div class="flex items-center justify-center">
                                <small>Page {{ currentPageReserve }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    id="complainModal"
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
                                    class="flex items-start justify-between p-4 border-b rounded-t "
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Add FAQ(s)
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center "
                                        @click="closeComplainModal()"
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
                                    <form>
                                        <div class="px-4 py-2 bg-white rounded-b-lg ">
                                            <label
                                            for="title"
                                            class="block mb-2 text-sm font-medium text-gray-900"
                                            >Question:</label
                                        >

                                        <input
                                            id="title"
                                            type="text"
                                            autocomplete="title"
                                            class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                            
                                            required
                                        />
                                        </div>
                                        
                                    <div class="px-4 py-2 bg-white rounded-b-lg ">
                                        
                                            <label for="complainmessage">Answer:</label>
                                            <textarea id="complainmessage" v-model="complain" rows="8" class="block w-full  text-sm text-gray-800 bg-gray-100 border-0 rounded-xl border-spacing-1 p-5" placeholder="Write a Answer..." required></textarea>
                                        </div>
                                        
                                        
                                    </form>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex w-full py-6 px-8 space-x-2 border-t border-gray-200 rounded-b "
                                >
                                <button
                                        @click.prevent="closeComplainModal()"
                                        type="button"
                                        class="text-white bg-red-400 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click.prevent="createTenant()"
                                        type="button"
                                        class="text-white bg-orange-400 hover:bg-orange-200 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                    >
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>
<script>
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import {ref, computed} from 'vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
export default{
    components:{
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        SidebarLayout,
    },
    setup(){
            // const searchQueryReserve = ref("");
            // const itemsPerPageReserve = 10; // Set the maximum number of items per page to 10
            // const currentPageReserve = ref(1); // Initialize to the first page

            
            // const filteredDataReserve = computed(() => {
            //     const query = searchQueryReserve.value.toLowerCase().trim();
            //     if (!query) {
            //         return tenantsData; // Return all data if the search query is empty.
            //     }

            //     return tenantsData.filter((row) => {
            //         // Modify the conditions as needed for your specific search criteria.
            //         return (
            //             row.dorm_name.toLowerCase().includes(query) ||
            //             row.room_name.toLowerCase().includes(query) ||
            //             row.tenant_name.toLowerCase().includes(query)
            //         );
            //     });
            // });

            // const totalPagesReserve = computed(() => Math.ceil(filteredDataReserve.value.length / itemsPerPageReserve));

            // const slicedRows = computed(() => {
            //     const startIndex = (currentPageReserve.value - 1) * itemsPerPageReserve;
            //     const endIndex = startIndex + itemsPerPageReserve;

            //     const slicedAndSorted = filteredDataReserve.value
            //         .slice(startIndex, endIndex)
            //         .sort((a, b) => {
            //             const dateA = new Date(a.created_at);
            //             const dateB = new Date(b.created_at);
            //             return dateB - dateA;
            //         });

            //     return slicedAndSorted;
            //     });

            // const changePageReserve = (pageChange) => {
            //     const newPage = currentPageReserve.value + pageChange;
            //     if (newPage >= 1 && newPage <= totalPagesReserve.value) {
            //         currentPageReserve.value = newPage;
            //     }
            // };
            const header = [
            "Question",
            "Answer",
        ];
            const openComplainModal = () => {
                var modal = document.getElementById("complainModal");

                modal.style.display = "block";
            };
            const closeComplainModal = () => {
                var modal = document.getElementById("complainModal");

                modal.style.display = "none";
            };
            return{
                openComplainModal,
                closeComplainModal,
                header,
            //     changePageReserve,
            //     itemsPerPageReserve,
            //     totalPagesReserve,
            //     currentPageReserve,
            //     searchQueryReserve,
            //     slicedRows
            }
    }
}
</script>