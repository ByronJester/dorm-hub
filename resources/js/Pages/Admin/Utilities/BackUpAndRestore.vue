<script>
import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import  fileDownload from "js-file-download";
import {usePage} from "@inertiajs/vue3";

export default {
        components: {
            AuthenticatedLayout,
            AppDropdown,
            AppDropdownContent,
            AppDropdownItem,
        },
        setup(){
            const numoptions=["5", "10", "10"];
            const headers=["BackUp-ID", "Description", "Back-Up Date"];
            const data=[{"BakcUp-ID":"001", "Description":"System Maintennace", "Bsack-Up Date":"2023-10-11"}];

            return{
                numoptions,
                headers,
                data
            }
        },
    mounted() {
        const page = usePage();

        console.log(page.props)
    },
    methods: {
        async backup() {

            swal({
                title: 'Creating database backup',
                text: 'Please wait and do not change/reload page while database backup is ongoing.',
                allowEscapeKey: false,
                allowOutsideClick: false,
                showConfirmButton: false,
                type: "info",
            })
            axios.post(route('admin.backup.execute')).then((response) => {
                // swal.close()
                if (response.status === 200) {
                    swal("Success", "Database backup created", "success")
                    fileDownload(response.data, "db-backup.sql");
                } else {

                }
            }).catch((err) => {
                const { response } = err;
                if (response.status === 500) {
                    swal("Error: 500", "Failed to create database backup", "error")
                }
            })
        },
    }
}
</script>
<template>
    <AuthenticatedLayout>
    <div class="px-4 pt-20 lg:ml-64">
        <div class="w-full">
                <div class="flex items-center justify-start">
                        <h3 class="text-3xl">Back-Up</h3>
                    </div>

                <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />
                <div class="bg-white p-5 rounded-md shadow">
                    <button class="py-2 px-3 bg-orange-400 text-white rounded" v-on:click="backup">
                        Start Back-Up
                    </button>
                </div>
        </div>
        <div class="w-full mb-5 mt-5">
                    <div
                        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                    >
                        <div class="rounded-t mb-0 px-4 py-3 border-0">
                            <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full  sm:flex-row sm:justify-between sm:items-center gap-5 file:px-4 max-w-full flex-col flex "
                                >
                                <div class="mb-3 sm:flex-row flex-col flex gap-3">
                                    <div class="flex flex-row items-center gap-2">
                                        <p class="text-sm font-bold">Show</p>
                                        <select
                                            id="subject"
                                            class="block w-16 px-5 py-1 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        >
                                            <option v-for="option in numoptions" :key="option">
                                                {{ option }}
                                            </option>
                                        </select>
                                        <p class="text-sm font-bold">entries</p>
                                    </div>
                                    <div class="flex flex-row gap-2">
                                    <button class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white">
                                        Copy
                                    </button>
                                    <button class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white">
                                        Excel
                                    </button>
                                    <button class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white">
                                        PDF
                                    </button>
                                    <button class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white">
                                        Print
                                    </button>
                                    </div>
                                </div>
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
                                            v-for="header in headers"
                                            :key="header"
                                        >
                                            {{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, rowIndex) in data"
                                        :key="rowIndex"
                                    >
                                        <td
                                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                            v-for="(value, colIndex) in item"
                                            :key="colIndex"
                                        >
                                            {{ value }}
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

    </AuthenticatedLayout>

</template>
