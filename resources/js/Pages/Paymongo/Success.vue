<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm, router } from '@inertiajs/vue3'
import { ref, reactive, watch, onMounted, computed } from 'vue';
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import 'mapbox-gl/dist/mapbox-gl.css';
import axios from 'axios';
import html2canvas from 'html2canvas';

export default {
    components: {
        AuthenticatedLayout,
        ApplicationLogo
    },
    setup(){
        const page = usePage()
        const user = computed(() => page.props.auth.user);
        const isMobileView = ref(false)
        isMobileView.value = screen.width < 600;
        const amount = ref()
        const owner = ref()
        const source = ref()
        const captureDiv = ref(null);

        const captureAsImage = () => {
        if (captureDiv.value) {
            html2canvas(captureDiv.value).then(canvas => {
            const imageBase64 = canvas.toDataURL('image/png');

            // Create a link to download the image
            const link = document.createElement('a');
            link.href = imageBase64;
            link.download = 'receipt.png';
            link.click();
            });
        }
        }

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
            source,
            captureDiv,
            captureAsImage
        }
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="bg-white border rounded-lg mt-20 mb-10 shadow-lg px-6 py-8 max-w-md mx-auto" ref="captureDiv">
            <h1 class="font-bold text-2xl my-4 text-center text-blue-600"><ApplicationLogo/></h1>
            <hr class="mb-2">
            <div class="flex justify-between mb-6">
                <h1 class="text-lg font-bold">Receipt</h1>
                <div class="text-gray-700">
                    <div>Date: {{ new Date().toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) }}</div>
                    <div>Invoice #: <span></span></div>
                </div>
            </div>
            <div class="mb-8">
                <h2 class="text-lg font-bold mb-4">Paid by:</h2>
                <div class="text-gray-700 mb-2" v-if="owner">Tenant name</div>
                <div class="text-gray-700 mb-2">Method</div>
                <div class="text-gray-700">contact no.</div>
            </div>
            <table class="w-full mb-8">
                <thead>
                    <tr>
                        <th class="text-left font-bold text-gray-700">Description</th>
                        <th class="text-right font-bold text-gray-700">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left text-gray-700">Monthly Rent</td>
                        <td class="text-right text-gray-700">₱ {{ parseFloat(amount).toFixed(2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-left font-bold text-gray-700">Total</td>
                        <td class="text-right font-bold text-gray-700">P1,200.00</td>
                    </tr>
                </tfoot>
            </table>
            <hr class="my-1"/>
            <div class="text-gray-700 mb-2 mt-10 text-center">Thank you for your business!</div>
            
        </div>
        <div class="flex item-center justify-center w-full mb-10">
                <button @click="captureAsImage()" class="bg-orange-400 py-2 px-4 rounded-lg text-white font-bold text-md">
                    Download Receipt
                </button>
        </div>
        <!--
        <div class="main w-full">
            <div class="w-full flex justify-center items-center">
                <div style="height: 100%; border: 1px solid #E4B934; border-radius: 10px" class="mt-32 flex flex-col" :style="{'width': isMobileView ? '90%': '30%'}">
                    <div class="w-full text-center mt-5" style="font-size: 40px">
                        <i class="fa-solid fa-circle-check"></i>
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
                                {{ billing.billing.name }} 

                            </span>
                        </div>

                        <div class="w-full">
                            <span class="float-right">
                               {{ billing.billing.phone }} 
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 100%;
        min-height: 92vh;
        background-color: #E5E8E8;
    }
</style>
