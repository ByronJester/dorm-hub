<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import { Head } from "@inertiajs/vue3";
import "vue-good-table-next/dist/vue-good-table-next.css";
import { VueGoodTable } from "vue-good-table-next";
import { ref, computed, reactive, watch, onMounted, defineProps } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import axios from "axios";
import jsPDF from "jspdf";
import "jspdf-autotable";
import ExcelJS from "exceljs";
import DataTable from "primevue/datatable";
import Button from "primevue/button";
import Tag from "primevue/tag";
import Column from "primevue/column";
import ColumnGroup from "primevue/columngroup"; // optional
import Row from "primevue/row";
import { FilterMatchMode, FilterOperator } from "primevue/api";
import Image from "primevue/image";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
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
        Image,
        Toast,
        ConfirmDialog
    },
    setup() {
        const page = usePage();
        const rows = ref([]);
        const statuses = ref(["approved", "decline", "pending"]);
        const filters = ref();
        const confirm = useConfirm();
        const toast = useToast();
        const reason = ref('');

        onMounted(() => {
            rows.value = page.props.dorms;
        });

        const getSeverity = (status) => {
            switch (status) {
                case "decline":
                    return "danger";

                case "approved":
                    return "success";

                case "pending":
                    return "warning";
            }
        };

        const initFilters = () => {
            filters.value = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                property_name: {
                    operator: FilterOperator.AND,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
                dorm_owner: {
                    operator: FilterOperator.AND,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
                detailed_address: {
                    operator: FilterOperator.AND,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                    ],
                },
                created_at: {
                    operator: FilterOperator.AND,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.DATE_IS },
                    ],
                },
                status: {
                    operator: FilterOperator.OR,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.EQUALS },
                    ],
                },
            };
        };

        initFilters();

        const formatDate = (value) => {
            // Check if value is a string and convert it to a Date object
            const date = typeof value === "string" ? new Date(value) : value;

            // Check if date is a valid Date object
            if (isNaN(date.getTime())) {
                // If not a valid Date, you can handle it according to your requirements
                return "Invalid Date"; // or return value.toString() or any other representation
            }

            // If it's a valid Date object, format it
            return date.toLocaleDateString("en-US", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
            });
        };

        const clearFilter = () => {
            initFilters();
        };


        const decline = ( id, reason) => {
            confirm.require({
                message: 'Are you sure you want to decline this dorm?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    axios
                        .post(route("dorm.changestatus.decline",  { reason: reason }), { id: id })
                        .then((response) => {
                            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Successfully decline the dorm', life: 3000 });
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
                message: 'Are you sure you want to approve this dorm?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    axios
                        .post(route('dorm.changestatus.approve'), { id: id })
                        .then((response) => {
                            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Successfully approve the dorm', life: 3000 });
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

        const business_permit = ref(null);
        const dorm = ref(null);
        const statuss = ref(null);
        const reasons = ref(null)
        const openTermsModal = (arg) => {
            business_permit.value = arg.business_permit_image;
            dorm.value = arg;
            var modal = document.getElementById("defaultModal");
            statuss.value = arg.status
            reasons.value = arg.reason
            modal.style.display = "block";
        };

        const closeTermsModal = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "none";
        };


        const openImageModal = (src) => {
            var modalImg = document.getElementById('modal-img');
            var modal = document.getElementById("modal");
            modalImg.src = src;

            modal.style.display = "block";
        };

        const closeImageModal = () => {
            var modal = document.getElementById("modal");

            modal.style.display = "none";
        };
        const closeReasonModal = () => {
            var modal = document.getElementById("reasonModal");

            modal.style.display = "none";
        }

        const openReasonModal = (arg) => {
            dorm.value = arg
            var modal = document.getElementById("reasonModal");

            modal.style.display = "block";
        }

        console.log(rows.dorm_owner);
        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };
        return {
            filters,
            clearFilter,
            getSeverity,
            formatDate,
            moneyFormat,
            statuses,
            statuss,
            rows,
            business_permit,
            openImageModal,
            closeImageModal,
            dorm,
            reasons,
            reason,
            openTermsModal,
            closeTermsModal,
            openReasonModal,
            closeReasonModal,
            decline,
            approved
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 lg:ml-64">
            <div class="flex items-center justify-start mt-16">
                <span
                    class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3"
                >
                    <svg
                        class="w-10 h-10 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        viewBox="0 0 384 512"
                        fill="currentColor"
                    >
                        <path
                            d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zM272 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"
                        />
                    </svg>
                </span>
                <h3 class="text-3xl">Dorm Verification</h3>
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
                                        x.status != "approved" &&
                                        x.status != "pending"
                                    );
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Declined Dorm</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                rows.filter((x) => {
                                    return x.status == "approved";
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Approved Dorm</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                rows.filter((x) => {
                                    return x.status == "pending";
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Pending Dorm</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <DataTable
                    v-model:filters="filters"
                    :value="rows"
                    tableStyle="min-width: 50rem"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    class="border"
                    paginator
                    :rows="10"
                    :globalFilterFields="[
                        'dorm_owner',
                        'dorm_name',
                        'detailed_address',
                        'created_at',
                        'status',
                    ]"
                >
                    <template #header>
                        <div class="flex items-center justify-between">
                            <Button
                                type="button"
                                class="rounded-lg border-green-400 border px-3 py-2.5"
                                icon="fa-solid fa-filter-circle-xmark"
                                label="Clear"
                                outlined
                                @click="clearFilter()"
                            />
                            <span class="p-input-icon-left">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input
                                    v-model="filters['global'].value"
                                    placeholder="Keyword Search"
                                    class="pl-10 rounded-lg"
                                />
                            </span>
                        </div>
                    </template>
                    <template #empty> No dormitory found. </template>
                    <Column
                        field="dorm_name"
                        header="Dorm Name"
                        sortable
                        style="min-width: 14rem"
                        class="border-b"
                    >
                        <template #body="{ data }">
                            {{ data.property_name }}
                        </template>
                    </Column>
                    <Column
                        field="dorm_owner"
                        header="Dorm Owner"
                        sortable
                        style="min-width: 14rem"
                        class="border-b"
                    >
                        <template #body="{ data }">
                            <p>
                                {{ data.user.first_name }}
                                {{ data.user.last_name }}
                            </p>
                        </template>
                    </Column>
                    <Column
                        field="detailed_address"
                        header="Address"
                        sortable
                        style="min-width: 14rem"
                        class="border-b"
                    >
                        <template #body="{ data }">
                            {{ data.detailed_address }}
                        </template>
                    </Column>
                    <Column
                        field="created_at"
                        header="Register Date"
                        sortable
                        dataType="date"
                        style="min-width: 10rem"
                        class="border-b"
                    >
                        <template #body="{ data }">
                            {{ formatDate(data.created_at) }}
                        </template>
                    </Column>
                    <Column
                        header="Status"
                        field="status"
                        sortable
                        style="min-width: 12rem"
                        class="border-b"
                    >
                        <template #body="{ data }">
                            <Tag
                                :value="data.status"
                                :severity="getSeverity(data.status)"
                            />
                        </template>
                    </Column>
                    <Column
                        header="Action"
                        style="min-width: 5rem"
                        class="border-b"
                    >
                        <template #body="{ data }">
                            <button
                                class="hover:text-orange-400"
                                :class="{
                                    'cursor-not-allowed':
                                        data.status == 'approved',
                                    'cursor-pointer':
                                        data.status == 'pending' &&
                                        data.status == 'decline',
                                }"
                                :disabled="data.status == 'approved'"
                                @click="openTermsModal(data)"
                            >
                                <!-- Use v-if to conditionally render the appropriate icon -->
                                <i
                                    v-if="
                                        data.status == 'pending' ||
                                        data.status == 'decline' ||
                                        data.status == null
                                    "
                                    class="fa-solid fa-eye"
                                ></i>
                                <i v-else class="fa-solid fa-eye-slash"></i>
                            </button>
                        </template>
                    </Column>
                </DataTable>
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
                                <h3 class="text-xl font-semibold text-black">
                                    
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
                                        You declined this Dorm. Reason: {{ reasons }}
                                    </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-6">
                                <div v-if="dorm">
                                    <div
                                        class="grid grid-rows md:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <div class="block">
                                                <div>
                                                    <p
                                                        class="font-bold text-lg text-gray-900"
                                                    >
                                                        Dorm Image
                                                    </p>
                                                    <img
                                                        :src="dorm.dorm_image"
                                                        alt="Image"
                                                        class="w-[400px] h-[200px] rounded-lg cursor-pointer hover:brightness-75"
                                                        @click="openImageModal(dorm.dorm_image)"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div>
                                                <p
                                                    class="font-bold text-lg text-gray-900"
                                                >
                                                    Business Permit
                                                </p>

                                                <div
                                                    class="w-full"
                                                    v-if="business_permit"
                                                >
                                                    <img
                                                        :src="
                                                            dorm.business_permit_image
                                                        "
                                                        alt="Image"
                                                        class="w-[400px] h-[200px] cursor-pointer hover:brightness-75 rounded"
                                                        @click="openImageModal(dorm.business_permit_image)"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-3" />
                                    <div class="block text-neutral-600">
                                        <div>
                                            <p
                                                class="font-bold text-lg text-gray-900"
                                            >
                                                Common Areas
                                            </p>
                                            <div class="grid grid-cols-3 gap-2">
                                                <div
                                                    class="w-full"
                                                    v-for="(
                                                        commonArea, index
                                                    ) in dorm.common_areas"
                                                    :key="index"
                                                >
                                                    <p
                                                        class="text-sm font-semibold"
                                                    >
                                                        {{ commonArea.name }}
                                                    </p>
                                                    <img
                                                        :src="commonArea.image"
                                                        alt="Image"
                                                        class="text-white w-[500px] h-[150px] cursor-pointer hover:brightness-75 rounded"
                                                        @click="openImageModal(commonArea.image)"
                                                    />
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <p
                                                class="font-bold text-lg text-gray-900"
                                            >
                                                Rooms
                                            </p>
                                            <div class="grid grid-cols-3 gap-2">
                                                <div
                                                    v-for="(
                                                        room, index
                                                    ) in dorm.rooms"
                                                    :key="index"
                                                >
                                                    <p
                                                        class="text-sm font-semibold"
                                                    >
                                                        {{ room.name }}
                                                    </p>
                                                    <img
                                                        :src="room.image"
                                                        alt="Image"
                                                        class="text-white w-[500px] h-[150px] cursor-pointer hover:brightness-75 rounded"
                                                        @click="openImageModal(room.image)"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-3" />
                                    <div class="text-neutral-600 grid grid-rows md:grid-cols-2 gap-3">
                                        <div>
                                        <p class="text-lg font-bold text-black">Dorm Details</p>
                                        <div className="font-light text-neutral-600">
                                            {{ moneyFormat(dorm.range_from) }} -
                                            {{ moneyFormat(dorm.range_to) }} a
                                            months
                                        </div>
                                        <span
                                            class="flex flex-row gap-1 items-center"
                                        >
                                            <i
                                                class="fa-solid fa-location-dot"
                                            ></i>
                                            <p>
                                                {{ dorm.detailed_address }}
                                            </p>
                                        </span>
                                        <span
                                            class="flex flex-row gap-1 items-center"
                                        >
                                            <i
                                                class="fa-solid fa-door-open"
                                            ></i>
                                            <p>
                                                Total no. of rooms
                                                {{ dorm.rooms_total }}
                                            </p>
                                        </span>
                                        <span
                                            class="flex flex-row gap-1 items-center"
                                        >
                                            <i class="fa-solid fa-building"></i>
                                            <p>
                                                Total no. of floors
                                                {{ dorm.floors_total }}
                                            </p>
                                        </span>
                                        <span
                                            class="flex flex-row gap-1 items-center"
                                        >
                                            <i class="fa-solid fa-landmark"></i>
                                            <p>
                                                Near
                                                {{ dorm.landmark }}
                                            </p>
                                        </span>
                                        </div>
                                        <hr class="block md:hidden"/>
                                        <div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div
                                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                v-if="dorm"
                            >
                                <button
                                        v-if="dorm.status == 'pending'"
                                        @click="openReasonModal(dorm), closeTermsModal()"
                                        type="button"
                                        class="text-white bg-red-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Decline
                                </button>
                                <button
                                    @click="approved(dorm.id)"
                                    v-if="dorm.status == 'pending'"
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
            <div id="modal"
                    class="z-50 fixed top-0 left-0 right-0 w-full h-screen bg-black/70 flex justify-center items-center hidden">

                    <!-- The close button -->
                    <a class="fixed z-90 top-6 right-8 text-white text-5xl font-bold" href="javascript:void(0)"
                        @click="closeImageModal()">&times;</a>

                    <!-- A big image will be displayed here -->
                    <Image id="modal-img" width="800" class="h-full flex items-center justify-center" />
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
                                    v-if="dorm"
                                >
                                    <Toast />
                                    <ConfirmDialog></ConfirmDialog>
                                    <button
                                        v-if="dorm.status == 'pending'"
                                        @click="closeReasonModal()"
                                        type="button"
                                        class="text-white bg-red-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="decline(dorm.id, reason )"
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
    </AuthenticatedLayout>
</template>
