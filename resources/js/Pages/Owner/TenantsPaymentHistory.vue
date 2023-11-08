<script>
import { ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import VueDatePicker from "@vuepic/vue-datepicker";
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
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
        AuthenticatedLayout,
    },
    setup() {
        const page = usePage();
        const user = page.props.auth.user;
        const date = ref();
        const numoptions = ["5", "10", "15", "20"];

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

        return {
            date,
            presetDates,
            numoptions,
            header,
            data,
            back,
            objectRemoveKey,
            removeUnderscoreAndCapitalizeAfterSpace,
            markAsPaid
        };
    },
};
</script>
<template>
    <AuthenticatedLayout>
        <div class="p-4 mt-16 lg:ml-64">
            <div class="flex flex-row gap-2 items-center">
                <button
                    @click="back()"
                    class="border-2 border-gray-500 px-3 py-1 text-gray-500 hover:text-white hover:border-orange-400 rounded-md hover:bg-orange-400"
                >
                    <span>
                        <i class="fa-solid fa-arrow-left fa-lg"></i>
                    </span>
                </button>
                <p class="text-2xl font-semibold my-4">Payment History</p>
            </div>
            <div
                class=" mb-4 mt-4 text-gray-400 dark:text-white"
            >
                <div
                    class="flex flex-row items-center jusitfy-center p-5 rounded-lg "
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
                            <h1 class="text-2xl font-bold">Jear Delarea</h1>
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

            </div>
            <div class="w-[278px] mt-5">
                <p class="text-sm">Date Range:</p>
                <VueDatePicker
                    v-model="date"
                    range
                    :preset-dates="presetDates"
                    :enable-time-picker="false"
                >
                    <template
                        #preset-date-range-button="{ label, value, presetDate }"
                    >
                        <span
                            class=""
                            role="button"
                            :tabindex="0"
                            @click="presetDate(value)"
                            @keyup.enter.prevent="presetDate(value)"
                            @keyup.space.prevent="presetDate(value)"
                        >
                            {{ label }}
                        </span>
                    </template>
                </VueDatePicker>
            </div>
            <!--Button-->
            <div class="mt-5">
                <button
                    class="px-3 py-2 bg-orange-400 rounded-md text-white shadow-lg font-semibold hover:bg-opacity-25"
                >
                    Generate
                </button>
            </div>
            <div class="w-full mb-5 mt-5">
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
                                            Copy
                                        </button>
                                        <button
                                            class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white"
                                        >
                                            Excel
                                        </button>
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
                            <tbody>
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
                            <div
                                class="justify-between items-center block md:flex"
                            >
                                <div
                                    class="flex items-center justify-center mb-6 md:mb-0"
                                >
                                    <div
                                        class="flex items-center justify-start flex-wrap -mb-3"
                                    >
                                        <button
                                            class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-200 dark:bg-slate-700 hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                            type="button"
                                        >
                                            <!----><span class="px-2"
                                                >1</span
                                            ></button
                                        ><button
                                            class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                            type="button"
                                        >
                                            <!----><span class="px-2"
                                                >2</span
                                            ></button
                                        ><button
                                            class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                            type="button"
                                        >
                                            <!----><span class="px-2"
                                                >3</span
                                            ></button
                                        ><button
                                            class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                            type="button"
                                        >
                                            <!----><span class="px-2">4</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center">
                                    <small>Page 1 of 4</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
