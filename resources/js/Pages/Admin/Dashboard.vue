<script>
import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';

export default{
    components:{
        AuthenticatedLayout
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
                            27
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
                             3
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
                            4
                        </p>
                        <p class="text-xs">TOTAL No. of Tenants</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            10
                        </p>
                        <p class="text-xs">TOTAL No. of Dorm owners</p>
                    </div>
                </div>
            </div>
        </div>
</AuthenticatedLayout>
</template>