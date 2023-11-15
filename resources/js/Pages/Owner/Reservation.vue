
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
    import jsPDF from "jspdf";
    import "jspdf-autotable";
    import ExcelJS from "exceljs";

    export default {
        components: {
            AuthenticatedLayout,
            AppDropdown,
            AppDropdownContent,
            AppDropdownItem,
            VueGoodTable
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
            const tableData = [this.headersReserve].concat(
                this.slicedRows.map((row) => [
                        row.dorm_name,
                        row.room_name,
                        row.tenant_name,
                        row.visit_date,
                        row.time_visit,
                        row.remaining,
                        row.status
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
            doc.text("Reservation Records", 15, 60)

            const margin = 65;

            // Create your data array with header and rows
            const tableData = [this.headersReserve].concat(
                this.slicedRows.map((row) => [
                        row.dorm_name,
                        row.room_name,
                        row.tenant_name,
                        row.visit_date,
                        row.time_visit,
                        row.remaining,
                        row.status
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
            const isMobileView = ref(false)
            isMobileView.value = screen.width < 600;

            const page = usePage()

            const user = page.props.auth.user;


            const getDaysRemaining = (reservation_date, expired_date) => {
                const date1 = new Date(reservation_date);
                const date2 = new Date(expired_date);

                // To calculate the time difference
                const timeDiff = Math.abs(date2.getTime() - date1.getTime());

                // Convert time difference to days
                return Math.ceil(timeDiff / (1000 * 3600 * 24));
            }

            const getStatus = (isActive) => {
                const active = isActive == true;
                const expired = isActive == false;
                let status = '';

                // To calculate the time difference

                // Assuming you have some logic to determine if it's active or expired
                if (active) {
                    status = 'Active';
                } else if (expired) {
                    status = 'Expired';
                }

                return status;
            }

            var dataReserve = [];
            

            const headersReserve = ["Dorm Name", "Room Name", "Tenant Name", "Date Visit", "Time Visit", 'Remaining Days', 'Status'];

            const reservations = page.props.reservations;

            for (let x = 0; x < reservations.length; x++) {
                dataReserve.push(
                    {
                        dorm_name: reservations[x].dorm.property_name,
                        room_name: reservations[x].room.name,
                        tenant_name: reservations[x].tenant_user.name,
                        visit_date: reservations[x].check_date,
                        time_visit: reservations[x].check_time,
                        remaining: getDaysRemaining(reservations[x].created_at, reservations[x].expired_at),
                        status: getStatus(reservations[x].is_active),
                        created_at: reservations[x].created_at
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

                const slicedAndSorted = filteredDataReserve.value
                    .slice(startIndex, endIndex)
                    .sort((a, b) => {
                        const dateA = new Date(a.created_at);
                        const dateB = new Date(b.created_at);
                        return dateB - dateA;
                    });

                return slicedAndSorted;
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
                                <p class="text-xl mb-5 font-bold">Reservation Records</p>
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
                                                    v-model="searchQueryReserve"
                                                    required
                                                />
                                        </div>
                                       
                                         
                                    </form> 
                                    <div class="flex flex-row items-center gap-2 font-semibold">
                                                <button 
                                                @click="exportToPDF()"
                                                class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    PDF
                                                </button>
                                                <button
                                                @click="printTable()"
                                                 class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    Print
                                                </button>    
                                            </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table
                                class="items-center w-full bg-transparent border-collapse"
                            >
                                <thead>
                                    <tr class="border-y">
                                        <th
                                            class="px-6 align-middle py-3 text-xs uppercase whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
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
                                  
                                            {{ colIndex !== 'created_at' ? value : '' }}
                                        
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


