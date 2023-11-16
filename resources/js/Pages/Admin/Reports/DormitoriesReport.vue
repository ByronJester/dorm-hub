<script>
import { ref, onMounted, computed} from "vue";
import { usePage, router } from "@inertiajs/vue3";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import {
    endOfMonth,
    endOfYear,
    startOfMonth,
    startOfYear,
    subMonths,
} from "date-fns";
import jsPDF from "jspdf";
import "jspdf-autotable";

import ExcelJS from "exceljs";

export default {
    component: {
        VueDatePicker,
    },
    methods: {
        exportToPDF() {
            const doc = new jsPDF();

            // Add a timestamp (current date) to the PDF
            doc.setFontSize(16);
            doc.text("Income Report", 10, 10); // Title
            const currentDate = new Date();
            const dateString = currentDate.toLocaleDateString();
            const timeString = currentDate.toLocaleTimeString().toLowerCase(); // Convert to lowercase
            const timestamp = `Export Date: ${dateString} ${timeString}`;
            doc.setFontSize(12);
            doc.text(timestamp, 10, 20);

            const margin = 30;

            // Create your data array with header and rows
            const tableData = [this.columns.map((col) => col.label)].concat(
                this.rows.map((row) => [
                    row.user.first_name + " " + row.user.last_name, // Dorm Owner's Name
                    row.user.phone_number, // Contact Number
                    // Add the remaining columns here as needed
                    row.property_name,
                    row.detailed_address,
                    row.floors_total,
                    row.rooms_total,
                    row.created_at,
                    row.status,
                ])
            );

            // Generate the table in the PDF
            doc.autoTable({
                head: [tableData[0]],
                body: tableData.slice(1),
                startY: margin,
            });

            doc.save("table-data.pdf");
        },
        async exportToExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet("Table Data");

            // Add headers to the worksheet
            const headerRow = worksheet.addRow(
                this.columns.map((column) => column.label)
            );

            // Set styles for the header row (if needed)
            headerRow.eachCell((cell) => {
                cell.fill = {
                    type: "pattern",
                    pattern: "solid",
                    fgColor: { argb: "FFD9D9D9" },
                };
                cell.font = { bold: true };
                cell.border = {
                    top: { style: "thin" },
                    left: { style: "thin" },
                    bottom: { style: "thin" },
                    right: { style: "thin" },
                };
            });

            // Add data rows to the worksheet
            this.rows.forEach((row) => {
                const rowData = this.columns.map((column) => {
                    if (column.field === "dorm_owner") {
                        return `${row.user.first_name} ${row.user.last_name}`;
                    } else if (column.field === "contact_number") {
                        return row.user.phone_number;
                    } else {
                        return row[column.field];
                    }
                });
                worksheet.addRow(rowData);
            });

            // Create a Blob from the workbook
            const blob = await workbook.xlsx.writeBuffer();
            const blobObject = new Blob([blob], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            });

            // Create a download link and trigger the download
            const link = document.createElement("a");
            link.href = window.URL.createObjectURL(blobObject);
            link.download = "table-data.xlsx";
            link.click();
        },
        printTable() {
            // Open the print dialog for the table
            const doc = new jsPDF();

            // Add a timestamp (current date) to the PDF
            doc.setFontSize(16);
            doc.text("Income Report", 10, 10); // Title
            const currentDate = new Date();
            const dateString = currentDate.toLocaleDateString();
            const timeString = currentDate.toLocaleTimeString().toLowerCase(); // Convert to lowercase
            const timestamp = `Export Date: ${dateString} ${timeString}`;
            doc.setFontSize(12);
            doc.text(timestamp, 10, 20);

            const margin = 30;

            // Create your data array with header and rows
            const tableData = [this.columns.map((col) => col.label)].concat(
                this.rows.map((row) => [
                    row.user.first_name + " " + row.user.last_name, // Dorm Owner's Name
                    row.user.phone_number, // Contact Number
                    // Add the remaining columns here as needed
                    row.property_name,
                    row.detailed_address,
                    row.status,
                    row.net_sales,
                ])
            );

            // Generate the table in the PDF
            doc.autoTable({
                head: [tableData[0]],
                body: tableData.slice(1),
                startY: margin,
            });

            doc.autoPrint();

            // Save the PDF to a temporary file
            const blob = doc.output("blob");
            const url = URL.createObjectURL(blob);
            const iframe = document.createElement("iframe");
            iframe.style.display = "none";
            iframe.src = url;
            document.body.appendChild(iframe);

            // Wait for the PDF to be displayed in the iframe
            iframe.onload = function () {
                iframe.contentWindow.print();
            };
        },
    },
    setup() {
        const page = usePage();
        const user = page.props.auth.user;
        const date = ref();
        const rows = ref([]);
        const dorm = ref(null);

        const options = ["Jear dorm", "Dorm 2"];
        const columns = ref([
            {
                label: "Dorm Owner",
                field: "dorm_owner",
            },
            {
                label: "Contact Number",
                field: "contact_number",
            },
            {
                label: "Dorm Name",
                field: "property_name",
            },
            {
                label: "Dorm Address",
                field: "detailed_address",
            },
            {
                label: "Storey Total",
                field: "floors_total",
            },

            {
                label: "Room Total",
                field: "rooms_total",
            },
            {
                label: "Date Registered",
                field: "created_at",
            },
            {
                label: "Status",
                field: "status",
            },
        ]);

        onMounted(() => {
            rows.value = page.props.dorms;
        });



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
                router.get(route("admin.reports"));
            } else {
                router.get(route("landing.page"));
            }
        };

        const currentPage = ref(1); // Initialize to the first page
        const itemsPerPage = 10;
        const totalPages = computed(() => Math.ceil(rows.value.length / itemsPerPage));

        const slicedRows = computed(() => {
        const startIndex = (currentPage.value - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        return filteredRows.value.slice(startIndex, endIndex);
        });

        const changePage = (pageChange) => {
            const newPage = currentPage.value + pageChange;
            if (newPage >= 1 && newPage <= totalPages.value) {
                currentPage.value = newPage;
            }
        };

        //search function
        const searchQuery = ref('');
        const filteredRows = computed(() => {
        const query = searchQuery.value.toLowerCase().trim();
        if (!query) {
            return rows.value; // Return all rows if the search query is empty.
        }

        return rows.value.filter(row => {
            // Modify the conditions as needed for your specific search criteria.
            return (
            row.user.first_name.toLowerCase().includes(query) ||
            row.user.last_name.toLowerCase().includes(query) ||
            row.user.phone_number.toLowerCase().includes(query)||
            row.status.toLowerCase().includes(query)||
            row.floors_total.toLowerCase().includes(query)||
            row.property_name.toLowerCase().includes(query)
            // Add more conditions for other columns as needed
            );
        });
        });
        return {
            presetDates,
            columns,
            rows,
            dorm,
            date,
            options,
            filteredRows,
            searchQuery,
            currentPage,
            itemsPerPage,
            itemsPerPage,
            changePage,
            slicedRows,
            back,
        };
    },
};
</script>
<template>
    <div class="flex flex-row gap-2 items-center">
        <button @click="back()" class="border-2 border-gray-500 px-3 py-1  text-gray-500 hover:text-white hover:border-orange-400 rounded-md hover:bg-orange-400 "><span>
            <i class="fa-solid fa-arrow-left fa-lg" ></i>
        </span></button>
        <p class="text-2xl font-semibold my-4">Dormitories Report</p>
    </div>
    <div class="w-[278px] mt-5">
        <p class="text-sm">Date Range:</p>
        <VueDatePicker
            v-model="date"
            range
            :preset-dates="presetDates"
            :enable-time-picker="false"
        >
            <template #preset-date-range-button="{ label, value, presetDate }">
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
        <button class="px-3 py-2 bg-orange-400 rounded-md text-white shadow-lg font-semibold hover:bg-opacity-25">Generate</button>
    </div>
    <div class="w-full mb-5 mt-5">
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                >
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div
                                    class="relative w-full  sm:flex-row sm:justify-between sm:items-center gap-5 file:px-4 max-w-full flex-col flex "
                                >
                                <div class="mb-3 sm:flex-row flex-col flex gap-3">

                                    <div class="flex flex-row gap-2">
                                        <button
                                    @click="exportToPDF"
                                    class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white"
                                >
                                    PDF
                                </button>
                                <button
                                    @click="exportToExcel"
                                    class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white"
                                >
                                    Excel
                                </button>
                                <button
                                    @click="printTable"
                                    class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white"
                                >
                                    Print
                                </button>

                                    </div>
                                </div>
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
                                                v-model="searchQuery"
                                            />
                                        </div>
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
                                        v-for="column in columns"
                                        :key="column.field"
                                    >
                                        {{ column.label }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, rowIndex) in slicedRows"
                                    :key="rowIndex"
                                >
                                    <td
                                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                        v-for="(value, colIndex) in columns"
                                        :key="colIndex"
                                    >
                                        <div>
                                            <div
                                                v-if="
                                                    value.field === 'dorm_owner'
                                                "
                                            >
                                                {{ row.user.first_name }}
                                                {{ row.user.last_name }}
                                            </div>


                                            <div
                                                v-if="
                                                    value.field ===
                                                    'contact_number'
                                                "
                                            >
                                                {{ row.user.phone_number }}
                                            </div>
                                            <div
                                                v-if="value.field === 'status'"
                                                class="mt-2"
                                            >
                                                <button
                                                    class="bg-orange-500 p-1 mx-1 text-white rounded-sm text-xs"
                                                    v-if="
                                                        row.status === 'pending'
                                                    "
                                                    :disabled="true"
                                                >
                                                    Pending
                                                </button>

                                                <button
                                                    class="bg-rose-500 p-1 mx-1 text-white rounded-md text-xs"
                                                    v-if="
                                                        row.status ===
                                                        'declined'
                                                    "
                                                    :disabled="true"
                                                >
                                                    DECLINED
                                                </button>

                                                <button
                                                    class="bg-cyan-900 p-1 text-white rounded-md text-xs"
                                                    v-if="
                                                        row.status ===
                                                        'approved'
                                                    "
                                                    :disabled="true"
                                                >
                                                    {{ row.status }}
                                                </button>
                                            </div>
                                            <div v-else>
                                                {{ row[value.field] }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div
                            class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800"
                        >
                        <div class="block w-full overflow-x-auto">
                                <div class="justify-between items-center block md:flex">
                                    <div class="flex items-center justify-start flex-wrap mb-3">
                                    <button
                                        @click="changePage(-1)"
                                        :disabled="currentPage == 1"
                                        :class="{
                                            'hidden': currentPage == 1,
                                        }"
                                        type="button"
                                        class="text-gray-500 bg-white mr-5 hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"

                                    >
                                        Previous
                                    </button>
                                    <button
                                        @click="changePage(1)"
                                        :disabled="currentPage === totalPages"
                                        type="button"
                                        class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"

                                    >
                                        Next
                                    </button>
                                    </div>
                                    <div class="flex items-center justify-center">
                                    <small>Page {{ currentPage }}</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
</template>
