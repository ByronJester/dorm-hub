<script>
    import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { VueGoodTable } from 'vue-good-table-next';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';

    export default {
        components: {
            AuthenticatedLayout,
        },
        setup() {
            const activeTab = ref('room')

            const changeActiveTab = (tab) => {
                activeTab.value = tab
            }

            return {
                activeTab,
                changeActiveTab

            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="main">
            <div class="p-5 md:p-10 w-full">
                <div class="w-full">
                    <div class="w-full flex justify-center items-center md:justify-start md:items-start">
                        <button @click="changeActiveTab('room')" :class="{'bg-gray-400': activeTab == 'room', 'bg-gray-300': activeTab != 'room'}"
                            class="py-3 md:py-5 px-2 md:px-5 text-xs md:text-sm"
                        >
                            Rooms Report
                        </button>

                        <button @click="changeActiveTab('tenant')" :class="{'bg-gray-400': activeTab == 'tenant', 'bg-gray-300': activeTab != 'tenant'}"
                            class="py-3 md:py-5 px-2 md:px-5 text-xs md:text-sm"
                        >
                            Tenants Reports
                        </button>

                        <button @click="changeActiveTab('dorm')" :class="{'bg-gray-400': activeTab == 'dorm', 'bg-gray-300': activeTab != 'dorm'}"
                            class="py-3 md:py-5 px-2 md:px-5 text-xs md:text-sm"
                        >
                            Dormitories Reports
                        </button>
                    </div>
                </div>

                <div class="w-full mt-10 h-full">
                    asd
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 100%;
        min-height: 92vh;
        background-color: #E5E8E8;
    }

    ::-webkit-scrollbar {
        width: 0px;
        background: transparent; /* make scrollbar transparent */
    }


</style>
