<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm, router } from '@inertiajs/vue3'
import { ref, reactive, watch, onMounted, computed } from 'vue';
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import axios from 'axios';

export default {
    components: {
        AuthenticatedLayout,
    },
    setup(){
        const page = usePage()
        const user = computed(() => page.props.auth.user);
        const isMobileView = ref(false)
        isMobileView.value = screen.width < 600;
        const amount = ref()
        const owner = ref()
        const source = ref()

        onMounted(() => {
            amount.value = page.props.amount
            owner.value = page.props.owner
            source.value = page.props.source
        });

        return {
            user,
            isMobileView,
            amount,
            owner,
            source
        }
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="main w-full">
            <div class="w-full flex justify-center items-center">
                <div style="height: 100%; border: 1px solid #E4B934; border-radius: 10px" class="mt-32 flex flex-col" :style="{'width': isMobileView ? '90%': '30%'}">
                    <div class="w-full text-center mt-5" style="font-size: 40px">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>

                    <div class="w-full text-center mt-5" style="font-size: 23px" v-if="owner">
                        {{ owner.name }}
                    </div>

                    <div class="w-full text-center mt-1" style="font-size: 12px" v-if="owner">
                        {{ owner.phone_number }}
                    </div>

                    <div class="w-full text-center mt-7" style="font-size: 20px">
                        ₱ {{ parseFloat(amount).toFixed(2) }}
                    </div>

                    <div class="w-full text-center mt-5" style="font-size: 15px">
                        GCash Payment
                    </div>


                    <div class="w-full flex flex-row mt-4 px-3" style="font-size: 15px" >
                        <div class="w-full">
                            <span>
                                <!-- {{ billing.billing.name }} -->

                            </span>
                        </div>

                        <div class="w-full">
                            <span class="float-right">
                                <!-- {{ billing.billing.phone }} -->
                            </span>
                        </div>
                    </div>
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
</style>
