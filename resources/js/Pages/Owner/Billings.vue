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
import Dropdown from "primevue/dropdown";
import DataTable from "primevue/datatable";
import Button from "primevue/button";
import Tag from "primevue/tag";
import Column from "primevue/column";
import ColumnGroup from "primevue/columngroup"; // optional
import Row from "primevue/row";
import { FilterMatchMode, FilterOperator } from "primevue/api";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import ConfirmDialog from "primevue/confirmdialog";
import Toast from "primevue/toast";
import Accordion from "primevue/accordion";
import AccordionTab from "primevue/accordiontab";

export default {
    components: {
        AuthenticatedLayout,
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        VueDatePicker,
        Dropdown,
        DataTable,
        Column,
        ColumnGroup,
        Row,
        Button,
        Tag,
        ConfirmDialog,
        Toast,
        Accordion,
        AccordionTab,
    },
    setup() {
        const selectedDay = ref(1); // Default to the 1st day of the month
        const days = Array.from({ length: 30 }, (_, index) => index + 1); // Create an array of days from 1 to 30
        const filters = ref();
        const filters2 = ref();
        const confirm = useConfirm();
        const toast = useToast();
        const page = usePage();
        const expandedRows = ref();

        const selectedDorm = ref(null);
        selectedDorm.value = page.props.dorms[0].id;

        const options = page.props.dorms.filter((dorm) => {
            return dorm && dorm.status === "approved";
        });
        console.log(selectedDorm.value);
        const headersHistory = [
            "Room Name",
            "Tenant Name",
            "Description",
            "Amount",
            "Invoice #",
            "Payment method",
            "Status",
        ];

        const dataHistory = ref([]);
        dataHistory.value = page.props.billingHistory
        .filter((x) => x.dorm_id === selectedDorm.value)
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        const headersBill = [
            "Room Name",
            "Tenant Name",
            "Monthly Rent",
            "Balance",
            "Moved-IN Date",
        ];

        const dataBill = ref([]);
        dataBill.value = page.props.billTenants.filter((x) => {
            return x.dorm_id == selectedDorm.value;
        });
        
        const outstandingCount = ref(0);
        const paidCount = ref(0);
        const unpaidCount = ref(0);

        outstandingCount.value = page.props.billingHistory.filter((x) => {
            return x.status == "Unpaid" && x.dorm_id == selectedDorm.value;
        }).length;

        paidCount.value = page.props.billingHistory.filter((x) => {
            return x.status == "Paid" && x.dorm_id == selectedDorm.value;
        }).length;

        unpaidCount.value = page.props.billingHistory.filter((x) => {
            return x.status == "Unpaid" && x.dorm_id == selectedDorm.value;
        }).length;

        const selectedBill = ref(null);
        const autoBillingForm = ref({
            tenant_id: null,
            auto_bill: false,
        });

        const openAutoBill = (arg) => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "block";

            selectedBill.value = arg;

            autoBillingForm.value.tenant_id = arg.tenant_id;
            autoBillingForm.value.auto_bill = arg.auto_bill;
        };

        const closeAutoBill = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "none";

            autoBillingForm.value = {
                tenant_id: null,
                auto_bill: false,
            };
        };

        const submitAutoBill = () => {
            axios
                .post(route("owner.auto-bill"), autoBillingForm.value)
                .then((response) => {})
                .catch((error) => {});
        };

        const manualBillingForm = ref({
            tenant_id: null,
            profile_id: null,
            subject: null,
            amount: null,
            description: null,
        });

        const openManualBill = (arg) => {
            var modal = document.getElementById("manualModal");

            modal.style.display = "block";

            selectedBill.value = arg;

            manualBillingForm.value.tenant_id = arg.tenant_id;
            manualBillingForm.value.profile_id = arg.profile_id;
        };

        const closeManualBill = () => {
            var modal = document.getElementById("manualModal");

            modal.style.display = "none";

            manualBillingForm.value = {
                tenant_id: null,
                profile_id: null,
                subject: null,
                amount: null,
                description: null,
            };
        };

        const submitManualBill = () => {
            axios
                .post(route("owner.manual-bill"), manualBillingForm.value)
                .then((response) => {
                    swal(
                        "Success!",
                        `You successfully create this manual bill.`,
                        "success"
                    );

                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                })
                .catch((error) => {});
        };

        const dates = [
            "All time",
            "Current month",
            "Previous month",
            "Last 12 months",
            "Last 30 days",
            "Previous Year",
        ];

        const objectRemoveKey = (object, key = null) => {
            const newObject = Object.assign({}, object);

            delete newObject.dorm_id;
            delete newObject.tenant_id;
            delete newObject.room_id;
            delete newObject.auto_bill;
            delete newObject.profile_id;

            return newObject;
        };

        const removeUnderscoreAndCapitalizeAfterSpace = (inputString) => {
            const stringWithSpaces = inputString.replace(/_/g, " ");

            // Split the string by spaces
            const words = stringWithSpaces.split(" ");

            // Capitalize the first letter of each word and join them
            const capitalizedString = words
                .map((word) => {
                    if (word.length > 0) {
                        return (
                            word[0].toUpperCase() + word.slice(1).toLowerCase()
                        );
                    }
                    return word; // Handle cases where there are multiple spaces
                })
                .join(" ");

            return capitalizedString;
        };

        const dorms = page.props.dorms;
        const imgs = ref("");
        console.log(dorms);
        const dormChange = (e) => {
            dataHistory.value = page.props.billingHistory.filter((x) => {
                return x.dorm_id == selectedDorm.value;
            });

            dataBill.value = page.props.billTenants.filter((x) => {
                return x.dorm_id == selectedDorm.value;
            });

            imgs.value = dorms.dorm_image.filter((x) => {
                return x.id == selectedDorm.value;
            });

            activeTab.value = "all";

            outstandingCount.value = page.props.billingHistory.filter((x) => {
                return x.status == "Unpaid" && x.dorm_id == selectedDorm.value;
            }).length;

            paidCount.value = page.props.billingHistory.filter((x) => {
                return x.status == "Paid" && x.dorm_id == selectedDorm.value;
            }).length;

            unpaidCount.value = page.props.billingHistory.filter((x) => {
                return x.status == "Unpaid" && x.dorm_id == selectedDorm.value;
            }).length;
        };

        console.log(dataHistory.value);

        const activeTab = ref("all");

        const changeActiveTab = (tab) => {
            activeTab.value = tab;

            if (tab == "paid") {
                dataHistory.value = page.props.billingHistory.filter((x) => {
                    return (
                        x.status == "Paid" && x.dorm_id == selectedDorm.value
                    );
                });

                return;
            }

            if (tab == "unpaid") {
                dataHistory.value = page.props.billingHistory.filter((x) => {
                    return (
                        x.status == "Unpaid" && x.dorm_id == selectedDorm.value
                    );
                });

                return;
            }

            dataHistory.value = page.props.billingHistory.filter((x) => {
                return x.dorm_id == selectedDorm.value;
            });

            return;
        };

        const getSeverity = (status) => {
            switch (status) {
                case "Unpaid":
                    return "danger";

                case "Paid":
                    return "success";
            }
        };

        const initFilters = () => {
            filters.value = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                tenant: {
                    operator: FilterOperator.AND,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.EQUALS },
                    ],
                },
                room: {
                    operator: FilterOperator.AND,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.EQUALS },
                    ],
                },
                monthly_fee: { value: null, matchMode: FilterMatchMode.IN },
                balance: { value: null, matchMode: FilterMatchMode.IN },
            };
        };

        initFilters();

        const clearFilter = () => {
            initFilters();
        };

        const initFilters2 = () => {
            filters2.value = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                tenant: {
                    operator: FilterOperator.AND,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.EQUALS },
                    ],
                },
                description: {
                    operator: FilterOperator.AND,
                    constraints: [
                        { value: null, matchMode: FilterMatchMode.EQUALS },
                    ],
                },
            };
        };

        initFilters2();

        const clearFilter2 = () => {
            initFilters2();
        };
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

        const optionDorm = dorms.map((p) => ({
            id: p.id,
            label: p.property_name,
            image: p.dorm_image,
        }));

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        console.log("Selected Dorm:", imgs.value);
        return {
            optionDorm,
            expandedRows,
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
            openManualBill,
            selectedDorm,
            objectRemoveKey,
            removeUnderscoreAndCapitalizeAfterSpace,
            dorms,
            dormChange,
            selectedBill,
            submitManualBill,
            manualBillingForm,
            autoBillingForm,
            activeTab,
            changeActiveTab,
            outstandingCount,
            paidCount,
            unpaidCount,
            submitAutoBill,
            filters,
            getSeverity,
            clearFilter,
            formatDate,
            moneyFormat,
            filters2,
            clearFilter2,
            imgs,
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
            <div class="mt-5">
                <p>Select Dorm</p>
                <select
                    v-model="selectedDorm"
                    @change="dormChange($event)"
                    class="w-30 rounded-xl border-0 shadow-[0px_2px_3px_-1px_rgba(0,0,0,0.1),0px_1px_0px_0px_rgba(25,28,33,0.02),0px_0px_0px_1px_rgba(25,28,33,0.08)]"
                >
                    <option
                        v-for="option in options"
                        :key="option.id"
                        :value="option.id"
                    >
                        {{ option.property_name }}
                    </option>
                </select>
            </div>

            <div class="grid grid-cols-1 2xl:grid-cols-3 mt-3 gap-4">
                <div class="card mb-10 2xl:col-span-2">
                    <DataTable
                        v-model:expandedRowGroups="expandedRows"
                        v-model:filters="filters2"
                        :value="dataHistory"
                        tableStyle="min-width: 50rem"
                        class="border rounded-xl"
                        expandableRowGroups
                        rowGroupMode="subheader"
                        groupRowsBy="tenant"
                    >
                    <template #header>
                            <h3 class="text-2xl font-semibold mb-3">
                                Billing History
                            </h3>
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
                                        v-model="filters2['global'].value"
                                        placeholder="Keyword Search"
                                        class="pl-10 rounded-lg"
                                    />
                                </span>
                            </div>
                        </template>
                        <template #empty> No bill found. </template>
                        <template #groupheader="slotProps">
                            {{ slotProps.data.tenant}}
                        </template>
                        <Column
                            field="room"
                            header="Room Name"
                            style="min-width: 9rem"
                            class="border-b"
                        ></Column>
                        <Column
                            field="tenant"
                            header="Tenant Name"
                            sortable
                            style="min-width: 9rem"
                            class="border-b"
                        ></Column>
                        <Column
                            field="description"
                            header="Description"
                            sortable
                            style="min-width: 14rem"
                            class="border-b"
                        ></Column>
                        <Column
                            field="payment_method"
                            header="Payment Method"
                            sortable
                            style="min-width: 8rem"
                            class="border-b"
                        >
                            <template #body="{ data }">
                                <p v-if="data.status == 'Paid'">
                                    <div class="">
                                                <img v-if="data.payment_method == 'PH_GCASH'" src="/images/gcashlogo.png" class="w-20"/>
                                                <img v-if="data.payment_method == 'PH_GRABPAY'" src="/images/grablogo.png" class="w-10"/>
                                                <img v-if="data.payment_method == 'VISA'" src="/images/visa.png" class="w-10"/>
                                                <img v-if="data.payment_method == 'PH_SHOPEEPAY'" src="/images/ShopeePay.png" class="w-16"/>
                                                <img v-if="data.payment_method == 'PH_PAYMAYA'" src="/images/paymaya.png" class="w-10"/>
                                            </div>
                                </p>
                                <p v-else>Not Paid</p>
                            </template>
                        </Column>
                        <Column
                            field="status"
                            header="Status"
                            sortable
                            style="min-width: 5rem"
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
                            field="amount"
                            header="Amount"
                            sortable
                            style="min-width: 14rem"
                            class="border-b"
                        >
                            <template #body="{ data }">
                                <p>{{ moneyFormat(data.amount) }}</p>
                            </template>
                        </Column>
                    </DataTable>
                </div>
                <div class="card mb-10">
                    <DataTable
                        v-model:filters="filters"
                        :value="dataBill"
                        tableStyle="min-width: 30rem"
                        :rowsPerPageOptions="[5, 10, 20, 50]"
                        class="border"
                        paginator
                        :rows="10"
                        :globalFilterFields="[
                            'tenant',
                            'room',
                            'monthly_fee',
                            'balance',
                        ]"
                    >
                        <template #header>
                            <h3 class="text-2xl font-semibold mb-3">
                                Bill Tenant(s)
                            </h3>
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
                        <template #empty> No tenant found. </template>
                        <Column
                            field="tenant"
                            header="Name"
                            sortable
                            style="min-width: 10rem"
                            class="border-b"
                        >
                            <template #body="{ data }">
                                {{ data.tenant }}
                            </template>
                        </Column>
                        <Column
                            field="balance"
                            header="Balance"
                            sortable
                            style="min-width: 10rem"
                            class="border-b"
                        >
                            <template #body="{ data }">
                                {{ moneyFormat(data.balance) }}
                            </template>
                        </Column>
                        <Column
                            header="Action"
                            style="min-width: 5rem"
                            class="border-b text-center"
                        >
                            <template #body="{ data }">
                                <a
                                    :href="
                                        route(
                                            'owner.tenantshistory',
                                            data.profile_id
                                        )
                                    "
                                    class="hover:text-orange-400 text-center"
                                >
                                    <i class="pi pi-pencil"></i>
                                </a>
                            </template>
                        </Column>
                    </DataTable>
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
                        <div
                            class="relative bg-white rounded-lg shadow"
                            v-if="selectedBill"
                        >
                            <!-- Modal header -->
                            <div
                                class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                            >
                                <h3 class="text-xl font-semibold text-black">
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
                                            v-model="selectedBill.tenant"
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
                                            v-model="selectedBill.room"
                                        />
                                    </div>
                                </div>

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
                                        v-model="selectedBill.monthly_fee"
                                    />
                                </div>
                                <!--Select Date-->
                                <!-- <div>
                                        <label for="daySelect" class="mr-2">Select a day:</label>
                                        <select id="daySelect" v-model="selectedDay" class="border-none rounded-md py-1 bg-gray-200 mb-1 text-sm text-gray-900">
                                        <option v-for="day in days" :key="day" :value="day">{{ day }}</option>
                                        </select>
                                        <p>You selected the {{ selectedDay }} day of the month.</p>
                                    </div> -->

                                <label
                                    class="relative inline-flex items-center cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="autoBillingForm.auto_bill"
                                        class="sr-only peer"
                                    />
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"
                                    ></div>
                                    <span
                                        class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"
                                        >Auto Bill</span
                                    >
                                </label>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                            >
                                <button
                                    @click="submitAutoBill()"
                                    type="button"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                >
                                    Okay
                                </button>
                                <button
                                    @click="closeAutoBill()"
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
                        <div
                            class="relative bg-white rounded-lg shadow"
                            v-if="selectedBill"
                        >
                            <!-- Modal header -->
                            <div
                                class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                            >
                                <h3 class="text-xl font-semibold text-black">
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
                                            v-model="selectedBill.tenant"
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
                                            v-model="selectedBill.room"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label
                                        for="dorm_owner_name"
                                        class="block text-sm font-medium text-gray-900"
                                        >Billing Subject:</label
                                    >
                                    <select
                                        id="subject"
                                        v-model="manualBillingForm.subject"
                                        class="block w-full px-2 py-2 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    >
                                        <option selected :value="null">
                                            Choose a Billing for:
                                        </option>
                                        <option value="Electricity">
                                            Electricity
                                        </option>
                                        <option value="Water">Water</option>
                                        <option value="Internet">
                                            Internet
                                        </option>
                                        <option value="Others">Others</option>
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
                                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                        required
                                        v-model="manualBillingForm.amount"
                                    />
                                </div>
                                <div>
                                    <label
                                        for="complainmessage"
                                        class="block text-sm font-medium text-gray-900"
                                        >Description:</label
                                    >
                                    <textarea
                                        v-model="manualBillingForm.description"
                                        id="complainmessage"
                                        rows="3"
                                        class="block w-full p-3 rounded-md text-sm text-gray-800 bg-gray-100 border-1 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400"
                                        placeholder="Write a description..."
                                        required
                                    >
                                    </textarea>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                            >
                                <button
                                    @click="submitManualBill()"
                                    type="button"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                >
                                    Bill
                                </button>
                                <button
                                    @click="closeManualBill()"
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
