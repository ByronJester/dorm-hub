<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage, useForm, router, Link } from "@inertiajs/vue3";
import { ref, reactive, watch, onMounted, computed } from "vue";
import { MapboxMap, MapboxMarker } from "@studiometa/vue-mapbox-gl";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import "mapbox-gl/dist/mapbox-gl.css";
import axios from "axios";
import html2canvas from "html2canvas";
import Image from "primevue/image";

export default {
    components: {
        AuthenticatedLayout,
        ApplicationLogo,
        Image,
        Link
    },
    setup() {
        const page = usePage();
        const user = computed(() => page.props.auth.user);
        const amount = ref();
        const owner = ref();
        const source = ref();
        const captureDiv = ref(null);
        const tenant = ref(null);
        
        const description = ref(null);
        const invoice = page.props.invoice;
        const billing = page.props.billing;

        const captureAsImage = () => {
            if (captureDiv.value) {
                html2canvas(captureDiv.value).then((canvas) => {
                    const imageBase64 = canvas.toDataURL("image/png");

                    // Create a link to download the image
                    const link = document.createElement("a");
                    link.href = imageBase64;
                    link.download = "receipt.png";
                    link.click();
                });
            }
        };

        console.log(billing);

        onMounted(() => {
            tenant.value = billing.profile.first_name + ' ' +billing.profile.last_name;
            amount.value = invoice.amount;
            description.value = billing.description;

        });

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        return {
            user,
            amount,
            owner,
            source,
            tenant,
            invoice,
            description,
            captureDiv,
            captureAsImage,
            moneyFormat
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="mt-20 mb-10 rid-rows mx-5">
            <div>
                <div
                    class="bg-white border rounded-lg shadow-lg px-6 py-8 max-w-md mx-auto"
                    ref="captureDiv"
                >
                    <h1
                        class="font-bold text-2xl my-4 text-center text-blue-600"
                    >
                        <ApplicationLogo />
                    </h1>
                    <hr class="mb-2" />
                    <div class="flex justify-between mb-6">
                        <h1 class="text-lg font-bold">Receipt</h1>
                        <div class="text-gray-700">
                            <div>
                                Date:
                                {{
                                    new Date().toLocaleString("en-US", {
                                        year: "numeric",
                                        month: "short",
                                        day: "numeric",
                                    })
                                }}
                            </div>
                            <div>
                                Invoice #: {{ invoice.reference_id
                                }}<span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                Method #: <span> 
                                    <img v-if="invoice.channel_code == 'PH_GCASH'" src="/images/gcashlogo.png" class="w-20"/>
                                    <img v-if="invoice.channel_code == 'PH_GRABPAY'" src="/images/grablogo.png" class="w-10"/>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-8">
                        <h2 class="text-lg font-bold mb-4">Paid by:</h2>
                        <div class="text-gray-700 mb-2">
                            {{ tenant }}
                        </div>
                    </div>
                    <table class="w-full mb-8">
                        <thead>
                            <tr>
                                <th class="text-left font-bold text-gray-700">
                                    Description
                                </th>
                                <th class="text-right font-bold text-gray-700">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left text-gray-700">
                                    {{ description }}
                                </td>
                                <td class="text-right text-gray-700">
                                    ₱
                                    {{ parseFloat(amount).toFixed(2) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-left font-bold text-gray-700">
                                    Total
                                </td>
                                <td class="text-right font-bold text-gray-700">
                                    ₱
                                    {{ parseFloat(amount).toFixed(2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <hr class="my-1" />
                    <div class="text-gray-700 mb-2 mt-10 text-center">
                        Thank you for your business!
                    </div>
                    <div class="text-gray-700 mb-2 mt-3 text-center">
                        <p
                            class="flex rounded-lg text-neutral-700 items-center justify-center px-3 py-2 border border-blue-500"
                        >
                            Powered by
                            <img src="/images/xenditlogo.png" class="w-16" />
                        </p>
                    </div>
                </div>
                <div class="flex item-center mt-5 justify-center w-full mb-10 gap-5">
                    <button
                        @click="captureAsImage()"
                        class="bg-orange-400 py-2 px-4 rounded-lg text-white font-bold text-md"
                    >
                        Download Receipt
                    </button>
                    <Link
                        v-if="description=='Reservation Fee'"
                        :href="route('tenant.reservationlist')"
                        class="bg-orange-400 py-2 px-4 rounded-lg text-white font-bold text-md"
                    >
                        Go To My Reservation
                </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
