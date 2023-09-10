<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm } from '@inertiajs/vue3'
import { ref, reactive, watch, onMounted, computed } from 'vue';
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import axios from 'axios';
import { router } from '@inertiajs/vue3'
import DormList from '@/Components/DormList.vue';

export default {
    components: {
        AuthenticatedLayout,
        DormList
    },
    setup(){
        const page = usePage()
        const user = computed(() => page.props.auth.user);
        const dorms = ref([])

        onMounted(() => {
            dorms.value = page.props.dorms
        });

        return {
            user,
            dorms
        }
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="main w-full">
            <DormList :dorms.sync="dorms" :user.sync="user"/>
        </div>
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 100%;
        min-height: 92vh;
        background-color: #E5E8E8;
    }
</style>
