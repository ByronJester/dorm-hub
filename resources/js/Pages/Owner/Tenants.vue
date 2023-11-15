<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import { ref, onMounted, computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import axios from "axios";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

export default {
    components: {
        AuthenticatedLayout,
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        InputLabel,
        TextInput,
        InputError,
    },
    setup() {
        const page = usePage();
        const headerTenants = [
            "Room Name",
            "Tenant Name",
            "Room Price",
            "Move-In Date",
            "Move-Out Date",
            "Balance",
        ];

        const searchQueryReserve = ref("");
            const itemsPerPageReserve = 10; // Set the maximum number of items per page to 10
            const currentPageReserve = ref(1); // Initialize to the first page

            
            const filteredDataReserve = computed(() => {
                const query = searchQueryReserve.value.toLowerCase().trim();
                if (!query) {
                    return tenantsData; // Return all data if the search query is empty.
                }

                return tenantsData.filter((row) => {
                    // Modify the conditions as needed for your specific search criteria.
                    return (
                        row.dorm_name.toLowerCase().includes(query) ||
                        row.room_name.toLowerCase().includes(query) ||
                        row.tenant_name.toLowerCase().includes(query)
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

        const options = page.props.dorms;

        const selectedDorm = ref(options[0].id);

        const tenantsData = ref([]);

        const tenants = page.props.tenants;

        const constructData = (dorm_id) => {
            var arrTenant = [];

            for (let t = 0; t < tenants.length; t++) {
                var balance = 0;

                let billings = tenants[t].billings;

                for (let b = 0; b < billings.length; b++) {
                    if (billings[b].is_paid == 0) {
                        balance = balance + billings[b].amount;
                    }
                }

                arrTenant.push({
                    id: tenants[t].id,
                    dorm_id: tenants[t].dorm_id,
                    room_name: tenants[t].room.name,
                    tenant_name: tenants[t].tenant_user.name,
                    fee: tenants[t].room.fee,
                    move_in: tenants[t].move_in,
                    move_out: tenants[t].move_out,
                    balance: balance,
                });
            }

            return arrTenant.filter((x) => {
                return x.dorm_id == dorm_id;
            });
        };

        const objectRemoveKey = (object, key = null) => {
            const newObject = Object.assign({}, object);

            delete newObject.id;
            delete newObject.dorm_id;

            return newObject;
        };

        onMounted(() => {
            tenantsData.value = constructData(selectedDorm.value);
        });

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        const dormChange = (evt) => {
            let dorm_id = evt.target.value;

            tenantsData.value = constructData(dorm_id);
        };

        const openComplainModal = () => {
            var modal = document.getElementById("complainModal");

            modal.style.display = "block";
        };

        const noticeTermination = (arg) => {
            swal(
                {
                    title: `Are you sure to notice this tenant for termination?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    axios
                        .post(
                            route("tenant.notice.termination", { id: arg.id }),
                            {
                                id: arg.refund_id,
                            }
                        )
                        .then((response) => {
                            swal(
                                "Success!",
                                `You successfully notice this tenant for termination.`,
                                "success"
                            );

                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        })
                        .catch((error) => {});
                }
            );
        };

        const removeTenant = (arg) => {
            swal(
                {
                    title: `Are you sure to remove this tenant?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    axios
                        .post(route("tenant.remove", { id: arg.id }), {
                            id: arg.refund_id,
                        })
                        .then((response) => {
                            swal(
                                "Success!",
                                `You successfully remove this tenant.`,
                                "success"
                            );

                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        })
                        .catch((error) => {});
                }
            );
        };

        const closeComplainModal = () => {
            var modal = document.getElementById("complainModal");

            modal.style.display = "none";
        };

        return {
            options,
            headerTenants,
            selectedDorm,
            tenantsData,
            objectRemoveKey,
            tenants,
            moneyFormat,
            dormChange,
            noticeTermination,
            removeTenant,
            openComplainModal,
            closeComplainModal,
            currentPageReserve,
            totalPagesReserve,
            changePageReserve
        };
    },
};
</script>
<template>
    <AuthenticatedLayout>
        <div class="px-4 pt-20 lg:ml-64">
            <div class="w-full">
                <div class="flex items-center justify-start">
                    <span
                        class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black mr-3"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 0 640 512"
                        >
                            <path
                                d="M335.5 4l288 160c15.4 8.6 21 28.1 12.4 43.5s-28.1 21-43.5 12.4L320 68.6 47.5 220c-15.4 8.6-34.9 3-43.5-12.4s-3-34.9 12.4-43.5L304.5 4c9.7-5.4 21.4-5.4 31.1 0zM320 160a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM144 256a40 40 0 1 1 0 80 40 40 0 1 1 0-80zm312 40a40 40 0 1 1 80 0 40 40 0 1 1 -80 0zM226.9 491.4L200 441.5V480c0 17.7-14.3 32-32 32H120c-17.7 0-32-14.3-32-32V441.5L61.1 491.4c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l37.9-70.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c16.3 0 31.9 4.5 45.4 12.6l33.6-62.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c32.4 0 62.1 17.8 77.5 46.3l33.6 62.3c13.5-8.1 29.1-12.6 45.4-12.6h19.5c32.4 0 62.1 17.8 77.5 46.3l37.9 70.3c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8L552 441.5V480c0 17.7-14.3 32-32 32H472c-17.7 0-32-14.3-32-32V441.5l-26.9 49.9c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l36.3-67.5c-1.7-1.7-3.2-3.6-4.3-5.8L376 345.5V400c0 17.7-14.3 32-32 32H296c-17.7 0-32-14.3-32-32V345.5l-26.9 49.9c-1.2 2.2-2.6 4.1-4.3 5.8l36.3 67.5c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8z"
                            />
                        </svg>
                    </span>
                    <h3 class="text-3xl">Manage Tenants</h3>
                </div>

                <hr class="h-px my-5 bg-orange-400 border-1 " />
                <div>
                    <p class="text-sm font-bold">Dorm:</p>
                    <select
                        v-model="selectedDorm"
                        id="subject"
                        @change="dormChange($event)"
                        class="block w-56 px-4 py-1.5 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500  "
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
                <div class="w-full mt-2">
                    <div class="w-full mb-5 mt-5">
                        <div
                            class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                        >
                            <div class="rounded-t mb-0 px-4 py-3 border-0">
                                <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                                >
                                <p class="text-xl mb-5 font-bold">Tenants Records</p>
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
                                <div class="block w-full overflow-x-auto mt-5">
                                    <table
                                        class="items-center w-full bg-transparent border-collapse"
                                    >
                                        <thead>
                                            <tr>
                                                <th
                                                    class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                                    v-for="header in headerTenants"
                                                    :key="header"
                                                >
                                                    {{ header }}
                                                </th>
                                                <th
                                                    class="px-6 align-middle border text-center border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                                >
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    item, rowIndex
                                                ) in tenantsData"
                                                :key="rowIndex"
                                            >
                                                <td
                                                    class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                                    v-for="(
                                                        value, colIndex
                                                    ) in objectRemoveKey(item)"
                                                    :key="colIndex"
                                                >
                                                    {{
                                                        colIndex == "fee" ||
                                                        colIndex == "balance"
                                                            ? moneyFormat(value)
                                                            : value
                                                    }}
                                                </td>
                                                <td
                                                    class="border-t-0 px-6 align-middle text-center border-l-0 border-r-0 text-green-500 text-xs whitespace-nowrap p-4"
                                                >
                                                    <AppDropdown class="">
                                                        <button>
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                height="24"
                                                                viewBox="0 0 448 512"
                                                            >
                                                                <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                <path
                                                                    d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <AppDropdownContent
                                                            class="bg-white z-50"
                                                        >
                                                        <AppDropdownItem @click="noticeTermination(item)">
                                                        Notice Termination
                                                    </AppDropdownItem>
                                                            <AppDropdownItem @click="removeTenant(item)">
                                                                Remove Tenant
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
                </div>

                <div
                    id="complainModal"
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
                                        Add Tenant
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center "
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
                                <div class="p-6 space-y-6">
                                    <form>
                                        <div
                                            class="flex flex-col gap-3 items-center justify-center"
                                        >
                                            <div class="flex mb-3 md:mb-0">
                                                <div>
                                                    <img
                                                        src="https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com"
                                                        alt="Profile picture"
                                                        class="rounded-full block md:h-40 w-40 bg-no-repeat bg-cover object-fit max-w-full bg-gray-100 "
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="inline-flex">
                                                    <a
                                                        class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-blue-600 ring-blue-300 py-2 px-3"
                                                        disabled="false"
                                                    >
                                                        <span
                                                            class="inline-flex justify-center items-center w-6 h-6"
                                                            ><svg
                                                                viewBox="0 0 24 24"
                                                                width="16"
                                                                height="16"
                                                                class="inline-block"
                                                            >
                                                                <path
                                                                    fill="currentColor"
                                                                    d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"
                                                                ></path>
                                                            </svg> </span
                                                        ><span class="px-2"
                                                            >Upload</span
                                                        >

                                                        <input
                                                            @change="
                                                                imageChange(
                                                                    $event
                                                                )
                                                            "
                                                            accept="image/png, image/gif, image/jpeg"
                                                            id="profile_image"
                                                            type="file"
                                                            class="absolute top-0 left-0 opacity-0 outline-none -z-1"
                                                        />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-5" />
                                        <div
                                            class="grid grid-cols-2 gap-2 mt-2"
                                        >
                                            <div>
                                                <InputLabel
                                                    for="first_name"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="First Name"
                                                />

                                                <TextInput
                                                    id="first_name"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="ex: Juan"
                                                    required
                                                    autofocus
                                                    autocomplete="first_name"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="last_name"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Last Name"
                                                />

                                                <TextInput
                                                    id="middle_name"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="ex: Casimero"
                                                    autofocus
                                                    autocomplete="middle_name"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="username"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Username"
                                                />

                                                <TextInput
                                                    id="username"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="ex: Jear12"
                                                    autofocus
                                                    autocomplete="middle_name"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="phone_number"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Contact"
                                                />

                                                <input
                                                    id="phone_number"
                                                    type="number"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="ex: 09112233445"
                                                    required
                                                    autocomplete="phone_number"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="password"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Password"
                                                />

                                                <TextInput
                                                    id="password"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="*********"
                                                    autofocus
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="confirm_password"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Confirm Password"
                                                />

                                                <TextInput
                                                    id="confirm_password"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="*********"
                                                    autofocus
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div class="mt-4">
                                                <div>
                                                    <div
                                                        class="flex gap-2 items-center"
                                                    >
                                                        <InputLabel
                                                            for="id_picture"
                                                            class="block text-sm font-medium text-gray-900 "
                                                            value="Valid ID"
                                                        />
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            color="orange"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="1.5"
                                                            stroke="currentColor"
                                                            class="w-5 h-5 cursor-pointer hover:bg-orange-400 animate-bounce"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                                                            />
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs text-red-500"
                                                        >(ex: Passport, National
                                                        Id, etc For Student:
                                                        School ID)</span
                                                    >
                                                </div>

                                                <input
                                                    type="file"
                                                    id="id_picture"
                                                    class="hidden"
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
                                                            src="https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com"
                                                            alt="Valid ID"
                                                            class="h-48 w-auto rounded-lg"
                                                        />
                                                    </div>
                                                </label>

                                                <InputError class="mt-2" />
                                            </div>
                                            <div class="mt-4">
                                                <div>
                                                    <div
                                                        class="flex gap-2 items-center"
                                                    >
                                                        <InputLabel
                                                            for="selfie_id_picture"
                                                            class="block text-sm font-medium text-gray-900 "
                                                            value="Selfie withValid ID"
                                                        />
                                                    </div>
                                                    <span
                                                        class="text-xs text-red-500"
                                                        >Add selfie holding the
                                                        valid id</span
                                                    >
                                                </div>

                                                <input
                                                    type="file"
                                                    id="selfie_id_picture"
                                                    class="hidden"
                                                    accept="image/*"
                                                />

                                                <label
                                                    for="selfie_id_picture"
                                                    class="relative cursor-pointer"
                                                >
                                                    <div
                                                        class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                    >
                                                        <img
                                                            src="https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com"
                                                            alt="Valid ID"
                                                            class="h-48 w-auto rounded-lg"
                                                        />
                                                    </div>
                                                </label>

                                                <InputError class="mt-2" />
                                            </div>

                                            <div class="mt-4">
                                                <div class="w-full">
                                                    <InputLabel
                                                        for="source_of_income"
                                                        class="block text-sm font-medium text-gray-900 "
                                                        value="Source of Income"
                                                    />

                                                    <select
                                                        class="w-full mt-1 rounded-md"
                                                    >
                                                        <option value="Salary">
                                                            Salary
                                                        </option>
                                                        <option
                                                            value="Business"
                                                        >
                                                            Business
                                                        </option>
                                                        <option value="Pension">
                                                            Pension
                                                        </option>
                                                        <option
                                                            value="Allowance"
                                                        >
                                                            Allowance
                                                        </option>
                                                    </select>

                                                    <InputError class="mt-2" />
                                                </div>

                                                <div class="w-full mt-3">
                                                    <InputLabel
                                                        for="monthly_income"
                                                        class="block text-sm font-medium text-gray-900 "
                                                        value="Monthly Income"
                                                    />

                                                    <TextInput
                                                        id="monthly_income"
                                                        type="number"
                                                        class="mt-1 block w-full"
                                                        required
                                                    />

                                                    <InputError class="mt-2" />
                                                </div>
                                            </div>

                                            <div
                                                class="w-full flex flex-col mt-4"
                                            >
                                                <div class="w-full">
                                                    <InputLabel
                                                        for="proof"
                                                        class="block text-sm font-medium text-gray-900 "
                                                        value="Proof of Income"
                                                    />

                                                    <input
                                                        type="file"
                                                        id="proof"
                                                        class="hidden"
                                                        accept="image/*"
                                                    />

                                                    <label
                                                        for="proof"
                                                        class="relative cursor-pointer"
                                                    >
                                                        <div
                                                            class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                        >
                                                            <img
                                                                src="https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com"
                                                                alt="Proof"
                                                                class="h-48 w-auto rounded-lg"
                                                            />
                                                        </div>
                                                    </label>

                                                    <InputError class="mt-2" />
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-5" />
                                        <div class="w-full">
                                            <InputLabel
                                                for="assign_room"
                                                class="block text-sm font-medium text-gray-900 "
                                                value="Assign Room"
                                            />

                                            <select
                                                class="w-full mt-1 rounded-md"
                                            >
                                                <option value="1">
                                                    Room 1
                                                </option>
                                                <option value="2">
                                                    Room 2
                                                </option>
                                                <option value="3">
                                                    Room 3
                                                </option>
                                                <option value="4">
                                                    Room 4
                                                </option>
                                            </select>

                                            <InputError class="mt-2" />
                                        </div>
                                    </form>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b "
                                >
                                    <button
                                        @click.prevent="submitComplain()"
                                        type="button"
                                        class="text-white bg-orange-400 hover:bg-orange-200 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
