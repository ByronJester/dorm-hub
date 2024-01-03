<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import { usePage, useForm, router } from "@inertiajs/vue3";
import { ref, reactive, watch, onMounted, computed } from "vue";
import { MapboxMap, MapboxMarker } from "@studiometa/vue-mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import axios from "axios";
import jsPDF from "jspdf";
import "jspdf-autotable";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import TenantVerif from '@/Pages/Tenant/Component/TenantVerif.vue';
import DropDown from 'primevue/dropdown';
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



export default {
    components: {
        AuthenticatedLayout,
        TenantVerif,
        DropDown,
        DataTable,
        Column,
        ColumnGroup,
        Row,
        Button,
        Tag,
    },
    methods: {

        exportToPDF() {
            const page = usePage();
            const doc = new jsPDF();
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
            doc.text("Payments Record", 15, 60)
            const margin = 65;

            const tableData = [this.headers].concat(
                this.slicedRows.map((row) => [
                    row.date,
                    row.payment_method,
                    row.amount,
                    row.category,
                    row.status,
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

    },
    setup() {
        const page = usePage();
        const user = computed(() => page.props.auth.user);
        const payments = ref(null);
        const profile = page.props.profile;
        const application = ref({});
        const owner = ref({});
        const methods = ref([]);
        const isMobileView = ref(false);
        isMobileView.value = screen.width < 600;
        const receipt = ref(null);
        const payment_id = ref();
        const imageError = ref(null);
        const lastBilled = page.props.lastBilled
        const options = ["E-Wallet", "Cash", "Bank Transfer"];

        const toast = useToast();

        onMounted(() => {
            application.value = page.props.application;
            owner.value = page.props.owner;
            methods.value = page.props.methods;
        });

        const selectedBill = ref();

        const pay = (arg) => {
            openModal()

            selectedBill.value = arg
        };

        const openModal = () => {

            var modal = document.getElementById("payModal");

            modal.style.display = "block";
        };

        const closeModal = () => {
            var modal = document.getElementById("payModal");

            modal.style.display = "none";
        };

        const selectedPayment = ref(null)
        const openLeaveModal = (arg) => {
            var modal = document.getElementById("refundModal");

            modal.style.display = "block";

            selectedPayment.value = arg
        };

        const closeLeaveModal = () => {
            var modal = document.getElementById("refundModal");

            modal.style.display = "none";
        };

        const submitRefund = () => {
            swal(
                {
                    title: `Are you sure to refund this payment?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    const data = {
                        user_payment_id: selectedPayment.value.id,
                        amount: selectedPayment.value.billing.amount,
                        payment_method: selectedPaymentMethod.value,
                        wallet_name: wallet_name.value,
                        account_name: account_name.value,
                        account_number: account_number.value
                    }

                    axios
                        .post(route("request.refund"), data)
                        .then((response) => {
                            swal(
                                "Request refund submitted.",
                                "Please wait for dorm owner approval.",
                                "success"
                            );

                            setTimeout(function () {
                                location.reload();
                            }, 3000);

                        })
                        .catch((error) => {
                            // errors.value = error.response.data.errors;
                        });
                }
            );
        }

        const selectedPaymentMethod = ref()
        const wallet_name = ref(null)
        const account_name = ref(null)
        const account_number = ref(null)

        const showBankTransfer = ref(false);
        const showEwallet = ref(false);
        const toggleTransfer = () => {
            showBankTransfer.value = selectedPaymentMethod.value === 'Bank Transfer';
            showEwallet.value = selectedPaymentMethod.value === 'E-Wallet';
        };

        const imageClick = () => {
            document.getElementById("bank_receipt").click();
        };

        const imageChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                receipt.value = e.target.result;
            };
        };

        const headers = ["Payment Date", "Payment Method", "Amount", "Description", "Status"];
        //

        const searchQuery = ref("");
        const itemsPerPage = 5; // Set the maximum number of items per page to 10
        const currentPage = ref(1); // Initialize to the first page


        const filteredData = computed(() => {
            const query = searchQuery.value.toLowerCase().trim();

            if (!query) {
                if (activeTable.value === 'all') {
                    return data; // Return all data if 'all' is selected
                } else {
                    return data.filter((row) => {
                        // Modify the condition based on your logic for 'paid' and 'unpaid'
                        if (activeTable.value === 'paid') {
                            return row.status.toLowerCase() === 'paid';
                        } else if (activeTable.value === 'unpaid') {
                            return row.status.toLowerCase() === 'unpaid';
                        }
                    });
                }
            }

            return data.filter((row) => {
                // Your existing query conditions
                return (
                    row.date.toLowerCase().includes(query) ||
                    row.payment_method.toLowerCase().includes(query) ||
                    row.category.toLowerCase().includes(query) ||
                    row.status.toLowerCase().includes(query)
                );
            });
        });

        const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage));

        const slicedRows = computed(() => {
            const startIndex = (currentPage.value - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            return filteredData.value.slice(startIndex, endIndex);
        });

        const changePage = (pageChange) => {
            const newPage = currentPage.value + pageChange;
            if (newPage >= 1 && newPage <= totalPages.value) {
                currentPage.value = newPage;
            }
        };
        //

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        const payment_method = ref("Online Payment")
        const proof_of_payment = ref(null);

        const proofOfPyamentChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                proof_of_payment.value = e.target.result;
            };
        }

        const proceedPayment = () => {
            const data = {
                id: selectedBill.value.id,
                billing_id: selectedBill.value.billing_id,
                amount: selectedBill.value.billing.amount,
                payment_method: payment_method.value,
                proof_of_payment: proof_of_payment.value
            }

            swal(
                {
                    title: `Are you sure to proceed payment?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    axios
                        .post(route("pay.specific.billing"), data)
                        .then((response) => {
                            if (payment_method.value == 'Online Payment') {
                                window.location.href = response.data.data.redirect.checkout_url
                            } else {
                                location.reload();
                            }

                        })
                        .catch((error) => {
                            // errors.value = error.response.data.errors;
                        });
                }
            );
        }



        const activeTable = ref('all')

        const setActiveTable = (table) => {
            activeTable.value = table;
            currentPage.value = 1;
        }


        const myDorm = ref()
        myDorm.value = page.props.myDorm

        const selectedProfile = ref(profile.length > 0 ? profile[0].id : null);
        const optionProfile = profile.map((p) => ({
            id: p.id,
            label: p.first_name,
        }));

        const nexPayment = ref(null)
        const balance = ref(0)
        const totalAmountPaid = ref(0)

        console.log(page.props)

        const rows = ref([])
        const filters = ref();

        const statuses = ref([0, 1]);
        const getSeverity = (status) => {
            switch (status) {
                case '0':
                    return 'danger';

                case 'paid':
                    return '1';

            }
        };

        const data = ref([])

        const removeUnderscoreAndCapitalizeAfterSpace = (inputString) => {
            if (inputString === undefined || typeof inputString === undefined) {
                return
            }

            const stringWithSpaces = inputString.replace(/_/g, ' ');

            // Split the string by spaces
            const words = stringWithSpaces.split(' ');

            // Capitalize the first letter of each word and join them
            const capitalizedString = words.map(word => {
                if (word.length > 0) {
                    return word[0].toUpperCase() + word.slice(1).toLowerCase();
                }
                return word; // Handle cases where there are multiple spaces
            }).join(' ');

            return capitalizedString;
        }
        onMounted(() => {
            rows.value = page.props.bills.filter(x => { return x.profile_id == selectedProfile.value });
            data.value = data.value.filter(x => { return x.profile_id == selectedProfile.value })

            balance.value = page.props.balances.filter(x => { return x.profile_id == selectedProfile.value }).reduce((accumulator, currentValue) => {
                return parseFloat(accumulator) + parseFloat(currentValue.amount);
            }, 0);

            totalAmountPaid.value = page.props.amountPaids.filter(x => { return x.profile_id == selectedProfile.value }).reduce((accumulator, currentValue) => {
                return parseFloat(accumulator) + parseFloat(currentValue.amount);
            }, 0);

            let nxtp = page.props.nextPayments.filter(x => { return x.profile_id == selectedProfile.value })

            nexPayment.value = nxtp.length > 0 ? nxtp[0] : 0

            payments.value = page.props.payments

            // for (let p = 0; p < payments.value.length; p++) {
            //     data.value.push(
            //         {
            //             payment_method: payments.value[p].payment_method,
            //             amount: parseFloat(payments.value[p].amount),
            //             category: removeUnderscoreAndCapitalizeAfterSpace(payments.value[p].description),
            //             profile_id: payments.value[p].profile_id
            //         }
            //     )
            // }

        });
        const initFilters = () => {
            filters.value = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                description: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                amount: { value: null, matchMode: FilterMatchMode.IN },

            };
        };

        initFilters();

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

        const clearFilter = () => {
            initFilters();
        };

        const changeSelectedProfile = (evt) => {
            if (document.getElementById('paymentProfile').innerHTML !== evt.value.label) {
                document.getElementById('paymentProfile').innerHTML = evt.value.label;
                document.getElementById('transactionDtls').style = '';
                document.getElementById('payDtls').style = '';
                rows.value = page.props.bills.filter(x => { return x.profile_id == evt.value.id });
                balance.value = page.props.balances.filter(x => { return x.profile_id == evt.value.id }).reduce((accumulator, currentValue) => {
                    return parseFloat(accumulator) + parseFloat(currentValue.amount);
                }, 0);

                totalAmountPaid.value = page.props.amountPaids.filter(x => { return x.profile_id == evt.value.id }).reduce((accumulator, currentValue) => {
                    return parseFloat(accumulator) + parseFloat(currentValue.amount);
                }, 0);

                let nxtp = page.props.nextPayments.filter(x => { return x.profile_id == evt.value.id })

                nexPayment.value = nxtp.length > 0 ? nxtp[0] : 0

                console.log(rows.value)

                for (let p = 0; p < payments.value.length; p++) {
                    data.value.push(
                        {
                            payment_method: payments.value[p].payment_method,
                            amount: parseFloat(payments.value[p].amount),
                            category: removeUnderscoreAndCapitalizeAfterSpace(payments.value[p].description),
                            profile_id: payments.value[p].profile_id,
                            payment_date: payments.value[p].payment_date,
                            invoiceNo: payments.value[p].invoice_number
                        }
                    )
                }

                data.value = data.value.filter(x => { return x.profile_id == evt.value.id })
            }

        }

        const payNow = (arg) => {
            axios
                .post(route("tenant.pay-billing"), arg)
                .then((response) => {
                    if (!!response.data) {
                        window.location.href = response.data
                    }

                })
                .catch((error) => {
                    toast.add({ severity: 'error', summary: 'Warning', detail: error, life: 3000 });
                    // errors.value = error.response.data.errors;
                });
        }

        // console.log(selectedProfile)

        return {
            filters,
            clearFilter,
            rows,
            selectedProfile,
            optionProfile,
            setActiveTable,
            activeTable,
            myDorm,
            user,
            payments,
            application,
            owner,
            methods,
            isMobileView,
            headers,
            data,
            receipt,
            payment_id,
            imageError,
            pay,
            openModal,
            closeModal,
            imageClick,
            imageChange,
            removeUnderscoreAndCapitalizeAfterSpace,
            nexPayment,
            lastBilled,
            moneyFormat,
            balance,
            totalAmountPaid,
            selectedBill,
            payment_method,
            proof_of_payment,
            proofOfPyamentChange,
            proceedPayment,
            searchQuery,
            itemsPerPage,
            currentPage,
            filteredData,
            totalPages,
            slicedRows,
            changePage,
            openLeaveModal,
            closeLeaveModal,
            showBankTransfer,
            showEwallet,
            options,
            toggleTransfer,
            selectedPaymentMethod,
            selectedPayment,
            wallet_name,
            account_name,
            account_number,
            submitRefund,
            changeSelectedProfile,
            payNow,
            formatDate
        };
    },
};
</script>

<template>
    <TenantVerif :user="user" />
    <AuthenticatedLayout v-if="user.status == 'approved'">
        <div class="p-4  lg:ml-64">
            <div class="min-w-screen
                        2xl:mx-40
                        mt-16">
                <section class="pt-6 mb-6 flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <span class="inline-flex justify-center items-center w-6 h-6 mr-2"><svg viewBox="0 0 24 24"
                                width="20" height="20" class="inline-block">
                                <path fill="currentColor"
                                    d="M7 12C9.2 12 11 10.2 11 8S9.2 4 7 4 3 5.8 3 8 4.8 12 7 12M11 20V14.7C9.9 14.3 8.5 14 7 14C3.1 14 0 15.8 0 18V20H11M22 4H15C13.9 4 13 4.9 13 6V18C13 19.1 13.9 20 15 20H22C23.1 20 24 19.1 24 18V6C24 4.9 23.1 4 22 4M18 18H16V6H18V18Z">
                                </path>
                            </svg></span>
                        <h1 class="text-3xl leading-tight">Payments</h1>
                    </div>
                </section>
                <hr />
                <div class="mt-5">
                    <div>
                        <div class="block">
                            <p>Select profile</p>
                            <div class="card flex justify-content-center">
                                <DropDown v-model="selectedProfile" @change="changeSelectedProfile($event)"
                                    :options="optionProfile" optionLabel="label"
                                    class="w-full md:w-14rem shadow-[0px_2px_3px_-1px_rgba(0,0,0,0.1),0px_1px_0px_0px_rgba(25,28,33,0.02),0px_0px_0px_1px_rgba(25,28,33,0.08)]" />
                            </div>

                        </div>
                    </div>
                    <div style="display: none;" id="payDtls" class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5 text-lg">
                        <div
                            class="bg-white rounded-lg p-4 gap-6 shadow-[0px_2px_3px_-1px_rgba(0,0,0,0.1),0px_1px_0px_0px_rgba(25,28,33,0.02),0px_0px_0px_1px_rgba(25,28,33,0.08)] justify-center items-center flex">
                            <img src='https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'
                                alt="Profile picture"
                                class="rounded-full block md:h-40 w-40 bg-no-repeat bg-cover object-fit max-w-full bg-gray-100 dark:bg-slate-800" />
                            <div>
                                <p>Hello!</p>
                                <span id="paymentProfile"></span>
                            </div>
                        </div>
                        <div
                            class="rounded-lg bg-black text-gray-200 grid grid-cols-2 p-4 shadow-[0px_2px_3px_-1px_rgba(0,0,0,0.1),0px_1px_0px_0px_rgba(25,28,33,0.02),0px_0px_0px_1px_rgba(25,28,33,0.08)]">
                            <div class="flex flex-col gap-10">
                                <div>
                                    <p class="text-xl font-bold">Next Payment on</p>
                                    {{ !!nexPayment ? nexPayment.display_date : '' }}
                                </div>
                                <div>
                                    <p class="text-xl font-bold">Last Billed on</p>
                                    {{ !!lastBilled ? lastBilled.display_created_date : '' }}
                                </div>

                            </div>
                            <div class="">
                                <p class="text-xl font-bold">Amount Due</p>
                                {{ !!nexPayment ? moneyFormat(nexPayment.amount) : moneyFormat(0) }}
                            </div>
                        </div>
                        <div class="w-full flex flex-col gap-3 text-gray-900">
                            <!-- <div class="flex flex-row items-center justify-between rounded-lg bg-orange-300 p-4 shadow-[0px_2px_3px_-1px_rgba(0,0,0,0.1),0px_1px_0px_0px_rgba(25,28,33,0.02),0px_0px_0px_1px_rgba(25,28,33,0.08)]">
                                <p class="text-xl font-bold">Upcoming Payment</p>
                                {{ !!nexPayment ?  moneyFormat(nexPayment.amount) : moneyFormat(0.00) }}
                            </div> -->
                            <div
                                class="flex flex-row items-center justify-between rounded-lg bg-red-300 p-4 shadow-[0px_2px_3px_-1px_rgba(0,0,0,0.1),0px_1px_0px_0px_rgba(25,28,33,0.02),0px_0px_0px_1px_rgba(25,28,33,0.08)]">
                                <p class="text-xl font-bold">Balance</p>
                                {{ moneyFormat(balance) }}
                            </div>
                            <div
                                class="flex flex-row items-center justify-between rounded-lg bg-green-300 p-4 shadow-[0px_2px_3px_-1px_rgba(0,0,0,0.1),0px_1px_0px_0px_rgba(25,28,33,0.02),0px_0px_0px_1px_rgba(25,28,33,0.08)]">
                                <p class="text-xl font-bold">Total Amount Paid</p>
                                <p>{{ moneyFormat(totalAmountPaid) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display: none;" id="transactionDtls" class="grid grid-cols-1 md:grid-cols-3 mt-5 gap-2 mb-20">
                    <div class="md:col-span-2">
                        <div class="card">
                            <DataTable v-model:filters="filters" :value="rows" tableStyle="min-width: 50rem"
                                :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="10"
                                :globalFilterFields="['description', 'amount', 'for_the_month', 'invoice_number']">
                                <template #header>
                                    <div class="flex items-center justify-between">
                                        <Button type="button" class="rounded-lg border-green-400 border px-3 py-2.5"
                                            icon="fa-solid fa-filter-circle-xmark" label="Clear" outlined
                                            @click="clearFilter()" />
                                        <span class="p-input-icon-left">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <input v-model="filters['global'].value" placeholder="Keyword Search"
                                                class="pl-10 rounded-lg" />
                                        </span>
                                    </div>
                                </template>
                                <template #empty> No bills found. </template>
                                <Column field="description" header="Bills" sortable style="min-width: 14rem"
                                    class="border-b">
                                    <template #body="{ data }">
                                        <div class="grid grid-cols-6 place-items-center justify-between">
                                            <p>{{ data.description }}</p>
                                            <div class="w-16 text-center ">
                                                <p class="bg-green-400  text-white rounded-full text-sm font-bold"
                                                    v-if="data.is_paid">Paid</p>
                                                <p class="bg-red-400  text-white rounded-full text-sm font-bold"
                                                    v-if="!data.is_paid">Unpaid</p>
                                            </div>
                                            <p class="text-center">{{ moneyFormat(data.amount) }}</p>
                                            <p class="text-center">{{ data.for_the_month == null ? '' :
                                                formatDate(data.for_the_month) }}</p>
                                            <div class="text-end">
                                                <button v-if="data.is_paid"
                                                    class="text-gray-400 disabled:cursor-not-allowed text-sm font-bold"
                                                    disabled>Pay Now</button>
                                                <button v-if="!data.is_paid"
                                                    class="text-gray-900 hover:text-orange-400 hover:underline text-sm font-bold"
                                                    @click="payNow(data)">
                                                    Pay Now
                                                </button>
                                            </div>

                                            <div class="text-end">
                                                <a :href="'/tenant/payment-success/' + data.invoice_number" target="_blank" class="text-gray-900 hover:text-orange-400 hover:underline text-sm font-bold"
                                                    v-if="data.is_paid">Download Receipt</a>
                                            </div>

                                        </div>
                                    </template>
                                </Column>


                            </DataTable>
                        </div>
                    </div>
                    <div>
                        <div class="card">
                            <DataTable v-model:filters="filters" :value="data" tableStyle="min-width: 25rem"
                                :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="5"
                                :globalFilterFields="['description', 'amount', 'payment_date']">
                                <template #empty> No transactions found. </template>
                                <Column field="description" header="Recent Transactions" sortable style="min-width: 14rem"
                                    class="border-b">
                                    <template #body="{ data }">
                                        <div class="grid grid-cols-4 w-full place-items-center gap-2">
                                            <p>{{ data.category }}</p>
                                            <div class="">
                                                <img v-if="data.payment_method == 'PH_GCASH'" src="/images/gcashlogo.png"
                                                    class="w-20" />
                                                <img v-if="data.payment_method == 'PH_GRABPAY'" src="/images/grablogo.png"
                                                    class="w-10" />
                                                <img v-if="data.payment_method == 'VISA'" src="/images/visa.png"
                                                    class="w-10" />
                                                <img v-if="data.payment_method == 'PH_SHOPEEPAY'"
                                                    src="/images/ShopeePay.png" class="w-16" />
                                                <img v-if="data.payment_method == 'PH_PAYMAYA'" src="/images/paymaya.png"
                                                    class="w-10" />
                                            </div>
                                            <p>{{ data.payment_date == null ? '' :
                                                formatDate(data.payment_date) }}</p>
                                            <p class="text-end">{{ moneyFormat(data.amount) }}</p>

                                        </div>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
                <!--Payments-->
                <!-- <div class="w-full mt-2 mb-5">
                        <div class="w-full mb-12 mt-5">
                            <div
                                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                                >
                                <div class="rounded-t mb-0 px-4 py-3 border-0">
                            <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                                >
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
                                                <button class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    Print
                                                </button>
                                            </div>
                                </div>
                                </div>
                            </div>
                        </div>

                                        <div class="flex flex-row mt-5 border-t">
                                            <button
                                                @click="setActiveTable('all')"
                                                :class="{ 'bg-orange-400 text-white': activeTable === 'all' }"
                                                class="py-2.5 px-5 font-semibold text-md border-x">
                                                All
                                            </button>
                                            <button
                                                @click="setActiveTable('paid')"
                                                :class="{ 'bg-orange-400 text-white': activeTable === 'paid' }"
                                                class="py-2.5 px-5 font-semibold text-md border-x">
                                                Paid
                                            </button>
                                            <button
                                                @click="setActiveTable('unpaid')"
                                                :class="{ 'bg-orange-400 text-white': activeTable === 'unpaid' }"
                                                class="py-2.5 px-5 font-semibold text-md border-x">
                                                Unpaid
                                            </button>
                                        </div>

                                    <div class="block w-full overflow-x-auto">
                                        <table
                                            class="items-center w-full bg-transparent border-collapse"
                                        >
                                            <thead>
                                                <tr>
                                                    <th
                                                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                                        v-for="header in headers"
                                                        :key="header"
                                                    >
                                                        {{ header }}
                                                    </th>
                                                    <th
                                                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                                    >
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        item, rowIndex
                                                    ) in slicedRows"
                                                    :key="rowIndex"
                                                >
                                                    <td
                                                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                                        v-for="(
                                                            value, colIndex
                                                        ) in item"
                                                        :key="colIndex"
                                                    >
                                                        <p v-if="colIndex != 'receipt' && colIndex != 'action'">
                                                            {{value}}
                                                        </p>

                                                        <div v-if="colIndex == 'receipt'">
                                                            <img v-if="value.proof_of_payment != null"
                                                                :src="value.proof_of_payment"
                                                                class="w-[100px] h-[100px]"
                                                            />
                                                        </div>

                                                        <button @click="pay(value)" class="bg-orange-400 text-white w-14 px-2 rounded-md font-semibold py-0.5 mr-2"
                                                            v-if="colIndex == 'action' && value.status != 'refunded'" :disabled="value.status == 'paid' || value.status == 'waiting_for_approval' || value.status == 'pending_refund' || value.status == 'declined_refund' || value.status == 'ongoing_refund'"
                                                            :class="{'cursor-not-allowed bg-opacity-20': value.status == 'paid' || value.status == 'waiting_for_approval' || value.status == 'pending_refund' || value.status == 'declined_refund' || value.status == 'ongoing_refund'}"
                                                        >
                                                            {{ value.status == 'paid' || value.status == 'pending_refund' || value.status == 'declined_refund' || value.status == 'ongoing_refund' ? 'Paid' : 'Pay' }}
                                                        </button>

                                                        <button @click="openLeaveModal(value)" class="bg-cyan-400 text-white w-14 px-2 rounded-md font-semibold py-0.5"
                                                            v-if="(colIndex == 'action' && value.status == 'paid' && value.status != 'refunded') && (value.description == 'monthly_fee' || value.billing.subject != null)"
                                                        >
                                                            Refund
                                                        </button>
                                                    </td>

                                                </tr>
                                            </tbody>

                                        </table>
                                        <div
                            class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800"
                        >
                        <div class="block w-full overflow-x-auto">
                                <div class="justify-between items-center block md:flex">
                                    <div class="flex items-center justify-start flex-wrap mb-3">
                                    <button
                                        @click="changePage(-1)"
                                        :disabled="currentPage == 1"
                                        :class="{
                                            'hidden': currentPage == 1,
                                        }"
                                        type="button"
                                        class="text-gray-500 bg-white mr-5 hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"

                                    >
                                        Previous
                                    </button>
                                    <button
                                        @click="changePage(1)"
                                        :disabled="currentPage === totalPages"
                                        type="button"
                                        class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"

                                    >
                                        Next
                                    </button>
                                    </div>
                                    <div class="flex items-center justify-center">
                                    <small>Page {{ currentPage }}</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                                    </div>
                                </div>
                        </div>
                    </div> -->

                <div id="payModal" class="payModal mt-10 md:mt-0">
                    <div class="pay-modal-content flex flex-col" :style="{ width: isMobileView ? '97%' : '30%' }">
                        <div class="w-full">
                            <span>
                                {{ selectedBill ? removeUnderscoreAndCapitalizeAfterSpace(selectedBill.category) :
                                    'Payment' }}
                            </span>

                            <span class="float-right cursor-pointer" @click="closeModal()">
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full h-[200px] flex justify-center items-center my-3" style="border: 1px solid black">
                            <span class="text-2xl">
                                {{ selectedBill ? moneyFormat(selectedBill.billing.amount) : 0.00 }}
                            </span>
                        </div>

                        <div class="w-full mt-3">
                            <!-- <div>Payment Method:</div> -->

                            <input type="radio" value="Online Payment" v-model="payment_method" />
                            <label class="ml-1 mt-2">Online Payment</label>

                            <input type="radio" value="Bank Transfer" class="ml-3" v-model="payment_method" />
                            <label class="ml-1 mt-2">Bank Transfer</label>
                        </div>

                        <div class="w-full mt-3" v-if="payment_method == 'Bank Transfer'">
                            <input type="file" id="id_picture" class="hidden" @change="
                                proofOfPyamentChange($event)
                                " accept="image/*" />

                            <label for="id_picture" class="relative cursor-pointer">
                                <div
                                    class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg">
                                    <img v-if="proof_of_payment" :src="proof_of_payment" alt="Proof of Payment"
                                        class="h-48 w-auto rounded-lg" />
                                    <span v-else>Input</span>
                                </div>
                            </label>
                        </div>

                        <div class="w-full mt-8">
                            <button class="rounded-md px-3 py-2 bg-cyan-300 float-right" @click="proceedPayment()">
                                Proceed Payment
                            </button>
                        </div>

                    </div>
                </div>

                <div id="refundModal" tabindex="-1" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div class="flex items-start justify-between p-4 border-b rounded-t ">
                                    <h3 class="text-xl font-semibold text-black">
                                        Refund Request
                                    </h3>
                                    <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                                        @click="closeLeaveModal()">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-6 space-y-6">
                                    <form class="mt-4">

                                        <label for="desc"
                                            class="block mb-2 text-base font-medium text-black">Description:</label>
                                        <input disabled type="text"
                                            :value="!!selectedPayment ? selectedPayment.billing.amount : null" id="desc"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                        <label for="amount"
                                            class="block mb-2 text-base font-medium text-black">Amount:</label>
                                        <input disabled type="text"
                                            :value="!!selectedPayment ? removeUnderscoreAndCapitalizeAfterSpace(selectedPayment.description) : null"
                                            id="amount"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">

                                        <label for="subject" class="block mb-2 text-base font-medium text-black">Choose how
                                            to receive refund:</label>
                                        <select id="subject" v-model="selectedPaymentMethod" @change="toggleTransfer"
                                            class="block w-full px-4 py-1 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 ">
                                            <option v-for="option in options" :key="option">
                                                {{ option }}
                                            </option>
                                        </select>
                                        <div class=" py-2 mt-2 bg-white rounded-b-lg" v-if="showEwallet">

                                            <form class="flex flex-col gap-1 ">
                                                <div class="mb-3">
                                                    <label for="EWalletName"
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-wallet/Bank
                                                        Name</label>
                                                    <input type="text" id="EWalletName" v-model="wallet_name"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="accName"
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account
                                                        Name:</label>
                                                    <input type="text" id="accName" v-model="account_name"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="accName"
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account
                                                        Number:</label>
                                                    <input type="text" id="accName" v-model="account_number"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                </div>
                                            </form>
                                        </div>
                                        <div class=" py-2 mt-4 bg-white rounded-b-lg" v-if="showBankTransfer">
                                            <form class="flex flex-col gap-1">
                                                <div class="mb-3">
                                                    <label for="BankName"
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-wallet/Bank
                                                        Name</label>
                                                    <input type="text" id="BankName" v-model="wallet_name"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="accbankName"
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account
                                                        Name:</label>
                                                    <input type="text" id="accbankName" v-model="account_name"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="accbankNumber"
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account
                                                        Number:</label>
                                                    <input type="text" id="accbankNumber" v-model="account_number"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                </div>
                                            </form>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="w-full border-t  border-gray-200">
                                <button @click="submitRefund()" type="button"
                                    class="text-white rounded-b-lg bg-orange-500 hover:bg-opacity-25 font-medium w-full text-sm px-5 py-2.5">
                                    Submit Refund Request
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
.main {
    height: 100%;
    min-height: 92vh;
    background-color: #e5e8e8;
}

.payModal {
    display: none;
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 100px;
    /* Location of the box */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4);
    /* Black w/ opacity */
}

/* Modal Content */
.pay-modal-content {
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
    background: transparent;
    /* make scrollbar transparent */
}
</style>
