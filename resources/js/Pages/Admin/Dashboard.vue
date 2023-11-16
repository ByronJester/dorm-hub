<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import DormitoriesReports from "@/Pages/Admin/Reports/DormitoriesReport.vue";
import IncomeReport from "@/Pages/Admin/Reports/IncomeReports.vue";
import UsersReport from "@/Pages/Admin/Reports/UsersReport.vue";
import { usePage, router } from "@inertiajs/vue3";

export default{
    components:{
        AuthenticatedLayout
    },
    setup() {
        const props = usePage().props

        return {
            props
        }
    }
}


</script>
<template>
<AuthenticatedLayout>
    <div class="p-4 mt-16 lg:ml-64">


            <div
                class="grid grid-cols-2 lg:grid-cols-4 sm:grid-cols-2 gap-4 mb-4 mt-4 text-gray-400 dark:text-white"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{props.users}}
                        </p>
                        <p class="text-xs">
                            TOTAL No. of Users
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{props.dorms}}
                        </p>
                        <p class="text-xs">
                            TOTAL No. of Registered Dorms
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{props.tenants}}
                        </p>
                        <p class="text-xs">TOTAL No. of Tenants</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{props.owners}}
                        </p>
                        <p class="text-xs">TOTAL No. of Dorm owners</p>
                    </div>
                </div>
            </div>
        </div>
</AuthenticatedLayout>
</template>
