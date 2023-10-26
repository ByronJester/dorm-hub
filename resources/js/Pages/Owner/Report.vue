<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import IncomeReport from "@/Pages/Owner/Reports/IncomeReport.vue";
import OccupancyReport from "@/Pages/Owner/Reports/OccupancyReport.vue"
import { ref } from 'vue';

export default {
    components: {
        AuthenticatedLayout,
        IncomeReport,
        OccupancyReport
    },setup(){
        const activeReport = ref(''); // Initially, no report is active
        const activeReportComponent = ref(null);

        const changeActiveReport = (reportName) => {
        // Set the active report and its component based on the selected report name
            if (reportName === 'IncomeReport') {
                activeReport.value = 'IncomeReport';
                activeReportComponent.value = IncomeReport;
            } else if (reportName === 'OccupancyReport') {
                activeReport.value = 'OccupancyReport';
                activeReportComponent.value = OccupancyReport;
            }
            // You can add more conditions for other reports here
        }

        return{
            activeReport,
            activeReportComponent,
            changeActiveReport,
        };
},
};
</script>
<template>
    <AuthenticatedLayout>
        <div class="p-4 mt-16 lg:ml-64">
            
            <div class="max-w-[2520px] xl:px-20 md:px-10 sm:px-2 px-4">
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
                <h3 class="text-3xl font-bold">Reports</h3>
            </div>
            <hr class="my-5" />
            <main>

            <transition 
                name="custom-transition"  
                mode="out-in"
                enter-active-class="opacity duration-500 transform scale-100"
                leave-active-class="opacity duration-500 transform scale-90"
            >
                <component :is="activeReportComponent"></component>
            </transition>

            <div
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 3xl:grid-cols-5 items-center w-full gap-8 mb-40 mt-5"
                v-if="!activeReport"
            >
                <div
                    class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                >
                    <div class="w-full flex flex-row items-center gap-5 ">
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="bi bi-bookmark-star-fill"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                />
                            </svg>
                        </span>
                        <p class="text-xl">
                            Income Report
                        </p>
                    </div>
                    <hr class="my-3"/>
                    <div class="flex flex-col gap-1 text-gray-400 ml-3 ">
                        <p>-Generate income reports</p>
                        <p>-Customize income reports for specific time periods.</p>
                        <p>-Generate annual income statements for financial assessment</p>
                    </div>
                    <div class="w-full mt-3">
                        <button class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right"
                        @click="changeActiveReport('IncomeReport')"                        >
                            View
                        </button>
                    </div>
                </div>
                <div
                    class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                >
                    <div class="w-full flex flex-row items-center gap-5 ">
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="bi bi-bookmark-star-fill"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                />
                            </svg>
                        </span>
                        <p class="text-xl">
                            Occupancy Report
                        </p>
                    </div>
                    <hr class="my-3"/>
                    <div class="flex flex-col gap-1 text-gray-400 ml-3 ">
                        <p>-Review room occupancy and identify vacant rooms.</p>
                        <p>-Track tenant assignments and move-in/move-out dates.</p>
                        <p>-Generate occupancy reports for dormitory insights.</p>
                    </div>
                    <div class="w-full mt-3">
                        <button class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right">
                            View
                        </button>
                    </div>
                </div>
                <div
                    class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                >
                    <div class="w-full flex flex-row items-center gap-5 ">
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="bi bi-bookmark-star-fill"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                />
                            </svg>
                        </span>
                        <p class="text-xl">
                            Rent Roll Report
                        </p>
                    </div>
                    <hr class="my-3"/>
                    <div class="flex flex-col gap-1 text-gray-400 ml-3 ">
                        <p>-Generate Rent Roll Reports for income tracking.</p>
                        <p>-Analyze rental income by room and tenant.</p>
                        <p>-Streamline rent collection and record-keeping.</p>
                    </div>
                    <div class="w-full mt-3">
                        <button class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right">
                            View
                        </button>
                    </div>
                </div>
                <div
                    class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                >
                    <div class="w-full flex flex-row items-center gap-5 ">
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="bi bi-bookmark-star-fill"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                />
                            </svg>
                        </span>
                        <p class="text-xl">
                            Maintenance Report
                        </p>
                    </div>
                    <hr class="my-3"/>
                    <div class="flex flex-col gap-1 text-gray-400 ml-3 ">
                        <p>-Generate maintenance task lists.</p>
                        <p>-Monitor request status for efficiency.</p>
                        <p>-Record maintenance history for tracking.</p>
                    </div>
                    <div class="w-full mt-3">
                        <button class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right">
                            View
                        </button>
                    </div>
                </div>
                <div
                    class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                >
                    <div class="w-full flex flex-row items-center gap-5 ">
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="bi bi-bookmark-star-fill"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                />
                            </svg>
                        </span>
                        <p class="text-xl">
                            Tenants Report
                        </p>
                    </div>
                    <hr class="my-3"/>
                    <div class="flex flex-col gap-1 text-gray-400 ml-3 ">
                        <p>-Generate tenant information reports.</p>
                        <p>-Create lease and occupancy reports.</p>
                        <p>-Generate and update tenant records.</p>
                    </div>
                    <div class="w-full mt-3">
                        <button class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right">
                            View
                        </button>
                    </div>
                </div>
                <div
                    class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                >
                    <div class="w-full flex flex-row items-center gap-5 ">
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="bi bi-bookmark-star-fill"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                />
                            </svg>
                        </span>
                        <p class="text-xl">
                            Reservation Report
                        </p>
                    </div>
                    <hr class="my-3"/>
                    <div class="flex flex-col gap-1 text-gray-400 ml-3 ">
                        <p>-Monitor room reservations and availability.</p>
                        <p>-Efficiently manage reservation requests.</p>
                        <p>-Track reservation history for future planning.</p>
                    </div>
                    <div class="w-full mt-3">
                        <button class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right">
                            View
                        </button>
                    </div>
                </div>
                <div
                    class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                >
                    <div class="w-full flex flex-row items-center gap-5 ">
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="bi bi-bookmark-star-fill"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                />
                            </svg>
                        </span>
                        <p class="text-xl">
                            Dorm Report
                        </p>
                    </div>
                    <hr class="my-3"/>
                    <div class="flex flex-col gap-1 text-gray-400 ml-3 ">
                        <p>-Generate dorm performance reports.</p>
                        <p>-Assess dorms' income and occupancy.</p>
                        <p>-Plan and optimize dormitory operations</p>
                    </div>
                    <div class="w-full mt-3">
                        <button class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right">
                            View
                        </button>
                    </div>
                </div>
            </div>
            </main>
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