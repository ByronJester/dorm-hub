<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage, useForm, router } from "@inertiajs/vue3";
import { ref, reactive, watch, onMounted, computed } from "vue";
import { MapboxMap, MapboxMarker } from "@studiometa/vue-mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import axios from "axios";

export default {
    components: {
        AuthenticatedLayout,
    },
    setup() {
        const page = usePage();
        const user = computed(() => page.props.auth.user);
        const payments = page.props.payments;
        const application = ref({});
        const owner = ref({});
        const methods = ref([]);
        const isMobileView = ref(false);
        isMobileView.value = screen.width < 600;
        const receipt = ref(null);
        const payment_id = ref();
        const imageError = ref(null);
        const nexPayment = page.props.nexPayment
        const lastBilled = page.props.lastBilled
        const balance = page.props.balance
        const totalAmountPaid = page.props.totalAmountPaid
        const options = ["E-Wallet", "Cash", "Bank Transfer"];

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

        const openLeaveModal = () => {
            var modal = document.getElementById("refundModal");

            modal.style.display = "block";
        };

        const closeLeaveModal = () => {
            var modal = document.getElementById("refundModal");

            modal.style.display = "none";
        };

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

        const headers = ["Payment Date" , "Payment Method", "Amount", "Description", "Status", "Receipt"];


        var data = [];

        const removeUnderscoreAndCapitalizeAfterSpace = (inputString) => {
            if(inputString ===  undefined || typeof inputString === undefined) {
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

        for(let p = 0; p < payments.length; p++) {
            data.push(
                {
                    // id: payments[p].id,
                    date: payments[p].display_date,
                    payment_method: payments[p].payment_method,
                    amount: payments[p].billing.amount,
                    category: removeUnderscoreAndCapitalizeAfterSpace(payments[p].description),
                    status: removeUnderscoreAndCapitalizeAfterSpace(payments[p].status),
                    receipt: '',
                    action: payments[p]
                }
            )
        }
        //

            const searchQuery = ref("");
            const itemsPerPage = 5; // Set the maximum number of items per page to 10
            const currentPage = ref(1); // Initialize to the first page


            const filteredData = computed(() => {
                const query = searchQuery.value.toLowerCase().trim();
                if (!query) {
                    return data; // Return all data if the search query is empty.
                }

                return data.filter((row) => {
                    // Modify the conditions as needed for your specific search criteria.
                    return (
                        row.date.toLowerCase().includes(query) ||
                        row.payment_method.toLowerCase().includes(query) ||
                        row.category.toLowerCase().includes(query) ||
                        row.status.toLowerCase().includes(query)
                        // Add more conditions for other columns as needed
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
                            if(payment_method.value == 'Online Payment') {
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

        console.log(totalAmountPaid)

        return {
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
            toggleTransfer
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <div
            class="max-w-[2520px] mt-16 mx-auto xl:px-20 md:px-10 sm:px-2 px-4"
        >
            <div
                className="
                        max-w-screen-lg
                        mx-auto
                        "
            >
                <section class="pt-6 mb-6 flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <span
                            class="inline-flex justify-center items-center w-6 h-6 mr-2"
                            ><svg
                                viewBox="0 0 24 24"
                                width="20"
                                height="20"
                                class="inline-block"
                            >
                                <path
                                    fill="currentColor"
                                    d="M7 12C9.2 12 11 10.2 11 8S9.2 4 7 4 3 5.8 3 8 4.8 12 7 12M11 20V14.7C9.9 14.3 8.5 14 7 14C3.1 14 0 15.8 0 18V20H11M22 4H15C13.9 4 13 4.9 13 6V18C13 19.1 13.9 20 15 20H22C23.1 20 24 19.1 24 18V6C24 4.9 23.1 4 22 4M18 18H16V6H18V18Z"
                                ></path></svg></span>
                        <h1 class="text-3xl leading-tight">Billing overview</h1>
                    </div>
                </section>
                <div class="grid grid-cols-12 gap-6 mb-6">
                    <!--Eto yung amount para sa upcoming payment-->
                    <div class="col-span-12 shadow-lg sm:col-span-6 xl:col-span-3">
                        <div
                            class="flex-col dark:bg-slate-900/70 bg-white flex"
                        >
                            <div class="flex-1 p-6">

                                <div class="justify-between items-center flex">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <div>
                                            <h3
                                                class="text-lg leading-tight text-green-500 dark:text-slate-400"
                                            >
                                                Upcoming Payment
                                            </h3>
                                            <!--Dito kasama na din yung balance pero pag nabayaran ni tenant ahead yung balance yung bill lang every month-->
                                            <h1
                                                class="text-3xl leading-tight font-semibold"
                                            >
                                                <div>
                                                    {{ !!nexPayment ?  moneyFormat(nexPayment.billing.amount) : 0.00 }}
                                                </div>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!---->
                        </div>
                    </div>
                    <!--Dito lalabas yung balance ni user-->
                    <div class="col-span-12 shadow-lg sm:col-span-6 xl:col-span-3">
                        <div
                            class="flex-col dark:bg-slate-900/70 bg-white flex"
                        >
                            <div class="flex-1 p-6">

                                <div class="justify-between items-center flex">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <div>
                                            <h3
                                                class="text-lg leading-tight text-red-500 dark:text-slate-400"
                                            >
                                                Balance
                                            </h3>
                                            <h1
                                                class="text-3xl leading-tight font-semibold"
                                            >
                                                <div>{{moneyFormat(balance)}}</div>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!---->
                        </div>
                    </div>
                    <!--Dito yung total na nabayad na ni user-->
                    <div class="col-span-12 rounded-2xl shadow-lg sm:col-span-6 xl:col-span-3">
                        <div
                            class="flex-col dark:bg-slate-900/70 bg-white flex"
                        >
                            <div class="flex-1 p-6">

                                <div class="justify-between items-center flex">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <div>
                                            <h3
                                                class="text-lg leading-tight text-orange-500 dark:text-slate-400"
                                            >
                                                Total Amount Paid
                                            </h3>
                                            <h1
                                                class="text-3xl leading-tight font-semibold"
                                            >
                                                <div>{{moneyFormat(totalAmountPaid)}}</div>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!---->
                        </div>
                    </div>

                </div>

                <div class="flex-1 shadow-lg rounded-lg p-6">

                    <div class="md:flex md:justify-between md:items-center">
                        <div class="md:flex md:items-center">
                            <div
                                class="mb-6 text-center md:mr-6 md:mb-0 md:text-left"
                            >
                                <!--Date kung kelan yung billing-->
                                <p class="text-gray-500">Next payment on</p>
                                <h1 class="text-xl font-semibold">
                                    {{ !!nexPayment ? nexPayment.display_date : '' }}
                                </h1>
                            </div>
                            <div class="mb-6 text-center md:mb-0 md:text-left">
                                <p class="text-gray-500">Last billed on</p>
                                <h1 class="text-xl">{{ !! lastBilled ? lastBilled.display_created_date : ''}}</h1>
                            </div>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-gray-500">Amount due</p>
                            <!--Pag walang balance yung upcoming payment lang pero pag may balance iplus sa upcoming payment-->
                            <h1 class="text-2xl font-semibold">{{ !!nexPayment ? moneyFormat(nexPayment.billing.amount) : 0.00 }}</h1>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mt-5 justify-start">
                    <span
                        class="inline-flex justify-center items-center w-6 h-6 mr-2"
                        ><svg
                            viewBox="0 0 24 24"
                            width="20"
                            height="20"
                            class="inline-block"
                        >
                            <path
                                fill="currentColor"
                                d="M7 12C9.2 12 11 10.2 11 8S9.2 4 7 4 3 5.8 3 8 4.8 12 7 12M11 20V14.7C9.9 14.3 8.5 14 7 14C3.1 14 0 15.8 0 18V20H11M22 4H15C13.9 4 13 4.9 13 6V18C13 19.1 13.9 20 15 20H22C23.1 20 24 19.1 24 18V6C24 4.9 23.1 4 22 4M18 18H16V6H18V18Z"
                            ></path></svg></span>
                    <h1 class="text-3xl leading-tight">Payment(s)</h1>
                </div>
                <!--Payments-->
                <div class="w-full mt-2 mb-5">
                        <div class="w-full mb-12 mt-5">
                            <div
                                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                                >
                                    <div
                                        class="rounded-t mb-0 px-4 py-3 border-0"
                                    >
                                        <div
                                            class="flex flex-wrap items-center"
                                        >
                                            <div
                                                class="relative w-full px-4 max-w-full flex-grow flex-1"
                                            >
                                            <form class="flex items-center">
                                                <label for="simple-search" class="sr-only">Search</label>
                                                <div class="relative w-full">
                                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 0V6a3 3 0 0 0-3-3H9m1.5-2-2 2 2 2"/>
                                                        </svg>
                                                    </div>
                                                    <input type="text" id="simple-search" v-model="searchQuery" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search in table..." required>
                                                </div>
                                                <button type="submit" class="p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                                    </svg>
                                                    <span class="sr-only">Search</span>
                                                </button>
                                            </form>
                                            </div>
                                        </div>
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
                                                            v-if="colIndex == 'action'" :disabled="value.status == 'paid' || value.status == 'waiting_for_approval'"
                                                            :class="{'cursor-not-allowed bg-opacity-20': value.status == 'paid' || value.status == 'waiting_for_approval'}"
                                                        >
                                                            {{ value.status == 'paid' ? 'Paid' : 'Pay' }}
                                                        </button>

                                                        <button @click="openLeaveModal()" class="bg-cyan-400 text-white w-14 px-2 rounded-md font-semibold py-0.5"
                                                            v-if="(colIndex == 'action' && value.status == 'paid') && (value.description == 'monthly_fee' || value.billing.subject != null)"
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
                    </div>

                    <div id="payModal" class="payModal mt-10 md:mt-0">
                        <div class="pay-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '30%'}">
                            <div class="w-full">
                                <span>
                                    {{ selectedBill ? removeUnderscoreAndCapitalizeAfterSpace(selectedBill.category) : 'Payment'}}
                                </span>

                                <span class="float-right cursor-pointer"
                                    @click="closeModal()"
                                >
                                    <i class="fa-solid fa-xmark"></i>
                                </span>
                            </div>

                            <div class="w-full h-[200px] flex justify-center items-center my-3"
                                style="border: 1px solid black"
                            >
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
                                <input
                                    type="file"
                                    id="id_picture"
                                    class="hidden"
                                    @change="
                                        proofOfPyamentChange($event)
                                    "
                                    accept="image/*"
                                />

                                <label
                                    for="id_picture"
                                    class="relative cursor-pointer"
                                >
                                    <div
                                        class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                    >
                                        <img
                                            v-if="proof_of_payment"
                                            :src="proof_of_payment"
                                            alt="Proof of Payment"
                                            class="h-48 w-auto rounded-lg"
                                        />
                                        <span v-else
                                            >Input</span
                                        >
                                    </div>
                                </label>
                            </div>

                            <div class="w-full mt-8">
                                <button class="rounded-md px-3 py-2 bg-cyan-300 float-right"
                                    @click="proceedPayment()"
                                >
                                    Proceed Payment
                                </button>
                            </div>

                        </div>
                    </div>

                    <div
                    id="refundModal"
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
                                    class="flex items-start justify-between p-4 border-b rounded-t "
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Refund Request
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                                        @click="closeLeaveModal()"
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
                                    <form class="mt-4">
                                        
                                        <label for="desc" class="block mb-2 text-base font-medium text-black">Description:</label>
                                        <input disabled type="text" id="desc" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                        <label for="amount" class="block mb-2 text-base font-medium text-black">Amount:</label>
                                        <input disabled type="text" id="amount"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">

                                        <label for="subject" class="block mb-2 text-base font-medium text-black">Choose how to receive refund:</label>
                                            <select
                                                id="subject"
                                                v-model="selectedPaymentMethod"
                                                 @change="toggleTransfer"
                                                class="block w-full px-4 py-1 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 "
                                            >
                                                <option
                                                    v-for="option in options"
                                                    :key="option"
                                                >
                                                    {{ option }}
                                                </option>
                                            </select>
                                            <div class=" py-2 mt-2 bg-white rounded-b-lg" v-if="showEwallet">

                                                <form class="flex flex-col gap-1 ">
                                                    <div class="mb-3">
                                                        <label for="EWalletName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-wallet/Bank Name</label>
                                                        <input type="text" id="EWalletName" v-model="wallet_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="accName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Name:</label>
                                                        <input type="text" id="accName" v-model="account_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="accName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Number:</label>
                                                        <input type="text" id="accName" v-model="account_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                </form>
                                            </div>
                                            <div class=" py-2 mt-4 bg-white rounded-b-lg" v-if="showBankTransfer">
                                                <form class="flex flex-col gap-1">
                                                    <div class="mb-3">
                                                        <label for="BankName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-wallet/Bank Name</label>
                                                        <input type="text" id="BankName" v-model="wallet_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="accbankName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Name:</label>
                                                        <input type="text" id="accbankName" v-model="account_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="accbankNumber" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Number:</label>
                                                        <input type="text" id="accbankNumber" v-model="account_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                </form>
                                            </div>
                                    </form>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="w-full border-t  border-gray-200"
                                >
                                    <button
                                        @click=""
                                        type="button"
                                        class="text-white rounded-b-lg bg-orange-500 hover:bg-opacity-25 font-medium w-full text-sm px-5 py-2.5"
                                    >
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
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0, 0, 0); /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
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
    background: transparent; /* make scrollbar transparent */
}
</style>
