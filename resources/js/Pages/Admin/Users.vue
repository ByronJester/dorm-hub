<script>
    import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import 'vue-good-table-next/dist/vue-good-table-next.css'
    import { VueGoodTable } from 'vue-good-table-next';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';
    import DataTable from 'primevue/datatable';
    import Button from 'primevue/button';
    import Tag from 'primevue/tag';
    import Column from 'primevue/column';
    import ColumnGroup from 'primevue/columngroup';   // optional
    import Row from 'primevue/row';      
    import { FilterMatchMode, FilterOperator } from 'primevue/api';
    import { useConfirm } from "primevue/useconfirm";
    import { useToast } from "primevue/usetoast";
    import ConfirmDialog from 'primevue/confirmdialog';
    import Toast from 'primevue/toast';
    import Image from 'primevue/image';

    export default {
        components: {
            AuthenticatedLayout,
            VueGoodTable,
            DataTable,
            Column,
            ColumnGroup,
            Row,
            Button,
            Tag,
            ConfirmDialog,
            Toast,
            Image
        },
        setup() {
            const page = usePage()
            const rows = ref([])
            const statuses = ref(['approved', 'decline', 'pending']);
            const filters = ref();
            const confirm = useConfirm();
            const toast = useToast();
            const reason = ref('');
            const errors = ref('');

            onMounted(() => {
                rows.value = page.props.users.filter(user => ['pending', 'decline', 'approved'].includes(user.status));
            });

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
                    name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    user_type: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    phone_number: { value: null, matchMode: FilterMatchMode.IN },
                    created_at: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                    status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                   
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

            

            const openModal = (arg) => {
                id_picture.value = arg.id_picture
                user.value = arg

                var modal = document.getElementById("userModal");

                modal.style.display = "block";

            }

            const closeReasonModal = () => {
                var modal = document.getElementById("reasonModal");

                modal.style.display = "none";
            }

            const openReasonModal = (arg) => {
                user.value = arg
                var modal = document.getElementById("reasonModal");

                modal.style.display = "block";

            }

            const closeModal = () => {
                var modal = document.getElementById("userModal");

                modal.style.display = "none";
            }



        const decline = ( id, reason) => {
            confirm.require({
                message: 'Are you sure you want to decline this user?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    axios
                        .post(route("user.changestatus.decline",  { reason: reason }), { id: id })
                        .then((response) => {
                            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Successfully decline the user', life: 3000 });
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        })
                        .catch((error) => {
                            errors.value = error.response.data.errors;
                        });
                    
                },
                reject: () => {
                    
                }
            });
        };

        const approved = (id) => {
            confirm.require({
                message: 'Are you sure you want to approve this user?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    axios
                        .post(route('user.approved'), { id: id })
                        .then((response) => {
                            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Successfully approve the user', life: 3000 });
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        })
                        .catch((error) => {
                            errors.value = error.response.data.errors;
                        });
                    
                },
                reject: () => {
                }
            });
        };

        const id_picture = ref(null)
        const selfie_id_picture = ref(null)
        const statuss = ref(null);
        const reasons = ref(null)
        const user = ref(null)
        const openTermsModal = (arg) => {
            console.log(arg)
            id_picture.value = arg.id_picture
            selfie_id_picture.value = arg.selfie_id_picture
            statuss.value = arg.status
            reasons.value = arg.reason
            user.value = arg

            var modal = document.getElementById("defaultModal");

            modal.style.display = "block";
        };

        const closeTermsModal = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "none";
        };



            return {
                rows,
                id_picture,
                user,
                openModal,
                closeModal,
                openTermsModal,
                closeTermsModal,
                selfie_id_picture,
                filters,
                clearFilter,
                getSeverity,
                formatDate,
                statuses,
                decline,
                approved,
                reason,
                statuss,
                reasons,
                openReasonModal,
                closeReasonModal
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 lg:ml-64">
            <div class="flex items-center justify-start gap-3  mt-16">
                <i class="fa-solid fa-user fa-2xl"></i>
                <h3 class="text-3xl">User Verification</h3>
            </div>
            <div
                class="grid grid-cols-2 lg:grid-cols-3 sm:grid-cols-2 gap-4 mb-4 mt-4 text-gray-400 dark:text-white"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                rows.filter((x) => {
                                    return (
                                        x.status == 'decline'
                                    );
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Declined Users</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                rows.filter((x) => {
                                    return x.status == 'approved'
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Approved Users</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                rows.filter((x) => {
                                    return x.status == 'pending'
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Pending Users</p>
                    </div>
                </div>
            </div>

            <div class="card mb-10">
                <DataTable v-model:filters="filters" :value="rows" tableStyle="min-width: 50rem" :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="10"
                :globalFilterFields="['name', 'phone_number', 'user_type', 'created_at', 'status']">
                <template #header>
                    <div class="flex items-center justify-between">
                        <Button type="button" class="rounded-lg border-green-400 border px-3 py-2.5" icon="fa-solid fa-filter-circle-xmark" label="Clear" outlined @click="clearFilter()" />
                        <span class="p-input-icon-left">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input v-model="filters['global'].value" placeholder="Keyword Search" class="pl-10 rounded-lg" />
                        </span>
                    </div>
                </template>
                <template #empty> No user found. </template>
                    <Column field="name" header="Name" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.name }}
                        </template>
                    </Column>
                    <Column field="created_at" header="Register Date" sortable dataType="date" style="min-width: 10rem" class="border-b">
                        <template #body="{ data }">
                            {{ formatDate(data.created_at) }}
                        </template>
                    </Column>
                    <Column field="user_type" header="User type" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.user_type }}
                        </template>
                    </Column>
                    <Column header="Status" field="status" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="getSeverity(data.status)" />
                        </template>
                    </Column>
                    <Column header="Action" style="min-width: 5rem" class="border-b">
                        <template #body ="{data}">
                            <button
                                class="hover:text-orange-400"
                                :class="{
                                    'cursor-not-allowed': data.status == 'approved',
                                    'cursor-pointer': data.status == 'pending' && data.status == 'decline'
                                }"
                                :disabled="data.status=='approved'"
                                @click="openTermsModal(data)"
                            >
                                <!-- Use v-if to conditionally render the appropriate icon -->
                                <i v-if="data.status =='pending' || data.status == 'decline' || data.status == null" class="fa-solid fa-eye"></i>
                                <i v-else class="fa-solid fa-eye-slash"></i>
                            </button>
                        </template>
                    </Column>
                </DataTable>
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
                                    v-if="user"
                                >
                                    <Toast />
                                    <ConfirmDialog></ConfirmDialog>
                                    <button
                                        v-if="user.status == 'pending'"
                                        @click="closeReasonModal()"
                                        type="button"
                                        class="text-white bg-red-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="decline(user.id, reason)"
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
            <div
                    id="defaultModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Validation ID
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeTermsModal()"
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
                                
                                <div class="w-full bg-red-400 p-3 text-white flex items-center justify-center" v-if="statuss=='decline'">
                                    You declined this user. Reason: {{ reasons }}
                                </div>
                                <div class="w-full bg-orange-400 p-3 text-white flex items-center justify-center" v-if="statuss=='pending'">
                                    This user is waiting for your approval
                                </div>
                                <!-- Modal body -->
                                <div class="p-6 space-y-6">
                                    <label class="text-lg font-bold">ID Picture</label>
                                    <div class="card flex justify-center" v-if="id_picture">
                                        <Image :src="id_picture" alt="Image" width="400"  preview />
                                    </div>
                                    <hr />
                                    <label class="text-lg font-bold">Selfie ID Picture</label> <br/>
                                    <div class="card flex justify-center" v-if="selfie_id_picture">
                                        <Image :src="selfie_id_picture" alt="Image" width="400" preview />
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                    v-if="user"
                                >
                                    <Toast />
                                    <button
                                        v-if="user.status == 'pending'"
                                        @click="openReasonModal(user), closeTermsModal()"
                                        type="button"
                                        class="text-white bg-red-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Decline
                                    </button>
                                    <button
                                        v-if="user.status == 'pending'"
                                        @click="approved(user.id)"
                                        type="button"
                                        class="text-white bg-green-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Approve
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
</style>