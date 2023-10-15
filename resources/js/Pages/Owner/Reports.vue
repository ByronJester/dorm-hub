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
            return {

            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="main">
            Reports
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
