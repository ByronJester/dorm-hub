<script>
import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import { ref, onMounted } from 'vue'
import { usePage, useForm } from "@inertiajs/vue3";
import axios from "axios";


export default {
        components: {
            AuthenticatedLayout,
            AppDropdown,
            AppDropdownContent,
            AppDropdownItem,
        },
        setup(){
            const page = usePage();
            const headerTenants=["Room Name", "Tenant Name", "Room Price", "Move-In Date", "Move-Out Date", "Balance"];

            const options = page.props.dorms

            const selectedDorm = ref(options[0].id)

            const tenantsData = ref([])

            const tenants = page.props.tenants

            const constructData = (dorm_id) => {
                var arrTenant = [];

                for(let t = 0; t < tenants.length; t++) {
                    var balance = 0;

                    let billings = tenants[t].billings

                    for(let b = 0; b < billings.length; b++) {
                        if(billings[b].is_paid == 0) {
                            balance = balance + billings[b].amount
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
                        balance: balance
                    });
                }

                return arrTenant.filter(x => {
                    return x.dorm_id == dorm_id;
                });
            }

            const objectRemoveKey = (object, key = null) => {
                const newObject = Object.assign({}, object);

                delete newObject.id
                delete newObject.dorm_id

                return newObject;
            }

            onMounted(() => {
                tenantsData.value = constructData(selectedDorm.value)
            });

            const moneyFormat = (amount) => {
                amount = parseFloat(amount).toFixed(2);

                return (
                    "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
            };

            const dormChange = (evt) => {
                let dorm_id = evt.target.value

                tenantsData.value = constructData(dorm_id)
            }

            return{
                options,
                headerTenants,
                selectedDorm,
                tenantsData,
                objectRemoveKey,
                tenants,
                moneyFormat,
                dormChange
            }
        }
}
</script>
<template>
    <AuthenticatedLayout>
    <div class="px-4 pt-20 lg:ml-64">
        <div class="w-full">
                <div class="flex items-center justify-start">
                        <span class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 640 512"><path d="M335.5 4l288 160c15.4 8.6 21 28.1 12.4 43.5s-28.1 21-43.5 12.4L320 68.6 47.5 220c-15.4 8.6-34.9 3-43.5-12.4s-3-34.9 12.4-43.5L304.5 4c9.7-5.4 21.4-5.4 31.1 0zM320 160a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM144 256a40 40 0 1 1 0 80 40 40 0 1 1 0-80zm312 40a40 40 0 1 1 80 0 40 40 0 1 1 -80 0zM226.9 491.4L200 441.5V480c0 17.7-14.3 32-32 32H120c-17.7 0-32-14.3-32-32V441.5L61.1 491.4c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l37.9-70.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c16.3 0 31.9 4.5 45.4 12.6l33.6-62.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c32.4 0 62.1 17.8 77.5 46.3l33.6 62.3c13.5-8.1 29.1-12.6 45.4-12.6h19.5c32.4 0 62.1 17.8 77.5 46.3l37.9 70.3c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8L552 441.5V480c0 17.7-14.3 32-32 32H472c-17.7 0-32-14.3-32-32V441.5l-26.9 49.9c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l36.3-67.5c-1.7-1.7-3.2-3.6-4.3-5.8L376 345.5V400c0 17.7-14.3 32-32 32H296c-17.7 0-32-14.3-32-32V345.5l-26.9 49.9c-1.2 2.2-2.6 4.1-4.3 5.8l36.3 67.5c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8z"/></svg>                </span>
                        <h3 class="text-3xl">Manage Tenants</h3>
                    </div>

                <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />
                <div>
                        <p class="text-sm font-bold">Dorm:</p>
                        <select
                            v-model="selectedDorm"
                            id="subject"
                            @change="dormChange($event)"
                            class="block w-56 px-4 py-1.5 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option v-for="option in options" :key="option.id" :value="option.id">
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
                                        <!-- <div class="mb-3">
                                            <button
                                            class="flex items-center justify-start bg-orange-400 py-2 px-2 gap-2 rounded-md h-8 text-white"
                                            @click=""
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>

                                                    Add Tenant
                                            </button>
                                        </div> -->
                                    <form class="flex items-center">

                                        <label
                                            for="simple-search"
                                            class="sr-only"
                                            >Search</label
                                        >
                                        <div class="relative w-full">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                            >
                                                <svg
                                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 18 20"
                                                >
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 0V6a3 3 0 0 0-3-3H9m1.5-2-2 2 2 2"
                                                    />
                                                </svg>
                                            </div>
                                            <input
                                                type="text"
                                                id="simple-search"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Search in table..."
                                                required
                                            />
                                        </div>
                                        <button
                                            type="submit"
                                            class="p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                                                />
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
                                        v-for="(item, rowIndex) in tenantsData"
                                        :key="rowIndex"
                                    >
                                        <td
                                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                            v-for="(value, colIndex) in objectRemoveKey(item)"
                                            :key="colIndex"
                                        >
                                            {{ colIndex == 'fee' || colIndex == 'balance' ? moneyFormat(value) : value }}
                                        </td>
                                        <td
                                            class="border-t-0 px-6 align-middle text-center border-l-0 border-r-0 text-green-500 text-xs whitespace-nowrap p-4"
                                        >
                                            <AppDropdown class="">
                                                <button >
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24"  viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>
                                                </button>
                                                <AppDropdownContent class="bg-white z-50 ">
                                                    <!-- <AppDropdownItem>
                                                        Edit Tenant
                                                    </AppDropdownItem> -->
                                                    <AppDropdownItem>
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
                                <div
                                    class="justify-between items-center block md:flex"
                                >
                                    <div
                                        class="flex items-center justify-center mb-6 md:mb-0"
                                    >
                                        <div
                                            class="flex items-center justify-start flex-wrap -mb-3"
                                        >
                                            <button
                                                class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-200 dark:bg-slate-700 hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                                type="button"
                                            >
                                                <!----><span class="px-2"
                                                    >1</span
                                                ></button
                                            ><button
                                                class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                                type="button"
                                            >
                                                <!----><span class="px-2"
                                                    >2</span
                                                ></button
                                            ><button
                                                class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                                type="button"
                                            >
                                                <!----><span class="px-2"
                                                    >3</span
                                                ></button
                                            ><button
                                                class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                                type="button"
                                            >
                                                <!----><span class="px-2"
                                                    >4</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <small>Page 1 of 4</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </AuthenticatedLayout>

</template>
