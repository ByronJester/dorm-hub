<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import { Head } from "@inertiajs/vue3";
import { VueGoodTable } from "vue-good-table-next";
import { ref, computed, reactive, watch, onMounted, defineProps } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import axios from "axios";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { router } from "@inertiajs/vue3";

export default {
    components: {
        AuthenticatedLayout,
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        VueDatePicker,
    },
    setup() {
        const options = ["M.D.R Apartment", "Dorm2"];
        const selectedDay = ref(1); // Default to the 1st day of the month
         const days = Array.from({ length: 30 }, (_, index) => index + 1); // Create an array of days from 1 to 30

        const dates = [
            "All time",
            "Current month",
            "Previous month",
            "Last 12 months",
            "Last 30 days",
            "Previous Year",
        ];
        const headersHistory = [
            "Room Name",
            "Tenant Name",
            "Description",
            "Amount",
            "Invoice #",
            "Payment method",
            "Status",
        ];

        const dataHistory = [
            {
                "Room Name": "Living Room",
                "Tenant Name": "John Doe",
                Description: "Monthly rent",
                Amount: 1000,
                "Invoice #": "INV-001",
                "Payment method": "Credit Card",
                Status: "Paid",
            },
            {
                "Room Name": "Bedroom",
                "Tenant Name": "Alice Johnson",
                Description: "Security deposit",
                Amount: 500,
                "Invoice #": "INV-002",
                "Payment method": "Bank Transfer",
                Status: "Paid",
            },
            {
                "Room Name": "Kitchen",
                "Tenant Name": "Bob Smith",
                Description: "Appliance repair",
                Amount: 75,
                "Invoice #": "INV-003",
                "Payment method": "PayPal",
                Status: "Pending",
            },
            {
                "Room Name": "Bathroom",
                "Tenant Name": "Ella Davis",
                Description: "Cleaning service",
                Amount: 50,
                "Invoice #": "INV-004",
                "Payment method": "Cash",
                Status: "Paid",
            },
            {
                "Room Name": "Dining Room",
                "Tenant Name": "Michael Wilson",
                Description: "Furniture rental",
                Amount: 200,
                "Invoice #": "INV-005",
                "Payment method": "Credit Card",
                Status: "Pending",
            },
        ];
        const headersBill = [
            "Room Name",
            "Tenant Name",
            "Monthly Rent",
            "Balance",
            "Moved-IN Date",
        ];
        const dataBill = [
            {
                "Room Name": "Living Room",
                "Tenant Name": "John Doe",
                "Monthly Rent": 1000,
                "Balance": 2000,
                "Moved-IN Date": "2023-01-01",
            },
            {
                "Room Name": "Bedroom",
                "Tenant Name": "Alice Johnson",
                "Monthly Rent": 800,
                "Balance": 2000,
                "Moved-IN Date": "2023-02-15",
            },
            {
                "Room Name": "Kitchen",
                "Tenant Name": "Bob Smith",
                "Monthly Rent": 1200,
                "Balance": 2000,
                "Moved-IN Date": "2023-03-10",
            },
        ];
        const openAutoBill = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "block";
        };

        const closeAutoBill = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "none";
        };
        const openManualBill = () => {
            var modal = document.getElementById("manualModal");

            modal.style.display = "block";
        };

        const closeManualBill = () => {
            var modal = document.getElementById("manualModal");

            modal.style.display = "none";
        };

        return {
            options,
            dates,
            headersHistory,
            dataHistory,
            headersBill,
            dataBill,
            selectedDay,
            days,
            openAutoBill,
            closeAutoBill,
            closeManualBill,
            openManualBill
            
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 mt-16 lg:ml-64">
            <div class="flex items-center gap-2 justify-start">
                <svg
                    class="w-10 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                    viewBox="0 0 384 512"
                    fill="currentColor"
                >
                    <path
                        d="M416 176c0 97.2-93.1 176-208 176c-38.2 0-73.9-8.7-104.7-23.9c-7.5 4-16 7.9-25.2 11.4C59.8 346.4 37.8 352 16 352c-6.9 0-13.1-4.5-15.2-11.1s.2-13.8 5.8-17.9l0 0 0 0 .2-.2c.2-.2 .6-.4 1.1-.8c1-.8 2.5-2 4.3-3.7c3.6-3.3 8.5-8.1 13.3-14.3c5.5-7 10.7-15.4 14.2-24.7C14.7 250.3 0 214.6 0 176C0 78.8 93.1 0 208 0S416 78.8 416 176zM231.5 383C348.9 372.9 448 288.3 448 176c0-5.2-.2-10.4-.6-15.5C555.1 167.1 640 243.2 640 336c0 38.6-14.7 74.3-39.6 103.4c3.5 9.4 8.7 17.7 14.2 24.7c4.8 6.2 9.7 11 13.3 14.3c1.8 1.6 3.3 2.9 4.3 3.7c.5 .4 .9 .7 1.1 .8l.2 .2 0 0 0 0c5.6 4.1 7.9 11.3 5.8 17.9c-2.1 6.6-8.3 11.1-15.2 11.1c-21.8 0-43.8-5.6-62.1-12.5c-9.2-3.5-17.8-7.4-25.2-11.4C505.9 503.3 470.2 512 432 512c-95.6 0-176.2-54.6-200.5-129zM228 72c0-11-9-20-20-20s-20 9-20 20V86c-7.6 1.7-15.2 4.4-22.2 8.5c-13.9 8.3-25.9 22.8-25.8 43.9c.1 20.3 12 33.1 24.7 40.7c11 6.6 24.7 10.8 35.6 14l1.7 .5c12.6 3.8 21.8 6.8 28 10.7c5.1 3.2 5.8 5.4 5.9 8.2c.1 5-1.8 8-5.9 10.5c-5 3.1-12.9 5-21.4 4.7c-11.1-.4-21.5-3.9-35.1-8.5c-2.3-.8-4.7-1.6-7.2-2.4c-10.5-3.5-21.8 2.2-25.3 12.6s2.2 21.8 12.6 25.3c1.9 .6 4 1.3 6.1 2.1l0 0 0 0c8.3 2.9 17.9 6.2 28.2 8.4V280c0 11 9 20 20 20s20-9 20-20V266.2c8-1.7 16-4.5 23.2-9c14.3-8.9 25.1-24.1 24.8-45c-.3-20.3-11.7-33.4-24.6-41.6c-11.5-7.2-25.9-11.6-37.1-15l-.7-.2c-12.8-3.9-21.9-6.7-28.3-10.5c-5.2-3.1-5.3-4.9-5.3-6.7c0-3.7 1.4-6.5 6.2-9.3c5.4-3.2 13.6-5.1 21.5-5c9.6 .1 20.2 2.2 31.2 5.2c10.7 2.8 21.6-3.5 24.5-14.2s-3.5-21.6-14.2-24.5c-6.5-1.7-13.7-3.4-21.1-4.7V72z"
                    />
                </svg>
                <h3 class="text-3xl font-bold">Billing</h3>
            </div>
            <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />
            <div class="flex flex-row gap-2 float-right">
                <select
                    id="subject"
                    class="block w-52 px-4 py-1 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                >
                    <option v-for="option in options" :key="option">
                        {{ option }}
                    </option>
                </select>
                <select
                    id="subject"
                    class="block w-40 px-4 py-1 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                >
                    <option v-for="date in dates" :key="date">
                        {{ date }}
                    </option>
                </select>
            </div>
            <div
                class="grid grid-cols-2 lg:grid-cols-3 sm:grid-cols-2 gap-4 mb-4 mt-20 text-gray-400 dark:text-white"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2 text-orange-500">0</p>
                        <p class="text-xs">Outstanding</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2 text-green-500">0</p>
                        <p class="text-xs">Paid</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2 text-red-500">0</p>
                        <p class="text-xs">Unpaid</p>
                    </div>
                </div>
            </div>
            <!--Tenant Billing-->
            <h3 class="text-2xl font-semibold">Bill Tenant(s)</h3>
            <div class="w-full mt-2">
                <div class="w-full mb-5 mt-5">
                    <div
                        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                    >
                        <div class="rounded-t mb-0 px-4 py-3 border-0">
                            <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                                >
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
                                            v-for="header in headersBill"
                                            :key="header"
                                        >
                                            {{ header }}
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
                                        v-for="(item, rowIndex) in dataBill"
                                        :key="rowIndex"
                                    >
                                        <td
                                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                            v-for="(value, colIndex) in item"
                                            :key="colIndex"
                                        >
                                            {{ value }}
                                        </td>
                                        <td
                                            class="border-t-0 px-6 align-middle text-center border-l-0 border-r-0 text-green-500 text-xs whitespace-nowrap p-4"
                                        >
                                            <AppDropdown class="">
                                                <button >
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24"  viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>
                                                </button>
                                                <AppDropdownContent class="bg-white z-50 ">
                                                    <AppDropdownItem @click="openAutoBill()">
                                                        Auto Billing
                                                    </AppDropdownItem>
                                                    <AppDropdownItem @click="openManualBill()">
                                                        Manual Billing
                                                    </AppDropdownItem>
                                                    <AppDropdownItem :href="route('owner.tenantshistory')">
                                                        View Payments
                                                    </AppDropdownItem>
                                                </AppDropdownContent>
                                            </AppDropdown>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div
                                class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800 mt-3"
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
                                                <!----><span class="px-2"
                                                    >4</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <small>Page 1 of 4</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Billing History-->
            <h3 class="text-2xl font-semibold">Billing History</h3>
            <div class="w-full mb-5">
                <div class="w-full mt-5">
                    <div
                        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                    >
                        <div class="rounded-t mb-0 px-4 py-3 border-0">
                            <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-row flex"
                                >
                                    <a> All </a>
                                    <a> Paid </a>
                                    <a> Unpaid </a>
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
                                            v-for="header in headersHistory"
                                            :key="header"
                                        >
                                            {{ header }}
                                        </th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, rowIndex) in dataHistory"
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
                                                <!----><span class="px-2"
                                                    >4</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <small>Page 1 of 4</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--AutoBillingModal-->
            <div
                    id="defaultModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Auto Billing
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeAutoBill()"
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
                                    <div class="flex flex-row gap-3">
                                    <div>
                                        <label
                                                for="dorm_owner_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Tenant Name:</label
                                            >
                                            <input
                                                type="text"
                                                disabled
                                                id="dorm_name"
                                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Matic malalagyan ng data"
                                                required
                                            />
                                    </div>
                                    <div class="flex-grow">
                                        <label
                                                for="dorm_owner_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Room Name:</label
                                            >
                                            <input
                                                type="text"
                                                disabled
                                                id="dorm_name"
                                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Matic malalagyan ng data"
                                                required
                                            />
                                    </div></div>
                                    
                                    <div>
                                        <label
                                                for="dorm_owner_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Monthly Rent:</label
                                            >
                                            <input
                                                type="text"
                                                disabled
                                                id="dorm_name"
                                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Matic malalagyan ng data"
                                                required
                                            />
                                    </div>
                                    <!--Select Date-->
                                    <div>
                                        <label for="daySelect" class="mr-2">Select a day:</label>
                                        <select id="daySelect" v-model="selectedDay" class="border-none rounded-md py-1 bg-gray-200 mb-1 text-sm text-gray-900">
                                        <option v-for="day in days" :key="day" :value="day">{{ day }}</option>
                                        </select>
                                        <p>You selected the {{ selectedDay }} day of the month.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" value="" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Auto Bill</span>
                                    </label>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                >
                                    <button
                                        @click="closeTermsModal()"
                                        type="button"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Okay
                                    </button>
                                    <button
                                        @click="closeTermsModal()"
                                        type="button"
                                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!--ManualBillingModal-->
            <div
                    id="manualModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Manual Billing
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeManualBill()"
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
                                <div class="p-6 space-y-4">
                                    <div class="flex flex-row gap-3">
                                    <div>
                                        <label
                                                for="tenant_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Tenant Name:</label
                                            >
                                            <input
                                                type="text"
                                                disabled
                                                id="tenant_name"
                                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Matic malalagyan ng data"
                                                required
                                            />
                                    </div>
                                    <div class="flex-grow">
                                        <label
                                                for="room_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Room Name:</label
                                            >
                                            <input
                                                type="text"
                                                disabled
                                                id="room_name"
                                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Matic malalagyan ng data"
                                                required
                                            />
                                    </div>
                                </div>
                                    
                                    <div>
                                        <label
                                                for="dorm_owner_name"
                                                class="block text-sm font-medium text-gray-900"
                                                >Billing Subject:</label
                                            >
                                            <select id="subject" class="block w-full px-2 py-2 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option selected>Choose a Billing for:</option>
                                                <option value="Maintenance">Electricity</option>
                                                <option value="Cleanliness">Water</option>
                                                <option value="Noise">Internet</option>
                                                <option value="Safety and Security">Others</option>
                                            </select>
                                    </div>
                                    <div class="flex-grow">
                                        <label
                                                for="room_name"
                                                class="block text-sm font-medium text-gray-900"
                                                >Amount:</label
                                            >
                                            <input
                                                type="text"
                                                id="room_name"
                                                class=" border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                required
                                            />
                                    </div>
                                    <div>
                                            <label for="complainmessage" class="block text-sm font-medium text-gray-900">Description:</label>
                                            <textarea id="complainmessage" rows="3" class="block w-full p-3 rounded-md text-sm text-gray-800 bg-gray-100 border-1 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400" placeholder="Write a description..." required></textarea>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                >
                                    <button
                                        @click=""
                                        type="button"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Bill
                                    </button>
                                    <button
                                        @click=""
                                        type="button"
                                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </AuthenticatedLayout>
</template>
