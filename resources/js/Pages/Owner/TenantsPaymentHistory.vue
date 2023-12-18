<script>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import VueDatePicker from "@vuepic/vue-datepicker";
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import DataTable from 'primevue/datatable';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';      
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { useConfirm } from "primevue/useconfirm";
import "@vuepic/vue-datepicker/dist/main.css";
import {
    endOfMonth,
    endOfYear,
    startOfMonth,
    startOfYear,
    subMonths,
} from "date-fns";

export default {
    components: {
        VueDatePicker,
        DataTable,
        Button,
        Tag,
        Column,
        AuthenticatedLayout,
    },
    setup() {
        const page = usePage();
        const user = page.props.auth.user;
        const date = ref();
        const numoptions = ["5", "10", "15", "20"];
        const filters = ref();
        console.log(page.props.payments)
        const header = [
            "Room Name",
            "Description",
            "Invoice #",
            "Payment method",
            "Payment Date",
            "Amount",
            "Status",
            'Proof Of Payment',
            "Action"
        ];

        const data = ref([])
        data.value = page.props.payments
        const tenant =  page.props.tenant
        
        const getSeverity = (status) => {
                switch (status) {
                    case 'decline':
                        return 'danger';

                    case 'approved':
                        return 'success';

                    case 'pending':
                        return 'warning';

                }
            };

            const initFilters = () => {
                filters.value = {
                    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                    description: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                    payment_method: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                   
                };
            };

            initFilters();
            
            const clearFilter = () => {
                initFilters();
            };

            const formatDate = (value) => {
                // Check if value is a string and convert it to a Date object
                const date = typeof value === 'string' ? new Date(value) : value;

                // Check if date is a valid Date object
                if (isNaN(date.getTime())) {
                    // If not a valid Date, you can handle it according to your requirements
                    return "Invalid Date"; // or return value.toString() or any other representation
                }

                // If it's a valid Date object, format it
                return date.toLocaleDateString('en-US', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            };
        console.log(data)

        const objectRemoveKey = (object, key = null) => {
            const newObject = Object.assign({}, object);

            delete newObject.billing_id;
            delete newObject.payment_id;
            newObject.description = removeUnderscoreAndCapitalizeAfterSpace(newObject.description)

            return newObject;
        }

        const presetDates = ref([
            { label: "Today", value: [new Date(), new Date()] },
            {
                label: "Today (Slot)",
                value: [new Date(), new Date()],
                slot: "preset-date-range-button",
            },
            {
                label: "This month",
                value: [startOfMonth(new Date()), endOfMonth(new Date())],
            },
            {
                label: "Last month",
                value: [
                    startOfMonth(subMonths(new Date(), 1)),
                    endOfMonth(subMonths(new Date(), 1)),
                ],
            },
            {
                label: "This year",
                value: [startOfYear(new Date()), endOfYear(new Date())],
            },
        ]);
        const back = () => {
            var url = null;

            if (user) {
                router.get(route("owner.billing"));
            } else {
                router.get(route("landing.page"));
            }
        };

        console.log(data);
        const removeUnderscoreAndCapitalizeAfterSpace = (inputString) => {
            const stringWithSpaces = inputString.replace(/_/g, ' ');

            // Split the string by spaces
            const words = stringWithSpaces.split(' ');

            // Capitalize the first letter of each word and join them
            const capitalizedString = words.map(word => {
                if (word.length > 0) {
                return word[0].toUpperCase() + word.slice(1).toLowerCase();
                }
                return word; // Handle cases where there are multiple spaces
            }).join(' ');

            return capitalizedString;
        }

        const markAsPaid = (payment_id, billing_id) => {
            swal({
                title: `Are you sure to mark as paid this payment?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function(){
                axios.post(route('payment-history.mark-as-paid'), {payment_id: payment_id, billing_id: billing_id})
                    .then(response => {
                        swal("Success!", `You successfully mark this payment as paid.`, "success");

                        setTimeout(function () {
                            location.reload()
                        }, 3000);
                    })
                    .catch(error => {

                    })
            });
        }

        // const searchQueryReserve = ref("");
        //     const itemsPerPageReserve = 10; // Set the maximum number of items per page to 10
        //     const currentPageReserve = ref(1); // Initialize to the first page


        //     const filteredDataReserve = computed(() => {

        //         return dataReserve.filter((row) => {
        //             // Modify the conditions as needed for your specific search criteria.
        //             return (
        //                 row.dorm_name.toLowerCase().includes(query) ||
        //                 row.room_name.toLowerCase().includes(query) ||
        //                 row.tenant_name.toLowerCase().includes(query)
        //             );
        //         });
        //     });

        //     const totalPagesReserve = computed(() => Math.ceil(filteredDataReserve.value.length / itemsPerPageReserve));

        //     const slicedRows = computed(() => {
        //         const startIndex = (currentPageReserve.value - 1) * itemsPerPageReserve;
        //         const endIndex = startIndex + itemsPerPageReserve;

        //         const slicedAndSorted = filteredDataReserve.value
        //             .slice(startIndex, endIndex)
        //             .sort((a, b) => {
        //                 const dateA = new Date(a.created_at);
        //                 const dateB = new Date(b.created_at);
        //                 return dateB - dateA;
        //             });

        //         return slicedAndSorted;
        //         });

        //     const changePageReserve = (pageChange) => {
        //         const newPage = currentPageReserve.value + pageChange;
        //         if (newPage >= 1 && newPage <= totalPagesReserve.value) {
        //             currentPageReserve.value = newPage;
        //         }
        //     };

        return {
            filters,
            tenant,
            date,
            presetDates,
            numoptions,
            header,
            data,
            back,
            objectRemoveKey,
            removeUnderscoreAndCapitalizeAfterSpace,
            markAsPaid,
            getSeverity,
            clearFilter,
            formatDate
        };
    },
};
</script>
<template>
    <AuthenticatedLayout>
        <div class="p-4 mt-16 lg:ml-64">
            <div class="max-w-screen-lg
                        mx-auto">
            <div class="flex flex-row gap-2 items-center">
                <button
                    @click="back()"
                    class="border-2 border-gray-500 px-3 py-1 text-gray-500 hover:text-white hover:border-orange-400 rounded-md hover:bg-orange-400"
                >
                    <span>
                        <i class="fa-solid fa-arrow-left fa-lg"></i>
                    </span>
                </button>
                <p class="text-2xl font-semibold my-4">Bill Tenant</p>
            </div>
            <div class="flex justify-between">
                <div>
                    <button class="py-1.5 px-2 border rounded-lg text-red-500 hover:bg-red-400 hover:text-white border-red-500">
                        Termination Notice
                    </button>
                </div>
                <div class="flex gap-3">
                    <button class="py-1.5 px-2 border rounded-lg text-orange-500 hover:bg-orange-400 hover:text-white border-orange-500">
                        Add Bill
                    </button>
                    <button class="py-1.5 px-2 border rounded-lg text-green-500 hover:bg-green-400 hover:text-white border-green-500">
                        Auto Bill
                    </button>
                    <button class="py-1.5 px-2 border rounded-lg text-red-500 hover:bg-red-400 hover:text-white border-red-500">
                        Mark as delinquent
                    </button>
                </div>
            </div>
            <div
                class=" mb-4 mt-4 text-gray-900 dark:text-white grid grid-cols-1 md:grid-cols-2 gap-3"
            >
                <div
                    class="flex flex-row items-center jusitfy-center p-5 rounded-lg bg-white shadow "
                >
                    <div class="flex items-center justify-center mb-6 md:mb-0">
                        <div>
                            <img
                                src="https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com"
                                alt="Profile picture"
                                class="rounded-full block md:h-60 w-full max-w-full bg-gray-100 dark:bg-slate-800"
                            />
                        </div>
                    </div>
                    <div class="flex items-center justify-center">
                        <div
                            class="space-y-3 text-center md:text-left lg:mx-12"
                        >
                            <h1 class="text-2xl font-bold text-black">{{tenant.profile.first_name}} {{ tenant.profile.last_name }}</h1>
                            <div class="flex justify-center md:block">
                                <div
                                    class="inline-flex items-center capitalize leading-none text-sm border rounded-full py-1.5 px-4 bg-blue-500 border-blue-500 text-white"
                                >
                                    <span
                                        class="inline-flex justify-center items-center w-4 h-4 mr-2"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"
                                            ></path></svg></span
                                    ><span>Verified</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-rows-4 text-black gap-2">
                    <div class="bg-white shadow rounded-lg">
                        <div class="flex w-full p-3 justify-between">
                            
                            <div class="flex items-center gap-5">
                                <div>
                                    <i class="fa-solid fa-droplet fa-xl" style="color: #005eff;"></i>
                                </div>
                                <div>
                                    <p>Description</p>
                                    <p>Water</p>
                                </div>
                                
                            </div>
                            <div>
                                <p>Due Date</p>
                                <p>11/29/23</p>
                            </div>
                            <div>
                                <p>Amount</p>
                                <p>P100.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-lg">
                        <div class="flex w-full p-3 justify-between">
                            
                            <div class="flex items-center gap-5">
                                <i class="fa-solid fa-bolt fa-xl" style="color: #fff700;"></i>
                                <div>
                                    <p>Description</p>
                                <p>Electricity</p>
                                </div>
                                
                            </div>
                            <div>
                                <p>Due Date</p>
                                <p>11/29/23</p>
                            </div>
                            <div>
                                <p>Amount</p>
                                <p>P100.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-lg">
                        <div class="flex w-full p-3 justify-between">
                            <div class="flex gap-5 items-center">
                                <i class="fa-solid fa-house fa-xl" style="color: #db7a0a;"></i>
                                <div>
                                    <p>Description</p>
                                    <p>Monthly Fee</p>
                                </div>
                                
                            </div>
                            <div>
                                <p>Due Date</p>
                                <p>11/29/23</p>
                            </div>
                            <div>
                                <p>Amount</p>
                                <p>P100.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-lg">
                        <div class="flex w-full p-3 justify-between">
                            <div class="flex gap-5 items-center">
                                <i class="fa-solid fa-wifi fa-xl" style="color: #00e1ff;"></i>
                                <div>
                                    <p>Description</p>
                                    <p>Internet</p>
                                </div>
                                
                            </div>
                            <div>
                                <p>Due Date</p>
                                <p>11/29/23</p>
                            </div>
                            <div>
                                <p>Amount</p>
                                <p>P100.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-10">
                <DataTable v-model:filters="filters" :value="data" tableStyle="min-width: 50rem" :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="10"
                :globalFilterFields="['description', 'payment_method']">
                <template #header>
                    <div class="flex items-center border-b py-2 justify-center">
                        <p class="text-2xl">Payment History</p>
                    </div>
                    <div class="flex items-center mt-3 justify-between">
                        <Button type="button" class="rounded-lg border-green-400 border px-3 py-2.5" icon="fa-solid fa-filter-circle-xmark" label="Clear" outlined @click="clearFilter()" />
                        <span class="p-input-icon-left">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input v-model="filters['global'].value" placeholder="Keyword Search" class="pl-10 rounded-lg" />
                        </span>
                    </div>
                </template>
                <template #empty> No user found. </template>
                    <Column field="description" header="Description" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.description }}
                        </template>
                    </Column>
                    <Column field="invoice_no" header="Invoice #" sortable style="min-width: 14rem" class="border-b">
                    </Column>
                    <Column field="payment_method" header="Payment Method" sortable style="min-width: 14rem" class="border-b">
                    </Column>
                    <Column header="Status" field="status" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="getSeverity(data.status)" />
                        </template>
                    </Column>
                </DataTable>
            </div>
            <!-- <div class="w-full mb-5 mt-5">
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                >
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div
                                class="relative w-full sm:flex-row sm:justify-between sm:items-center gap-5 file:px-4 max-w-full flex-col flex"
                            >
                                <div
                                    class="mb-3 sm:flex-row flex-col flex gap-3"
                                >
                                    <div class="flex flex-row gap-2">
                                        <button
                                            class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white"
                                        >
                                            PDF
                                        </button>
                                        <button
                                            class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white"
                                        >
                                            Print
                                        </button>
                                    </div>
                                </div>
                                <form class="flex items-center">
                                    <label for="simple-search" class="sr-only"
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
                                        v-for="header in header"
                                        :key="header"
                                    >
                                        {{ header }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="data">
                                <tr
                                    v-for="(item, rowIndex) in data"
                                    :key="rowIndex"
                                >
                                    <td
                                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                        v-for="(value, colIndex) in objectRemoveKey(item)"
                                        :key="colIndex"
                                    >
                                        <span v-if="colIndex != 'action' && colIndex != 'proof_of_payment'">
                                            {{ value }}
                                        </span>

                                        <img v-if="colIndex == 'proof_of_payment' && value != null"
                                            class="w-[300px] h-[100px]"
                                            :src="value"
                                            style="border: 1px solid black"
                                        />

                                        <button v-if="colIndex == 'action'"
                                            class="px-3 py-2 bg-cyan-400 text-[8px] rounded-md"
                                            :disabled="item.status == 'Paid'"
                                            :class="{'cursor-not-allowed': item.status == 'Paid'}"
                                            @click="markAsPaid(item.payment_id, item.billing_id)"
                                        >
                                            MARK AS PAID
                                        </button>
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
            </div> -->
        
        </div>
    </div>
    </AuthenticatedLayout>
</template>
