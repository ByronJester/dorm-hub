<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import { ref } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import axios from "axios";

export default {
    components: {
        AuthenticatedLayout,
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
    },
    setup() {
        const page = usePage();

        const selectedRefund = ref(null)

        const openComplainModal = (arg) => {
            var modal = document.getElementById("viewModal");

            modal.style.display = "block";

            selectedRefund.value = arg
            console.log(arg)
        };

        const closeComplainModal = () => {
            var modal = document.getElementById("viewModal");

            modal.style.display = "none";
        };

        const headerRefunds=["Refund Description", "Refund Method", "Bank/Wallet Name", "Account number", "Account Name", "Status", "Refund Date", "Action"]

        const refundsData = page.props.refunds

        const refundObjectRemoveKey = (object, key = null) => {
            const newObject = Object.assign({}, object);

            delete newObject.dorm_id
            delete newObject.payment_id
            delete newObject.refund_id
            delete newObject.proof_of_refund

            return newObject;
        }

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

        const approveRefund = () => {
            var status = 'refunded'

            swal({
                title: `Are you sure to mark as ${status} this refund?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function(){
                axios.post(route('owner.refund.change.status', status),
                    {
                        id: selectedRefund.value.refund_id,
                    })
                    .then(response => {
                        swal("Success!", `You successfully mark as ${status} this refund.`, "success");

                        setTimeout(function () {
                            location.reload()
                        }, 3000);
                    })
                    .catch(error => {

                    })
            });
        }

        const declineRefund = () => {
            var status = 'declined'

            swal({
                title: `Are you sure to mark as ${status} this refund?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function(){
                axios.post(route('owner.refund.change.status', status),
                    {
                        id: selectedRefund.value.refund_id,
                    })
                    .then(response => {
                        swal("Success!", `You successfully mark as ${status} this refund.`, "success");

                        setTimeout(function () {
                            location.reload()
                        }, 3000);
                    })
                    .catch(error => {

                    })
            });
        }

        return {
            openComplainModal,
            closeComplainModal,
            headerRefunds,
            refundsData,
            refundObjectRemoveKey,
            removeUnderscoreAndCapitalizeAfterSpace,
            selectedRefund,
            approveRefund,
            declineRefund
        };
    },
};
</script>
<template>
    <AuthenticatedLayout>
        <div class="p-4 mt-16 lg:ml-64">
            <div class="flex items-center justify-start">
                <span
                    class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70  mr-3"
                >
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" height="40" width="35" version="1.1" id="Capa_1" viewBox="0 0 492.153 492.153" xml:space="preserve">
<g>
	<path d="M426.638,87.91c-42.247-42.247-98.418-65.514-158.166-65.514c-5.799,0-10.5,4.701-10.5,10.5v56.531   c0,5.799,4.701,10.5,10.5,10.5c80.587,0,146.148,65.561,146.148,146.147c0,80.587-65.561,146.148-146.148,146.148   c-73.915,0-135.549-54.985-144.913-127.088h36.91c0.008,0,0.013,0.001,0.02,0c5.799,0,10.5-4.701,10.5-10.5   c0-2.887-1.165-5.502-3.051-7.4l-75.345-84.401c-1.993-2.232-4.842-3.508-7.833-3.508c-0.017,0-0.034,0-0.05,0   c-3.009,0.015-5.867,1.319-7.85,3.583L2.6,247.719c-2.714,3.101-3.365,7.502-1.663,11.254c1.702,3.753,5.442,6.163,9.563,6.163   h35.11c4.553,54.02,28.36,104.134,67.69,142.033c41.883,40.359,96.99,62.587,155.171,62.587   c59.748,0,115.919-23.267,158.166-65.515c42.248-42.248,65.515-98.419,65.515-158.166   C492.153,186.328,468.886,130.157,426.638,87.91z M268.472,448.756c-109.242,0-198.191-85.45-202.501-194.535   c-0.223-5.633-4.854-10.085-10.492-10.085H33.65l51.186-58.457l52.185,58.457H112.06c-2.883,0-5.639,1.186-7.621,3.278   c-1.983,2.092-3.018,4.908-2.863,7.786c4.774,88.611,78.084,158.023,166.897,158.023c92.166,0,167.148-74.982,167.148-167.148   c0-88.639-69.355-161.384-156.648-166.821V43.665c106.9,5.479,192.181,94.173,192.181,202.41   C471.153,357.834,380.231,448.756,268.472,448.756z"/>
	<path d="M255.41,255.643v79.405h-25.332c-5.799,0-10.5,4.701-10.5,10.5s4.701,10.5,10.5,10.5h25.332v13.028   c0,5.799,4.701,10.5,10.5,10.5c5.799,0,10.5-4.701,10.5-10.5v-13.964c28.222-4.984,49.733-29.669,49.733-59.3   c0-29.63-21.512-54.314-49.733-59.299v-79.407l22.119-0.001c5.799,0,10.5-4.701,10.5-10.5c0-5.799-4.701-10.5-10.5-10.5   l-22.119,0.001v-13.03c0-5.799-4.701-10.5-10.5-10.5c-5.799,0-10.5,4.701-10.5,10.5v13.965c-28.224,4.985-49.736,29.67-49.736,59.3   C205.674,225.973,227.186,250.658,255.41,255.643z M305.143,295.813c0,17.998-12.184,33.193-28.733,37.797v-75.593   C292.959,262.62,305.143,277.816,305.143,295.813z M255.41,158.545v75.595c-16.551-4.604-28.736-19.8-28.736-37.799   C226.674,178.344,238.859,163.149,255.41,158.545z"/>
</g>
</svg>
                </span>
                <h3 class="text-3xl">Manage Refund</h3>
            </div>

            <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />
            <div >
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                >
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div
                                class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                            >
                                <p class="text-xl mb-5 font-bold">
                                    Refund Records
                                </p>
                                <div
                                    class="flex-row flex items-center justify-between"
                                >
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
                                    <div
                                        class="flex flex-row items-center gap-2 font-semibold"
                                    >
                                        <button
                                            class="py-2.5 rounded-lg bg-orange-400 text-white px-4"
                                        >
                                            PDF
                                        </button>

                                        <button
                                            class="py-2.5 rounded-lg bg-orange-400 text-white px-4"
                                        >
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
                                        v-for="header in headerRefunds"
                                        :key="header"
                                    >
                                        {{ header }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, rowIndex) in refundsData"
                                    :key="rowIndex"
                                >
                                    <td
                                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                        v-for="(value, colIndex) in refundObjectRemoveKey(item)"
                                        :key="colIndex"
                                    >

                                        {{ colIndex == 'refund_description' ? removeUnderscoreAndCapitalizeAfterSpace(value) : value }}

                                    </td>

                                    <td
                                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                    >
                                    <AppDropdown class="flex justify-center items-center">
                                            <button >
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24"  viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>
                                            </button>
                                            <AppDropdownContent class="bg-white z-50 " v-if="item.status == 'ongoing'">
                                                <AppDropdownItem @click="openComplainModal(item)">
                                                    View
                                                </AppDropdownItem>
                                            </AppDropdownContent>
                                    </AppDropdown>


                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div
                            class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800"
                        >
                            <div class="block w-full overflow-x-auto">
                                <div
                                    class="justify-between items-center block md:flex"
                                >
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
                                            :disabled="
                                                currentPageReserve ===
                                                totalPagesReserve
                                            "
                                            type="button"
                                            class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                        >
                                            Next
                                        </button>
                                    </div>
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <small
                                            >Page
                                            {{ currentPageReserve }}</small
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                id="viewModal"
                tabindex="-1"
                aria-hidden="true"
                style="background-color: rgba(0, 0, 0, 0.7)"
                class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
            >
                <div class="h-screen flex justify-center items-center">
                    <div class="relative w-full max-w-xl max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow">
                            <!-- Modal header -->
                            <div
                                class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                            >
                                <h3 class="text-xl font-semibold text-black">
                                    Refund Request
                                </h3>
                                <button
                                    type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    @click="closeComplainModal()"
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
                            <div class="p-6 space-y-6" v-if="selectedRefund">
                                <div class="mb-3">
                                    <label
                                        for="EWalletName"
                                        class="block mb-2 text-sm font-medium text-gray-900 "
                                        >E-wallet/Bank Name</label
                                    >
                                    <input
                                        :value="selectedRefund.wallet_name"
                                        disabled
                                        type="text"
                                        id="EWalletName"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                                    />
                                </div>
                                <div class="mb-3">
                                    <label
                                        disabled
                                        for="accName"
                                        class="block mb-2 text-sm font-medium text-gray-900 "
                                        >Account Name:</label
                                    >
                                    <input
                                        :value="selectedRefund.account_name"
                                        disabled
                                        type="text"
                                        id="accName"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                                    />
                                </div>
                                <div class="mb-3">
                                    <label
                                        for="accName"
                                        class="block mb-2 text-sm font-medium text-gray-900 "
                                        >Account Number:</label
                                    >
                                    <input
                                        :value="selectedRefund.account_number"
                                        disabled
                                        type="text"
                                        id="accName"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                                    />
                                </div>
                                <div class="mb-3">
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 "
                                        >Proof of refund:</label
                                    >

                                    <img :src="selectedRefund.proof_of_refund"
                                        class="w-full h-[200px]"
                                    />
                                    <!--padagdag image pre-->
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                            >
                                <button
                                    @click="declineRefund()"
                                    type="button"
                                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                >
                                    Decline
                                </button>

                                <button
                                    @click="approveRefund()"
                                    type="button"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
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
