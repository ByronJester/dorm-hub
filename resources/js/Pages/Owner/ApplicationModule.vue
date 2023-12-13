
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
    import DataTable from 'primevue/datatable'
    import Button from 'primevue/button';
    import Tag from 'primevue/tag';
    import Column from 'primevue/column';
    import ColumnGroup from 'primevue/columngroup';   // optional
    import Row from 'primevue/row';
    import { FilterMatchMode, FilterOperator } from 'primevue/api';

    export default {
        components: {
            AuthenticatedLayout,
            AppDropdown,
            AppDropdownContent,
            AppDropdownItem,
            VueGoodTable,
            DataTable,
            Button,
            Tag,
            Column,
            Row,
            ColumnGroup,
        },
        data(){
            return{
                selectedContent: 'applicants',
            }
        },
        setup() {

            const page = usePage()
            const rows = ref([])
            const filters = ref();
            const reason = ref('');
            onMounted(() => {
                rows.value = page.props.applications
            });

            const user = page.props.auth.user;

            const application = ref();
            const payments = ref([])
            const viewPayment = ref(false)

            const openModal = (arg, isViewPayment = false) => {
                viewPayment.value = isViewPayment

                if(!isViewPayment) {
                    application.value = arg

                    var modal = document.getElementById("tenantModal");

                    modal.style.display = "block";
                } else {
                    payments.value = arg

                    var modal = document.getElementById("tenantModal");

                    modal.style.display = "block";
                }
            }

            const closeModal = () => {
                var modal = document.getElementById("tenantModal");

                modal.style.display = "none";
            }

            const changeStatus = (status, id) => {
                swal({
                    title: `Are you sure to ${!status ? 'deny' : 'approve'} the rental application of this tenant?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(){
                    axios.post(route('change.application.status', status), {id: id, status: status})
                        .then(response => {
                            swal("Success!", `You successfully ${!status ? 'denied' : 'approved'} the rental application of this tenant.`, "success");

                            setTimeout(function () {
                                location.reload()
                            }, 1500);
                        })
                        .catch(error => {
                            errors.value = error.response.data.errors
                        })
                });


            }

            const moneyFormat = (amount) => {
                if(amount == null || amount == '') {
                    amount = 0;
                }

                amount = parseFloat(amount).toFixed(2)

                return '₱ ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');;
            }

            const markAsPaid = (id) => {

                swal({
                    title: `Are you sure to mark as paid this payment?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(){
                    axios.post(route('payment.mark-as-paid'), {id: id})
                        .then(response => {
                            swal("Success!", `You successfully mark as paid this payment.`, "success");

                            setTimeout(function () {
                                location.reload()
                            }, 1500);
                        })
                        .catch(error => {
                            errors.value = error.response.data.errors
                        })
                });

            }

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

            const formatTime = (t) => {
                // Creating a date object without specifying the date
                const date = new Date(`2000-01-01T${t}`);

                const options = { hour: 'numeric', minute: 'numeric', hour12: true };

                return date.toLocaleTimeString('en-US', options);
            }


            //Rent
            var dataRent = [];

            const applications = page.props.applications;

            for (let y = 0; y < applications.length; y++) {
                dataRent.push({
                    dorm_name: applications[y].dorm.property_name,
                    room_name: applications[y].room.name,
                    tenant_name: applications[y].tenant.name,
                    source_of_income: applications[y].income_information.source_of_income,
                    monthly_income: moneyFormat(applications[y].income_information.monthly_income),
                    move_in: !applications[y].move_in ? 'N/A' : formatDate(applications[y].move_in),
                    move_out: 'N/A',
                    status: applications[y].status,
                    action: applications[y]
                });
            }

            const initFilters = () => {
                filters.value = {
                    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                    dorm_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    room_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    tenant_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    source_of_income: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    monthly_income: { value: null, matchMode: FilterMatchMode.IN },
                    move_in: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                    move_out: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                    status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },

                };
            };

            initFilters();

            const clearFilter = () => {
                initFilters();
            };



            const selectedApplication = ref(null)

            const openProofModal = (arg) => {
                selectedApplication.value = arg

                var modal = document.getElementById("proofModal");

                modal.style.display = "block";
            };

            const closeProofModal = () => {
                var modal = document.getElementById("proofModal");

                modal.style.display = "none";
            };

            const closeReasonModal = () => {
                var modal = document.getElementById("reasonModal");

                modal.style.display = "none";
            }

            const openReasonModal = (arg) => {
                selectedApplication.value = arg
                var modal = document.getElementById("reasonModal");

                modal.style.display = "block";

            }

            const approveApplication = (arg) => {
                const data = {
                    tenant_id: arg.tenant_id,
                    owner_id: arg.owner_id,
                    dorm_id: arg.dorm_id,
                    room_id: arg.room_id,
                    move_in: arg.move_in
                }

                swal({
                    title: `Are you sure to approve this application?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(){
                    axios.post(route('approve.application', arg.id), data)
                        .then(response => {
                            swal("Success!", `You successfully approved this application.`, "success");

                            setTimeout(function () {
                                location.reload()
                            }, 3000);
                        })
                        .catch(error => {
                            errors.value = error.response.data.errors
                        })
                });
            }

            const declineApplication = (arg) => {
                swal({
                    title: `Are you sure to decline this application?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(){
                    axios.post(route('decline.application', arg.id), {room_id: arg.room_id})
                        .then(response => {
                            swal("Success!", `You successfully declined this application.`, "success");

                            setTimeout(function () {
                                location.reload()
                            }, 3000);
                        })
                        .catch(error => {
                            errors.value = error.response.data.errors
                        })
                });
            }

            return {
                filters,
                clearFilter,
                dataRent,
                rows,
                user,
                application,
                viewPayment,
                openModal,
                closeModal,
                changeStatus,
                moneyFormat,
                markAsPaid,
                openProofModal,
                closeProofModal,
                applications,
                selectedApplication,
                approveApplication,
                declineApplication,
                formatDate,
                formatTime,
                reason,
                openReasonModal,
                closeReasonModal
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
                <h3 class="text-3xl">Applicants</h3>
            </div>
            <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />
            <div class="card">
                <DataTable v-model:filters="filters" :value="dataRent" tableStyle="min-width: 50rem" :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="10"
                :globalFilterFields="['dorm_name', 'room_name', 'tenant_name', 'monthly_income','source_of_income', 'move_in', 'move_in', 'status']">
                <template #header>
                    <div class="flex items-center justify-between">
                        <Button type="button" class="rounded-lg border-green-400 border px-3 py-2.5" icon="fa-solid fa-filter-circle-xmark" label="Clear" outlined @click="clearFilter()" />
                        <span class="p-input-icon-left">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input v-model="filters['global'].value" placeholder="Keyword Search" class="pl-10 rounded-lg" />
                        </span>
                    </div>
                </template>
                <template #empty> No application found. </template>
                    <Column field="dorm_name" header="Dorm Name" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.dorm_name }}
                        </template>
                    </Column>
                    <Column field="room_name" header="Room Name" sortable dataType="date" style="min-width: 10rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.room_name }}
                        </template>
                    </Column>
                    <Column field="tenant_name" header="Tenant Name" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.tenant_name }}
                        </template>
                    </Column>
                    <Column field="monthly_income" header="Monthly Income" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.monthly_income }}
                        </template>
                    </Column>
                    <Column header="Moved-in Date" field="move_in" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            {{ formatDate(data.move_in) }}
                        </template>
                    </Column>
                    <Column header="Move-out Date" field="move_out" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            {{ formatDate(data.move_out) }}
                        </template>
                    </Column>
                    <Column header="Status" field="status" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.status }}
                        </template>
                    </Column>
                    <Column header="Action" style="min-width: 5rem" class="border-b">
                        <template #body ="{data}">
                            <button
                                class="hover:text-orange-400"
                                
                                @click="openProofModal(data)"
                            >
                                <!-- Use v-if to conditionally render the appropriate icon -->
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </template>
                    </Column>
                </DataTable>
            </div>
            <!--Proof of income View Modal-->
            <div
                    id="proofModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-lg max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Proof of Income
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeProofModal()"
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
                                <div class="p-6 space-y-6" v-if="selectedApplication">
                                    <img :src="selectedApplication.tenant.income_information.proof" class="w-full h-[300px]"/>
                                </div>
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                >
                                    <button
                                        @click="openReasonModal(selectedApplication)"
                                        type="button"
                                        v-if="selectedApplication && selectedApplication.status == 'rent' && !selectedApplication.is_approved"
                                        class="text-white bg-red-600 hover:bg-opacity-25 focus:ring-4 focus:outline-none focus:ring-rose-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-rose-500 dark:hover:bg-rose-700 dark:focus:ring-rose-800"
                                    >
                                        Decline
                                    </button>
                                    <button
                                        @click="approveApplication(selectedApplication)"
                                        type="button"
                                        v-if="selectedApplication && selectedApplication.status == 'rent' && !selectedApplication.is_approved"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Approve
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    id="reasonModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-sm max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Add reason
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeReasonModal()"
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
                                    <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Reason:</label>
                                    <textarea v-model="reason" id="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 " placeholder="Write your reason here..."></textarea>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                    v-if="application"
                                >
                                    <Toast />
                                    <ConfirmDialog></ConfirmDialog>
                                    <button
                                        @click="closeReasonModal()"
                                        type="button"
                                        class="text-white bg-red-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="decline(application.id, reason)"
                                        type="button"
                                        class="text-white bg-green-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </AuthenticatedLayout>
</template>


