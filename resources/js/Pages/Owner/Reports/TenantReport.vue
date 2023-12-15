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
import jsPDF from "jspdf";
import "jspdf-autotable";

export default {
    component: {
        VueDatePicker,
    },

    methods: {
        exportToPDF() {
            const doc = new jsPDF();

            const page = usePage();
            const contacts = page.props.contact;
            const currentDate = new Date();
            const logo = "/images/logo.png";
            const dateString = currentDate.toLocaleDateString();
            const timeString = currentDate.toLocaleTimeString().toLowerCase(); // Convert to lowercase
            const timestamp = `Export Date: ${dateString} ${timeString}`;
            const emails = contacts.email;
            const phone = contacts.phone;
            const facebook = contacts.facebook;
            const ig = `Ig: ${contacts.ig}`;
            const address =  contacts.address;



            doc.addImage(logo, 'PNG', 141, 10, 55, 13);
            doc.setFontSize(10);
            doc.text(emails, 150, 30)
            doc.setFontSize(10);
            doc.text(phone, 175, 36)
            doc.setFontSize(10);
            doc.text(facebook, 159, 41)
            doc.setFontSize(10);
            doc.text(ig, 174, 46)
            doc.setFontSize(10);
            doc.text(address, 146, 52)
            doc.setFontSize(10);
            doc.text(timestamp, 135, 60);
            doc.setFontSize(15);
            doc.text("Reservation Records", 15, 60)

            const margin = 65;

            // Create your data array with header and rows
            const tableData = [this.header].concat(
                this.slicedRows.map((row) => [

                ])
            );

            // Generate the table in the PDF
            doc.autoTable({
                head: [tableData[0]],
                body: tableData.slice(1),
                startY: margin,
                theme: 'grid',
                styles: { textColor: [0, 0, 0], fontStyle: 'normal', overflow: 'linebreak' },
            });

            doc.save("table-data.pdf");
        },
            printTable() {
            const doc = new jsPDF();

            const page = usePage();
            const contacts = page.props.contact;
            const currentDate = new Date();
            const logo = "/images/logo.png";
            const dateString = currentDate.toLocaleDateString();
            const timeString = currentDate.toLocaleTimeString().toLowerCase(); // Convert to lowercase
            const timestamp = `Export Date: ${dateString} ${timeString}`;
            const emails = contacts.email;
            const phone = contacts.phone;
            const facebook = contacts.facebook;
            const ig = `Ig: ${contacts.ig}`;
            const address =  contacts.address;



            doc.addImage(logo, 'PNG', 141, 10, 55, 13);
            doc.setFontSize(10);
            doc.text(emails, 150, 30)
            doc.setFontSize(10);
            doc.text(phone, 175, 36)
            doc.setFontSize(10);
            doc.text(facebook, 159, 41)
            doc.setFontSize(10);
            doc.text(ig, 174, 46)
            doc.setFontSize(10);
            doc.text(address, 146, 52)
            doc.setFontSize(10);
            doc.text(timestamp, 135, 60);
            doc.setFontSize(15);
            doc.text("Tenant Report", 15, 60)

            const margin = 65;

            // Create your data array with header and rows
            const tableData = [this.headersReserve].concat(
                this.slicedRows.map((row) => [
                ])
            );

            // Generate the table in the PDF
            doc.autoTable({
                head: [tableData[0]],
                body: tableData.slice(1),
                startY: margin,
                theme: 'grid',
                styles: { textColor: [0, 0, 0], fontStyle: 'normal', overflow: 'linebreak' },
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
        const date = ref();
        const options = ["M.D.R Apartment", "Dorm2"];
        const numoptions = ["5", "10", "15", "20"];
        const header=["Tenant Name", "Contact Inforamtion", "Room Name", "Monthly Rent", "Move-In Date", "Move-Out Date"]

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
                router.get(route("owner.reports"));
            } else {
                router.get(route("landing.page"));
            }
        };

        const page = usePage();
        const user = page.props.auth.user;

        const data = page.props.tenantReports

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        return {
            date,
            presetDates,
            options,
            numoptions,
            header,
            back,
            data,
            moneyFormat
        };
    },
};
</script>
<template>
     <div class="flex flex-row gap-2 items-center">
       
       <p class="text-2xl font-semibold my-4">Tenant Report</p>
   </div>
   <p class="text-lg  ">This report serves various purposes, including record-keeping, safety, and management. </p>
   
   <div class="flex flex-row items-center justify-between gap-2 w-full">
       <div class="flex flex-row w-full items-center gap-2">
           <div>
               <p class="text-sm">Dorm:</p>
               <select
                   id="subject"
                   class="block w-56 px-4 py-1.5 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
               >
                   <option v-for="option in options" :key="option">
                       {{ option }}
                   </option>
               </select>
           </div>
       </div>
   </div>
   <div class="w-[278px] mt-5">
       <div>
       <p class="text-sm">Date Range:</p>
       <VueDatePicker
           v-model="date"
           range
           :preset-dates="presetDates"
           :enable-time-picker="false"
       >
           <template #preset-date-range-button="{ label, value, presetDate }">
               <span
                   class="px-3"
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

   </div>

 
   <!--Button-->

   <div class="w-full mb-5 mt-5">
                  <button
             @click="exportToPDF"
             class="flex items-center px-3 py-2 bg-orange-400 rounded-md text-white shadow-lg font-semibold hover:bg-opacity-80"
             >
              Generate Tenant Report
           </button>
               </div>
</template>
