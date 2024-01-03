<script>
import { ref, computed, watch } from "vue";
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
            const address = contacts.address;



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
            const address = contacts.address;



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
        const page = usePage();
        const user = page.props.auth.user;
        const date = ref();
        const options = ["M.D.R Apartment", "Dorm2"];
        const numoptions = ["5", "10", "15", "20"];
        const header = ["Tenant Name", "Transaction", "Amount"]

        const presetDates = ref([
            { label: "Today", value: [new Date(), new Date()] },
            // {
            //     label: "Today (Slot)",
            //     value: [new Date(), new Date()],
            //     slot: "preset-date-range-button",
            // },
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


        const data = page.props.incomeReports
        const allTenants = page.props.allTenants

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        const slicedRows = ref([]);
        const datarange = ref([]);

        watch(date, (newDate) => {
            console.log(data)
            if (newDate.length == 2) {
                slicedRows.value = filterDataByDateRange(newDate[0], newDate[1], data)
                datarange.value = newDate;
            } else {
                slicedRows.value = data;
            }
        })

        const filterDataByDateRange = (startDate, endDate, dataArray) => {
            return dataArray.filter(item => {
                const itemDate = new Date(item.created_at);
                return new Date(itemDate) >= new Date(startDate) && new Date(itemDate) <= new Date(endDate);
            });
        }
        const contacts = page.props.contact;

        const exportToPDF = () => {
            const doc = new jsPDF();

            const currentDate = new Date();
            const logo = "/images/logo.png";
            const dateString = currentDate.toLocaleDateString();
            const timeString = currentDate.toLocaleTimeString().toLowerCase(); // Convert to lowercase
            const timestamp = `Export Date: ${dateString} ${timeString}`;
            const emails = contacts.email;
            const phone = contacts.phone;
            const facebook = contacts.facebook;
            const ig = `Ig: ${contacts.ig}`;
            const address = contacts.address;


            doc.addImage(logo, 'PNG', 141, 10, 55, 13);
            doc.setFontSize(10);
            doc.text(emails, 163, 30)
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
            doc.text("Income Report", 15, 60)

            const margin = 65;

            // Create your data array with header and rows
            const tableData = [header].concat(
                slicedRows.value.map((row) => [
                    row.name,
                    row.description,
                    moneyFormat(row.amount)
                ])
            );
            console.log(tableData)

            let total = slicedRows.value.reduce((sum, el) => sum + el.amount, 0);

            var body = [...slicedRows.value.map(el => [el.name, el.description, { content: moneyFormat(el.amount), styles: { halign: 'right' } }]),
            [{
                content: `Total ${moneyFormat(total)}`, colSpan: 3,
                styles: { halign: 'right' }
            }]]
            // Generate the table in the PDF
            doc.autoTable({
                head: [tableData[0]],
                body: body,
                startY: margin,
                theme: 'grid',
                styles: { textColor: [0, 0, 0], fontStyle: 'normal', overflow: 'linebreak' },
            });

            doc.save("income-report-" + timestamp + '.pdf');
        }
        return {
            exportToPDF,
            data,
            date,
            presetDates,
            options,
            numoptions,
            header,
            back,
            moneyFormat
        };
    },
};
</script>
<template>
    <div class="flex flex-row gap-2 items-center">

        <p class="text-2xl font-semibold my-4">Income Report</p>
    </div>
    <p class="text-lg  "> This report provides a comprehensive overview of the dormitory's financial performance, allowing
        stakeholders to assess its profitability and make informed decisions.</p>


    <div class="w-[278px] mt-5">
        <div>
            <p class="text-sm">Date Range:</p>
            <VueDatePicker v-model="date" range :preset-dates="presetDates" :enable-time-picker="false">
                <template #preset-date-range-button="{ label, value, presetDate }">
                    <span class="px-3" role="button" :tabindex="0" @click="presetDate(value)"
                        @keyup.enter.prevent="presetDate(value)" @keyup.space.prevent="presetDate(value)">
                        {{ label }}
                    </span>
                </template>
            </VueDatePicker>
        </div>

    </div>


    <!--Button-->

    <div class="w-full mb-5 mt-5">
        <button @click="exportToPDF"
            class="flex items-center px-3 py-2 bg-orange-400 rounded-md text-white shadow-lg font-semibold hover:bg-opacity-80">
            Generate Income Report
        </button>
    </div>
</template>
