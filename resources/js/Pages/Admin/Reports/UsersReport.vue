<script>
import { ref } from "vue";
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
        const numoptions = ["5", "10", "15", "20"];
        const header=["Name", "Username", "User Type", "Contact No.","Date Registered"]

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

        const data = page.props.userArr
        return {
            date,
            presetDates,
            options,
            numoptions,
            header,
            data,
            back
        };
    },
};
</script>
<template>
    <div class="flex flex-row gap-2 items-center">
        
        <p class="text-2xl font-semibold my-4">User(s) Report</p>

    </div>

    <p class="text-lg  ">This report include various details about the individuals associated with the dormitory. </p>
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

    <!-- <div class="mt-5">
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
        </div> -->


    <!--Button-->
    <div class="mt-5">
       
        <button  @click="exportToPDF"
         class="flex items-center px-3 py-2 bg-orange-400 rounded-md text-white shadow-lg font-semibold hover:bg-opacity-80">
            Generate Users Report
         </button>
    </div>


</template>
