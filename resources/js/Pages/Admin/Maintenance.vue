<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import { usePage, router } from "@inertiajs/vue3";
import { ref} from 'vue';

export default {
    components: {
        AuthenticatedLayout,
    },setup(){
        const page = usePage();
        const user = page.props.auth.user;
        const options = ["Jear dorm", "Dorm 2"];
        const numoptions = ["5", "10", "15", "20"];
        const header=["Page Name", "Description"]
        const data = [
            ["About us", "About us"],
            ["Contact us", "Contact us"],
            ["Privacy policy", "Privacy Policy"],
            ["Terms & Condition Tenants", "Terms & Condition Tenants"],
            ["Terms & Condition Owner", "Terms & Condition Owner"],
            
            

        ];
        

        return{
            options,
            numoptions,
            header,
            data,
            user
        };
},
};
</script>
<template>
    <AuthenticatedLayout>
        <div class="p-4 mt-16 lg:ml-64">
            
            <div class="">
            <div class="flex items-center gap-2 justify-start">
                <svg
                    class="w-10 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                    viewBox="0 0 384 512"
                    fill="currentColor"
                >
                    <path
                        d="M0 64C0 28.7 28.7 0 64 0H224V128c0 17.7 14.3 32 32 32H384V288H216c-13.3 0-24 10.7-24 24s10.7 24 24 24H384V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zM384 336V288H494.1l-39-39c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l80 80c9.4 9.4 9.4 24.6 0 33.9l-80 80c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l39-39H384zm0-208H256V0L384 128z"
                    />
                </svg>
                <h3 class="text-3xl font-bold">Maintenance</h3>
            </div>
            <hr class="my-5" />
            
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
                                    <div class="w-full"> <form class="flex items-center">
                                        
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
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table
                                class="items-center w-full bg-transparent border-collapse"
                            >
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                            v-for="header in header"
                                            :key="header"
                                        >
                                            {{ header }}
                                        </th>
                                        <th
                                        class="px-6 border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"

                                        >
                                            Command
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
                                        <td 
                                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                        >
                                            <div class="flex flex-row items-center gap-2">
                                                <span>
                                                    <i class="fa-solid fa-pen-to-square" style="color: #03a800;"></i>
                                                </span>
                                                <span>
                                                    <i class="fa-solid fa-trash" style="color: #cc0000;"></i>
                                                </span>
                                            </div>
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
