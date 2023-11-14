<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import { ref } from 'vue'
export default{
    components:{
        AuthenticatedLayout,
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
    },
    setup(){
        const activeTable = ref('complain');
        const setActiveTable = (table)=>{
            activeTable.value = table;
        }
        //mga sample data lang to
        const options=["M.D.R Apartment", "Dorm2"]
        const headerComplaints=["Complain Subject", "Message", "Status", "Image", "Complain Date", "Date Finish"]
        const headerRefunds=["Refund Subject", "Refund Description", "Status", "Refund Date", "Bank/Wallet Name", "Account number", "Account Name", "Date Refunded"]
        const headerMoveOut=["Move-out Subject", "Move-out Description", "Status", "Move-out Date", "Date moved-out"]
       
            return{
                activeTable,
                setActiveTable,
                options,
                headerComplaints,
                headerRefunds,
                headerMoveOut,
            }
    }
}
</script>
<template>
    <AuthenticatedLayout>
    <div class="p-4 mt-16 lg:ml-64">
        <div class="flex items-center justify-start">
                        <span class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 640 512"><path d="M335.5 4l288 160c15.4 8.6 21 28.1 12.4 43.5s-28.1 21-43.5 12.4L320 68.6 47.5 220c-15.4 8.6-34.9 3-43.5-12.4s-3-34.9 12.4-43.5L304.5 4c9.7-5.4 21.4-5.4 31.1 0zM320 160a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM144 256a40 40 0 1 1 0 80 40 40 0 1 1 0-80zm312 40a40 40 0 1 1 80 0 40 40 0 1 1 -80 0zM226.9 491.4L200 441.5V480c0 17.7-14.3 32-32 32H120c-17.7 0-32-14.3-32-32V441.5L61.1 491.4c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l37.9-70.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c16.3 0 31.9 4.5 45.4 12.6l33.6-62.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c32.4 0 62.1 17.8 77.5 46.3l33.6 62.3c13.5-8.1 29.1-12.6 45.4-12.6h19.5c32.4 0 62.1 17.8 77.5 46.3l37.9 70.3c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8L552 441.5V480c0 17.7-14.3 32-32 32H472c-17.7 0-32-14.3-32-32V441.5l-26.9 49.9c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l36.3-67.5c-1.7-1.7-3.2-3.6-4.3-5.8L376 345.5V400c0 17.7-14.3 32-32 32H296c-17.7 0-32-14.3-32-32V345.5l-26.9 49.9c-1.2 2.2-2.6 4.1-4.3 5.8l36.3 67.5c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8z"/></svg>                </span>
                        <h3 class="text-3xl">Manage Requests</h3>
                    </div>
                
                <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />
                <div>
                        <p class="text-sm font-bold">Dorm:</p>
                        <select
                            id="subject"
                            class="block w-56 px-4 py-1.5 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option v-for="option in options" :key="option">
                                {{ option }}
                            </option>
                        </select>
                </div>
                <div class="flex flex-row mt-5">
                    <button 
                    @click="setActiveTable('complain')"
                    :class="{ 'bg-orange-400 text-white': activeTable === 'complain' }"
                    class="py-2.5 px-5 font-semibold text-md border-x">
                        Complain
                    </button>
                    <button 
                    @click="setActiveTable('refund')"
                    :class="{ 'bg-orange-400 text-white': activeTable === 'refund' }"
                    class="py-2.5 px-5 font-semibold text-md border-x">
                        Refund
                    </button>
                    <button 
                    @click="setActiveTable('move')"
                    :class="{ 'bg-orange-400 text-white': activeTable === 'move' }"
                    class="py-2.5 px-5 font-semibold text-md border-x">
                        Move out
                    </button>
                </div>
                <div v-if="activeTable === 'complain'">
                    <div
                        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                    >
                        <div class="rounded-t mb-0 px-4 py-3 border-0">
                            <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                                >
                                <p class="text-xl mb-5 font-bold">Complaints Records</p>
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
                                                class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    PDF
                                                </button>
                                                <button 
                                                class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    Excel
                                                </button>
                                                <button class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
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
                                            v-for="header in headerComplaints"
                                            :key="header"
                                        >
                                            {{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, rowIndex) in slicedRows"
                                        :key="rowIndex"
                                    >
                                        <td
                                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                            v-for="(value, colIndex) in item"
                                            :key="colIndex"
                                        >
                                  
                                            {{ colIndex !== 'created_at' ? value : '' }}
                                        
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
            
           

           
                
            
                <div v-if="activeTable === 'refund'">
                    <div
                        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                    >
                        <div class="rounded-t mb-0 px-4 py-3 border-0">
                            <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                                >
                                <p class="text-xl mb-5 font-bold">Refund Records</p>
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
                                                class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    PDF
                                                </button>
                                                <button 
                                                class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    Excel
                                                </button>
                                                <button class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
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
                                        v-for="(item, rowIndex) in slicedRows"
                                        :key="rowIndex"
                                    >
                                        <td
                                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                            v-for="(value, colIndex) in item"
                                            :key="colIndex"
                                        >
                                  
                                            {{ colIndex !== 'created_at' ? value : '' }}
                                        
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
            
            

               
                <div v-if="activeTable === 'move'">
                    <div
                        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                    >
                        <div class="rounded-t mb-0 px-4 py-3 border-0">
                            <div class="flex flex-wrap items-center">
                                <div
                                    class="relative w-full gap-5 file:px-4 max-w-full flex-grow flex-1"
                                >
                                <p class="text-xl mb-5 font-bold">Move out Records</p>
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
                                                class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    PDF
                                                </button>
                                                <button 
                                                class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
                                                    Excel
                                                </button>
                                                <button class="py-2.5 rounded-lg bg-orange-400 text-white px-4">
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
                                            v-for="header in headerMoveOut"
                                            :key="header"
                                        >
                                            {{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, rowIndex) in slicedRows"
                                        :key="rowIndex"
                                    >
                                        <td
                                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                            v-for="(value, colIndex) in item"
                                            :key="colIndex"
                                        >
                                  
                                            {{ colIndex !== 'created_at' ? value : '' }}
                                        
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
    </AuthenticatedLayout>
</template>