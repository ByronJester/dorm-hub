<!-- <script>

</script> -->

<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import 'vue-good-table-next/dist/vue-good-table-next.css'
    import { VueGoodTable } from 'vue-good-table-next';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';

    export default {
        components: {
            AuthenticatedLayout,
            VueGoodTable
        },
        setup() {
            const page = usePage()
            const rows = ref([])
            const columns = ref([
                {
                    label: 'Property Name',
                    field: 'property_name',
                },
                {
                    label: 'Address',
                    field: 'detailed_address',
                },
                {
                    label: 'Floor Total',
                    field: 'floor_total',
                },

                {
                    label: 'Room Total',
                    field: 'room_total',
                },

                {
                    label: 'ID',
                    field: 'id',
                },
            ])

            onMounted(() => {
                console.log(page.props.dorms);

                rows.value = page.props.dorms

            });

            return {
                columns,
                rows
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="main">
            <vue-good-table
                    styleClass="vgt-table condensed"
                    style="padding: 0vw 2vw; width: 96%"
                    :columns="columns"
                    :rows="rows"
                    :pagination-options="{ enabled: true }"
                    :select-options="{ enabled: true }"
                    :search-options="{ enabled: true }"
            />
        </div>
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 92vh;
    }
</style>
