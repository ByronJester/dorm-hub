<script>
    import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
    // import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import 'vue-good-table-next/dist/vue-good-table-next.css'
    import { VueGoodTable } from 'vue-good-table-next';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';

    export default {
        components: {
            AuthenticatedLayout,
            VueGoodTable
        },
        setup() {
            const isMobileView = ref(false)
            isMobileView.value = screen.width < 600;
            const page = usePage()
            const rows = ref([])
            const columns = ref([
                {
                    label: 'Name',
                    field: 'name',
                },
                {
                    label: 'Source of Income',
                    field: 'source_of_income',
                },
                {
                    label: 'Monthly Income',
                    field: 'monthly_income',
                },
                {
                    label: 'Monthly Expenses',
                    field: 'monthly_expenses',
                },
                {
                    label: 'Status',
                    field: 'status',
                },
                {
                    label: 'Actions',
                    field: 'action',
                },
            ])

            onMounted(() => {
                rows.value = page.props.applications
            });

            const user = page.props.auth.user;

            const paymentColumns = ref([
                {
                    label: 'Amount To Pay',
                    field: 'amount_to_pay',
                },
                {
                    label: 'Amount Paid',
                    field: 'amount_paid',
                },
                {
                    label: 'Partial Payment',
                    field: 'partial',
                },
                {
                    label: 'Balance',
                    field: 'balance',
                },
                {
                    label: 'Mode of Payment',
                    field: 'mode_of_payment',
                },
                {
                    label: 'Status',
                    field: 'status',
                },
                {
                    label: 'Date',
                    field: 'display_date',
                },
                {
                    label: 'Partial Payment Receipt',
                    field: 'partial_receipt',
                },
                {
                    label: 'Receipt',
                    field: 'receipt',
                },
                {
                    label: 'Actions',
                    field: 'action',
                },
            ])

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

            return {
                isMobileView,
                columns,
                rows,
                user,
                application,
                payments,
                paymentColumns,
                viewPayment,
                openModal,
                closeModal,
                changeStatus,
                moneyFormat,
                markAsPaid
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="main">
            <div class="w-full pt-2 md:pt-20">
                <vue-good-table
                    styleClass="vgt-table condensed"
                    style="padding: 0vw 5vw; width: 100%"
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
            </div>

            `<div class="w-full">
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
            </div>`

        </div>
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 100%;
        min-height: 92vh;
        background-color: #E5E8E8;
    }

    .tenantModal {
        display: none;
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 20px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .tenant-modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 100%;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    ::-webkit-scrollbar {
        width: 0px;
        background: transparent; /* make scrollbar transparent */
    }

</style>
