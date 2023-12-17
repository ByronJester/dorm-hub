<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import IncomeReport from "@/Pages/Owner/Reports/IncomeReport.vue";
import OccupancyReport from "@/Pages/Owner/Reports/OccupancyReport.vue"
import RentRollReport from "@/Pages/Owner/Reports/RentRollReport.vue"
import RequestReport from "@/Pages/Owner/Reports/RequestReport.vue"
import ReservationReport from "@/Pages/Owner/Reports/ReservationReport.vue"
import TenantReport from "@/Pages/Owner/Reports/TenantReport.vue"
import DormReport from "@/Pages/Owner/Reports/DormReport.vue"
import { ref} from 'vue';
import { usePage, router } from "@inertiajs/vue3";

export default {
    components: {
        AuthenticatedLayout,
        IncomeReport,
        OccupancyReport,
        RentRollReport,
        RequestReport,
        ReservationReport,
        TenantReport,
        DormReport
    },setup(){
        const activeReport = ref('IncomeReport'); // Initially, no report is active
        const activeReportComponent = ref(IncomeReport);

        const changeActiveReport = (reportName) => {
        // Set the active report and its component based on the selected report name
            if (reportName === 'IncomeReport') {
                activeReport.value = 'IncomeReport';
                activeReportComponent.value = IncomeReport;
            } else if (reportName === 'OccupancyReport') {
                activeReport.value = 'OccupancyReport';
                activeReportComponent.value = OccupancyReport;
            }
            else if (reportName === 'RentRollReport') {
                activeReport.value = 'RentRollReport';
                activeReportComponent.value = RentRollReport;
            }
            else if (reportName === 'RequestReport') {
                activeReport.value = 'RequestReport';
                activeReportComponent.value = RequestReport;
            }
            else if (reportName === 'ReservationReport') {
                activeReport.value = 'ReservationReport';
                activeReportComponent.value = ReservationReport;
            }
            else if (reportName === 'TenantReport') {
                activeReport.value = 'TenantReport';
                activeReportComponent.value = TenantReport;
            }
            else if (reportName === 'DormReport') {
                activeReport.value = 'DormReport';
                activeReportComponent.value = DormReport;
            }
            // You can add more conditions for other reports here
        };

        const isDropDownOpen = ref(true);

        //dropdown function
        const toggleDropDown = () => {
            isDropDownOpen.value = !isDropDownOpen.value;
        };

        return{
            activeReport,
            activeReportComponent,
            changeActiveReport,
            toggleDropDown,
            isDropDownOpen
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
                    <h3 class="text-3xl font-bold">Generate Reports</h3>
                </div>
                <hr class="my-5" />
                <div class="w-full grid grid-cols-1 md:grid-cols-6">
                    <div class="pr-4 md:border-r w-full col-span-2 md:col-span-1 flex flex-col">
                        <div
                            class="flex flex-row gap-2 py-2 px-3 mb-2  cursor-pointer bg-gray-500 rounded-md text-white items-center justify-between"
                            @click="toggleDropDown()"
                        >
                            <p class="font-bold">Reports</p>
                            <i
                                class="block fa-solid fa-caret-left cursor-pointer"
                                :class="{ hidden: isDropDownOpen }"
                            ></i>
                            <i class="fa-solid fa-caret-down block" :class="{ hidden: !isDropDownOpen }"></i>
                        </div>

                        <div class="block mx-5" :class="{ hidden: !isDropDownOpen }">
                            <div
                                class="w-full my-1 p-2 rounded-md hover:bg-orange-400 hover:text-white group"
                                :class="{
                                    
                                'text-white bg-gradient-to-r from-orange-400 to-red-600':
                                  
                        
                                        activeReport == 'IncomeReport',
                                }"
                            >
                                <span
                                    class="cursor-pointer"
                                    @click="changeActiveReport('IncomeReport')"
                                >
                                    Income Report
                                </span>
                            </div>

                            <div
                                class="w-full my-1 p-2 rounded-md hover:bg-orange-400 hover:text-white group"
                                :class="{
                                    'text-white bg-gradient-to-r from-orange-400 to-red-600':
                                        activeReport == 'OccupancyReport',
                                }"
                            >
                                <span
                                    class="cursor-pointer "
                                    @click="
                                        changeActiveReport('OccupancyReport')
                                    "
                                >
                                    Occupancy Report
                                </span>
                            </div>

                            <div
                                class="w-full my-1 p-2 rounded-md hover:bg-orange-400 hover:text-white group"
                                :class="{
                                    'text-white bg-gradient-to-r from-orange-400 to-red-600':
                                        activeReport == 'TenantReport',
                                }"
                            >
                                <span
                                    class="cursor-pointer "
                                    @click="
                                        changeActiveReport('TenantReport')
                                    "
                                >
                                    Tenant Report
                                </span>
                            </div>

                            <div
                                class="w-full my-1 p-2 rounded-md hover:bg-orange-400 hover:text-white group"
                                :class="{
                                    'text-white bg-gradient-to-r from-orange-400 to-red-600':
                                        activeReport == 'ReservationReport',
                                }"
                            >
                                <span
                                    class="cursor-pointer"
                                    @click="changeActiveReport('ReservationReport')"
                                >
                                  Reservation Report
                                </span>
                            </div>

                            <div
                                class="w-full my-1 p-2 rounded-md hover:bg-orange-400 hover:text-white group"
                                :class="{
                                    'text-white bg-gradient-to-r from-orange-400 to-red-600':
                                        activeReport == 'DormReport',
                                }"
                            >
                                <span
                                    class="cursor-pointer "
                                    @click="
                                        changeActiveReport('DormReport')
                                    "
                                >
                                     Dorm Report
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-4 col-span-5">
                        <div class="md:pl-5">
                            <component :is="activeReportComponent"></component>
                        </div>
                    </div>
                </div>
                </div>
                </div>
    </AuthenticatedLayout>
</template>

<style>
.custom-transition-enter-active, .custom-transition-leave-active {
  transition: opacity 0.5s, transform 0.5s;
}
.custom-transition-enter, .custom-transition-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>
