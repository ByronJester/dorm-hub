
<script>
    import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
    // import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import 'vue-good-table-next/dist/vue-good-table-next.css'
    import { VueGoodTable } from 'vue-good-table-next';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';
    import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
    import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
    import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";

    export default {
        components: {
            AuthenticatedLayout,
            AppDropdown,
            AppDropdownContent,
            AppDropdownItem,
            VueGoodTable
        },
        setup() {
            const isMobileView = ref(false)
            isMobileView.value = screen.width < 600;

            const page = usePage()

            const user = page.props.auth.user;




            var dataReserve = [];

            const headersReserve = ["Dorm Name", "Room Name", "Applicant Name", "Date Visit", "Time Visit", "Payment Method", "Status"];

            const reservations = page.props.reservations;

            for (let x = 0; x < reservations.length; x++) {
                dataReserve.push(
                    {
                        dorm_name: reservations[x].dorm.property_name,
                        room_name: reservations[x].room.name,
                        tenant_name: reservations[x].tenant.name,
                        visit_date: reservations[x].check_date,
                        time_visit: reservations[x].check_time,
                        payment_method: reservations[x].reservation_payment.payment_method,
                        status: reservations[x].status,
                    }
                );
            }

            const searchQueryReserve = ref("");
            const itemsPerPageReserve = 10; // Set the maximum number of items per page to 10
            const currentPageReserve = ref(1); // Initialize to the first page

            
            const filteredDataReserve = computed(() => {
                const query = searchQueryReserve.value.toLowerCase().trim();
                if (!query) {
                    return dataReserve; // Return all data if the search query is empty.
                }

                return dataReserve.filter((row) => {
                    // Modify the conditions as needed for your specific search criteria.
                    return (
                        row.dorm_name.toLowerCase().includes(query) ||
                        row.room_name.toLowerCase().includes(query) ||
                        row.tenant_name.toLowerCase().includes(query) ||
                        row.status.toLowerCase().includes(query)
                        // Add more conditions for other columns as needed
                    );
                });
            });

            const totalPagesReserve = computed(() => Math.ceil(filteredDataReserve.value.length / itemsPerPageReserve));

            const slicedRows = computed(() => {
                const startIndex = (currentPageReserve.value - 1) * itemsPerPageReserve;
                const endIndex = startIndex + itemsPerPageReserve;
                return filteredDataReserve.value.slice(startIndex, endIndex);
            });

            const changePageReserve = (pageChange) => {
                const newPage = currentPageReserve.value + pageChange;
                if (newPage >= 1 && newPage <= totalPagesReserve.value) {
                    currentPageReserve.value = newPage;
                }
            };


            return {
                searchQueryReserve,
                itemsPerPageReserve,
                currentPageReserve,
                filteredDataReserve,
                totalPagesReserve,
                changePageReserve,
                headersReserve,
                dataReserve,
                isMobileView,
                slicedRows,
                user,
                reservations,
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <!---->
        <div class="px-4 pt-20 lg:ml-64">
            <div class="flex items-center justify-start">
                <span class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" height="25px" viewBox="0 0 640 512"><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/></svg>                </span>
                <h3 class="text-3xl">Reservation</h3>
            </div>
            <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />
          
            <!--Reservation-->
            <div class="w-full mt-5">
                <div class="w-full mb-5 ">
                    <div
                        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                    >
                        <div class="rounded-t mb-0 px-4 py-3 border-0">
                            <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                                >
                                <p class=" text-lg mb-5">Reservation Applicants</p>
                                    <form class="flex items-center">
                                        <label
                                            for="simple-search"
                                            class="sr-only"
                                            >Search</label
                                        >
                                        <div class="relative w-full">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                            >
                                                <svg
                                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 18 20"
                                                >
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 0V6a3 3 0 0 0-3-3H9m1.5-2-2 2 2 2"
                                                    />
                                                </svg>
                                            </div>
                                            <input
                                                type="text"
                                                id="simple-search"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Search in table..."
                                                v-model="searchQueryReserve"
                                                required
                                            />
                                        </div>
                                        <button
                                            type="submit"
                                            class="p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                        >
                                            <svg
                                                class="w-4 h-4"
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
                                            <span class="sr-only">Search</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table
                                class="items-center w-full bg-transparent border-collapse"
                            >
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                            v-for="header in headersReserve"
                                            :key="header"
                                        >
                                            {{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, rowIndex) in slicedRows"
                                        :key="rowIndex"
                                    >
                                        <td
                                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                            v-for="(value, colIndex) in item"
                                            :key="colIndex"
                                        >
                                  
                                                {{ value }}
                                        
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
            <!-- Pre eto yung dating table
            <div class="w-full">
                <vue-good-table
                    styleClass="vgt-table condensed"
                    :columns="columns"
                    :rows="rows"
                    :pagination-options="{ enabled: true }"
                    :select-options="{ enabled: false }"
                    :search-options="{ enabled: true }"
                >
                    <template #table-row="props">
                        <div v-if="props.column.field == 'name'" class="mt-2">
                            {{ props.row.tenant.name }}
                        </div>

                        <div v-if="props.column.field == 'source_of_income'" class="mt-2">
                            {{ props.row.tenant.income_information.source_of_income }}
                        </div>

                        <div v-if="props.column.field == 'monthly_income'" class="mt-2">
                            {{ moneyFormat(props.row.tenant.income_information.monthly_income) }}
                        </div>

                        <div v-if="props.column.field == 'status'" class="mt-2">
                            {{ props.row.is_approved ? 'Approved' : props.row.created_at != props.row.updated_at ? 'Declined' :'Pending' }}
                        </div>

                        <div v-if="props.column.field == 'monthly_expenses'" class="mt-2">
                            {{ moneyFormat(props.row.tenant.income_information.monthly_expenses) }}
                        </div>

                        <div v-if="props.column.field == 'action'">
                            <button class="bg-cyan-500 p-3 mx-1 text-white rounded-md text-xs"
                                @click="openModal(props.row, false)"
                            >
                                View
                            </button>

                            <button class="bg-cyan-500 p-3 mx-1 text-white rounded-md text-xs"
                            @click="openModal(props.row.payments, true)"
                            >
                                Payments
                            </button>
                        </div>
                    </template>
                </vue-good-table>
            </div>-->

            <!-- eto yung mga modal
            <div class="w-full">
                <div id="tenantModal" class="tenantModal mt-10 md:mt-0">
                    <div class="tenant-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '30%'}" v-if="!viewPayment">
                        <div class="w-full">
                            <span class="text-lg font-bold">
                                Source of Income Proof
                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5" v-if="application">
                            <img :src="application.tenant.income_information.proof" alt="proof"
                                style="width: 100%; height: 250px;"
                            >
                        </div>

                        <div class="w-full mt-5"  v-if="application">
                            <button class="bg-cyan-900 p-3 mx-1 text-white rounded-md text-xs float-right"
                                @click="changeStatus('approved', application.id)"
                                :disabled="application.is_approved"
                                :class="{'cursor-not-allowed': application.is_approved}"
                            >
                                Approve
                            </button>

                            <button class="bg-rose-500 p-3 mx-1 text-white rounded-md text-xs float-right"
                                @click="changeStatus('declined', application.id)"
                                :disabled="application.is_approved"
                                :class="{'cursor-not-allowed': application.is_approved}"
                            >
                                Decline
                            </button>
                        </div>

                    </div>

                    <div class="tenant-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '97%'}" v-else>
                        <div class="w-full">
                            <span class="text-lg font-bold">
                                Payments
                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <vue-good-table
                            class="mt-5"
                            styleClass="vgt-table condensed"
                            style="width: 100%"
                            :columns="paymentColumns"
                            :rows="payments"
                            :select-options="{ enabled: false }"
                            :search-options="{ enabled: true }"
                            :pagination-options="{
                                enabled: true,
                                perPage: 3,
                                perPageDropdown: [3],
                                jumpFirstOrLast : true,
                            }"
                        >
                            <template #table-row="props">
                                <div v-if="props.column.field == 'amount_to_pay'" class="mt-2">
                                    {{ moneyFormat(props.row.amount_to_pay) }}
                                </div>

                                <div v-if="props.column.field == 'amount_paid'" class="mt-2">
                                    {{ moneyFormat(props.row.amount_paid) }}
                                </div>

                                <div v-if="props.column.field == 'partial'" class="mt-2">
                                    {{ moneyFormat(props.row.partial) }}
                                </div>

                                <div v-if="props.column.field == 'balance'" class="mt-2">
                                    {{ moneyFormat(props.row.amount_to_pay - props.row.amount_paid) }}
                                </div>

                                <div v-if="props.column.field == 'mode_of_payment'" class="mt-2">
                                    {{ props.row.mode_of_payment }}
                                </div>

                                <div v-if="props.column.field == 'status'" class="mt-2">
                                    <button class="bg-cyan-500 py-1 px-3 mx-1 text-white rounded-md text-xs"
                                        v-if="!!props.row.is_paid"
                                        :disabled="true"
                                    >
                                        PAID
                                    </button>

                                    <button class="bg-rose-500 py-1 px-3 mx-1 text-white rounded-md text-xs"
                                        v-if="!props.row.is_paid"
                                        :disabled="true"
                                    >
                                        NOT PAID
                                    </button>
                                </div>

                                <div v-if="props.column.field == 'partial_receipt'" class="mt-2">
                                    <img :src="props.row.partial_receipt ?? '/images/upload_image.png'" class="rounded-md"
                                        style="width: 150px; height: 200px; border: 1px solid gray;"
                                    />
                                </div>

                                <div v-if="props.column.field == 'receipt'" class="mt-2">
                                    <img :src="props.row.receipt ?? '/images/upload_image.png'" class="rounded-md"
                                        style="width: 150px; height: 200px; border: 1px solid gray;"
                                    />
                                </div>

                                <div v-if="props.column.field == 'action'" class="mt-2">
                                    <button class="bg-cyan-500 p-3 mx-1 text-white rounded-md text-xs"
                                        :disabled="!!props.row.is_paid"
                                        :class="{'cursor-not-allowed' : !!props.row.is_paid}"
                                        @click="markAsPaid(props.row.id)"
                                    >
                                        MARK AS PAID
                                    </button>
                                </div>
                            </template>
                        </vue-good-table>
                    </div>
                </div>
            </div>`-->

        </div>
    </AuthenticatedLayout>
</template>


