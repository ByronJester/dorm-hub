<script>
import { ref, onMounted, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import jsPDF from "jspdf";
import "jspdf-autotable";

import ExcelJS from "exceljs";

import {
    endOfMonth,
    endOfYear,
    startOfMonth,
    startOfYear,
    subMonths,
} from "date-fns";

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
        const options = ["Jear dorm", "Dorm 2"];
        const rows = ref([]);
        const dorm = ref(null);

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
                label: "Status",
                field: "status",
            },
            {
                label: "Net Sales",
                field: "net_sales",
            },
        ]);

        const data = ref([])

        onMounted(() => {
            rows.value = page.props.dorms;

            data.value = page.props.incomeArr
        });

        const currentPage = ref(1); // Initialize to the first page
        const itemsPerPage = 10;
        const totalPages = computed(() =>
            Math.ceil(rows.value.length / itemsPerPage)
        );

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
        const searchQuery = ref("");
        const filteredRows = computed(() => {
            const query = searchQuery.value.toLowerCase().trim();
            if (!query) {
                return rows.value; // Return all rows if the search query is empty.
            }

            return rows.value.filter((row) => {
                // Modify the conditions as needed for your specific search criteria.
                return (
                    row.user.first_name.toLowerCase().includes(query) ||
                    row.user.last_name.toLowerCase().includes(query) ||
                    row.user.phone_number.toLowerCase().includes(query) ||
                    row.status.toLowerCase().includes(query) ||
                    row.property_name.toLowerCase().includes(query)
                    // Add more conditions for other columns as needed
                );
            });
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

        console.log('asdasd')
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
            data,
        };
    },
};
</script>
<template>
    <div class="flex flex-row gap-2 items-center">
        
        <p class="text-2xl font-semibold my-4">Income Report</p>

    </div>
    
    <p class="text-lg  ">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quibusdam consequatur dolorem tempora alias quisquam quasi esse, maiores minus impedit voluptas.</p>

    <div class="w-[278px] mt-5">
        <p class="text-lg">Date Range:</p>
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

    

    <div class="mt-5">
        <div class="w-[278px] mt-5">
        <p class="text-lg">Position:</p>
        </div>
        <select class="px-3 py-2  focus:ring focus:outline-none rounded w-full h-12 border bg-white ">
            <option value="[object Object]">Owner</option>
            <option value="[object Object]">User</option>
            <option value="[object Object]">Admin</option></select>
        </div>    

        <div class="mt-5">
        <p class="text-lg">Department:</p>
        <input placeholder="Enter the department" type="tel" class="px-3 py-2 max-w-full focus:ring focus:outline-none rounded w-full dark:placeholder-gray-400 h-12 border bg-white ">
        </div>


        <div class="mt-5">
        <button
              @click="exportToPDF"
              class="flex items-center px-3 py-2 bg-orange-400 rounded-md text-white shadow-lg font-semibold hover:bg-opacity-80"
              >
               Generate Income Report
            </button>
    </div>

    

    
    <!--Button-->
    
    <div class="w-full mb-5 mt-5">
          </div>
                       
              
    
            <div class="block w-full overflow-x-auto">
              
                   
                    <tbody>
                        <tr
                            v-for="(item, rowIndex) in data"
                            :key="rowIndex"
                        >
                           
                        </tr>
                        <!-- <tr
                            v-for="(row, rowIndex) in slicedRows"
                            :key="rowIndex"
                        >
                            <td
                                class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                v-for="(value, colIndex) in columns"
                                :key="colIndex"
                            >
                                <div>
                                    <div v-if="value.field === 'dorm_owner'">
                                        {{ row.user.first_name }}
                                        {{ row.user.last_name }}
                                    </div>

                                    <div
                                        v-if="value.field === 'contact_number'"
                                    >
                                        {{ row.user.phone_number }}
                                    </div>
                                    <div
                                        v-if="value.field === 'status'"
                                        class="mt-2"
                                    >
                                        <button
                                            class="bg-orange-500 p-1 mx-1 text-white rounded-sm text-xs"
                                            v-if="row.status === 'pending'"
                                            :disabled="true"
                                        >
                                            Pending
                                        </button>

                                        <button
                                            class="bg-rose-500 p-1 mx-1 text-white rounded-md text-xs"
                                            v-if="row.status === 'declined'"
                                            :disabled="true"
                                        >
                                            DECLINED
                                        </button>

                                        <button
                                            class="bg-cyan-900 p-1 text-white rounded-md text-xs"
                                            v-if="row.status === 'approved'"
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
                        </tr> -->
                    </tbody>
               
                        <div class="justify-between items-center block md:flex"> 
                            </div>
                            

                            
                        </div>
                
</template>
