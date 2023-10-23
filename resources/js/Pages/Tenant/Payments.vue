<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage, useForm, router } from "@inertiajs/vue3";
import { ref, reactive, watch, onMounted, computed } from "vue";
import { MapboxMap, MapboxMarker } from "@studiometa/vue-mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import axios from "axios";

export default {
    components: {
        AuthenticatedLayout,
    },
    setup() {
        const page = usePage();
        const user = computed(() => page.props.auth.user);
        const payments = ref([]);
        const application = ref({});
        const owner = ref({});
        const methods = ref([]);
        const isMobileView = ref(false);
        isMobileView.value = screen.width < 600;
        const receipt = ref(null);
        const payment_id = ref();
        const imageError = ref(null);

        onMounted(() => {
            payments.value = page.props.payments;
            application.value = page.props.application;
            owner.value = page.props.owner;
            methods.value = page.props.methods;
        });

        const pay = (id, method) => {
            if (method == "GCash Payment") {
                swal(
                    {
                        title: `Are you sure to pay rent?`,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        closeOnConfirm: false,
                    },
                    function () {
                        axios
                            .post(route("pay.rent", id), { method: method })
                            .then((response) => {
                                window.location.href =
                                    response.data.redirect.checkout_url;
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                    }
                );
            }

            if (method == "Bank Payment") {
                if (receipt.value == null) {
                    imageError.value = "Bank transfer receipt is required";

                    return;
                }

                imageError.value = null;

                swal(
                    {
                        title: `Are you sure to upload this receipt?`,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        closeOnConfirm: false,
                    },
                    function () {
                        axios
                            .post(route("pay.rent", id), {
                                method: method,
                                receipt: receipt.value,
                            })
                            .then((response) => {
                                swal(
                                    "Success!",
                                    `Please wait for the dorm owner to verify you receipt.`,
                                    "success"
                                );

                                setTimeout(function () {
                                    location.reload();
                                }, 1500);
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                    }
                );
            }
        };

        const openModal = (arg) => {
            payment_id.value = arg;

            var modal = document.getElementById("bankModal");

            modal.style.display = "block";
        };

        const closeModal = () => {
            var modal = document.getElementById("bankModal");

            modal.style.display = "none";
        };

        const imageClick = () => {
            document.getElementById("bank_receipt").click();
        };

        const imageChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                receipt.value = e.target.result;
            };
        };

        return {
            user,
            payments,
            application,
            owner,
            methods,
            isMobileView,
            receipt,
            payment_id,
            imageError,
            pay,
            openModal,
            closeModal,
            imageClick,
            imageChange,
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <div
            class="max-w-[2520px] mt-16 mx-auto xl:px-20 md:px-10 sm:px-2 px-4"
        >
            <div
                className="
                        max-w-screen-lg 
                        mx-auto
                        "
            >
                <section class="pt-6 mb-6 flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <span
                            class="inline-flex justify-center items-center w-6 h-6 mr-2"
                            ><svg
                                viewBox="0 0 24 24"
                                width="20"
                                height="20"
                                class="inline-block"
                            >
                                <path
                                    fill="currentColor"
                                    d="M7 12C9.2 12 11 10.2 11 8S9.2 4 7 4 3 5.8 3 8 4.8 12 7 12M11 20V14.7C9.9 14.3 8.5 14 7 14C3.1 14 0 15.8 0 18V20H11M22 4H15C13.9 4 13 4.9 13 6V18C13 19.1 13.9 20 15 20H22C23.1 20 24 19.1 24 18V6C24 4.9 23.1 4 22 4M18 18H16V6H18V18Z"
                                ></path></svg></span>
                        <h1 class="text-3xl leading-tight">Billing overview</h1>
                    </div>
                </section>
                <div class="grid grid-cols-12 gap-6 mb-6">
                    <!--Eto yung amount para sa upcoming payment-->
                    <div class="col-span-12 shadow-lg sm:col-span-6 xl:col-span-3">
                        <div
                            class="flex-col dark:bg-slate-900/70 bg-white flex"
                        >
                            <div class="flex-1 p-6">
                                
                                <div class="justify-between items-center flex">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <div>
                                            <h3
                                                class="text-lg leading-tight text-green-500 dark:text-slate-400"
                                            >
                                                Upcoming Payment
                                            </h3>
                                            <!--Dito kasama na din yung balance pero pag nabayaran ni tenant ahead yung balance yung bill lang every month-->
                                            <h1
                                                class="text-3xl leading-tight font-semibold"
                                            >
                                                <div>P6000.00</div>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!---->
                        </div>
                    </div>
                    <!--Dito lalabas yung balance ni user-->
                    <div class="col-span-12 shadow-lg sm:col-span-6 xl:col-span-3">
                        <div
                            class="flex-col dark:bg-slate-900/70 bg-white flex"
                        >
                            <div class="flex-1 p-6">
                                
                                <div class="justify-between items-center flex">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <div>
                                            <h3
                                                class="text-lg leading-tight text-red-500 dark:text-slate-400"
                                            >
                                                Balance
                                            </h3>
                                            <h1
                                                class="text-3xl leading-tight font-semibold"
                                            >
                                                <div>P3000.00</div>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!---->
                        </div>
                    </div>
                    <!--Dito yung total na nabayad na ni user-->
                    <div class="col-span-12 rounded-2xl shadow-lg sm:col-span-6 xl:col-span-3">
                        <div
                            class="flex-col dark:bg-slate-900/70 bg-white flex"
                        >
                            <div class="flex-1 p-6">
                                
                                <div class="justify-between items-center flex">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <div>
                                            <h3
                                                class="text-lg leading-tight text-orange-500 dark:text-slate-400"
                                            >
                                                Total Amount Paid
                                            </h3>
                                            <h1
                                                class="text-3xl leading-tight font-semibold"
                                            >
                                                <div>P6000.00</div>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!---->
                        </div>
                    </div>
                   
                </div>

                <div class="flex-1 shadow-lg rounded-lg p-6">
                    <div
                        class="bg-blue-500 border-blue-500 text-white px-3 py-6 md:py-3 mb-6 last:mb-0 border rounded-lg transition-colors duration-150"
                    >
                        <div class="justify-between items-center block md:flex">
                            <div
                                class="flex items-center justify-center mb-6 md:mb-0"
                            >
                                <div
                                    class="flex flex-col md:flex-row items-center"
                                >
                                    <span
                                        class="inline-flex justify-center items-center w-10 md:w-5 h-10 md:h-5 md:mr-2"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="24"
                                            height="24"
                                            class="inline-block"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"
                                            ></path></svg></span
                                    ><span
                                        class="text-center md:text-left md:py-2"
                                        ><b>Payment date</b> is approaching
                                        soon.
                                    </span>
                                </div>
                            </div>
                            <!--Button Para makita yung details ng billing-->
                            <div class="flex items-center justify-center">
                                <button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded-full border-white ring-gray-200 dark:ring-gray-500 bg-white text-black hover:bg-gray-100 text-sm px-3 py-1"
                                    type="button"
                                >
                                    <!----><span class="px-2">See details</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="md:flex md:justify-between md:items-center">
                        <div class="md:flex md:items-center">
                            <div
                                class="mb-6 text-center md:mr-6 md:mb-0 md:text-left"
                            >
                                <!--Date kung kelan yung billing-->
                                <p class="text-gray-500">Next payment on</p>
                                <h1 class="text-xl font-semibold">
                                    Wed, Nov 1, 2023
                                </h1>
                            </div>
                            <div class="mb-6 text-center md:mb-0 md:text-left">
                                <p class="text-gray-500">Last billed on</p>
                                <h1 class="text-xl">Sun, Oct 1, 2023</h1>
                            </div>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-gray-500">Amount due</p>
                            <!--Pag walang balance yung upcoming payment lang pero pag may balance iplus sa upcoming payment-->
                            <h1 class="text-2xl font-semibold">P6000.00</h1>
                        </div>
                    </div>
                </div>
                <div class="flex items-center mt-5 justify-start">
                    <span
                        class="inline-flex justify-center items-center w-6 h-6 mr-2"
                        ><svg
                            viewBox="0 0 24 24"
                            width="20"
                            height="20"
                            class="inline-block"
                        >
                            <path
                                fill="currentColor"
                                d="M7 12C9.2 12 11 10.2 11 8S9.2 4 7 4 3 5.8 3 8 4.8 12 7 12M11 20V14.7C9.9 14.3 8.5 14 7 14C3.1 14 0 15.8 0 18V20H11M22 4H15C13.9 4 13 4.9 13 6V18C13 19.1 13.9 20 15 20H22C23.1 20 24 19.1 24 18V6C24 4.9 23.1 4 22 4M18 18H16V6H18V18Z"
                            ></path></svg></span>
                    <h1 class="text-3xl leading-tight">Payment History</h1>
                </div>
                <div
                    class="rounded-2xl flex-col shadow-lg mt-5 dark:bg-slate-900/70 bg-white flex mb-6"
                >
                    <div class="flex-1 p-6">
                        <div class="md:flex md:justify-between md:items-center">
                            <div
                                class="flex items-center justify-center md:justify-start flex-wrap -mb-3"
                            >
                                <button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-100 text-black dark:bg-slate-800 dark:text-white hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                    type="button"
                                >
                                    <!----><span class="px-2"
                                        >View invoice</span
                                    ></button
                                ><button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-100 text-black dark:bg-slate-800 dark:text-white hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                    type="button"
                                >
                                    <!----><span class="px-2">PDF</span>
                                </button>
                            </div>
                            <div
                                class="mt-6 md:mt-0 flex justify-between md:justify-end items-center"
                            >
                                <p class="text-gray-500 mr-6">
                                    Sun, Oct 1, 2023
                                </p>
                                <div
                                    class="inline-flex items-center capitalize leading-none text-sm border rounded-full py-1.5 px-4 bg-red-500 border-red-500 text-white mr-6"
                                >
                                    <!----><span>Unpaid</span>
                                </div>
                                <h2 class="text-2xl font-semibold">P3000.00</h2>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 p-6">
                        <div class="md:flex md:justify-between md:items-center">
                            <div
                                class="flex items-center justify-center md:justify-start flex-wrap -mb-3"
                            >
                                <button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-100 text-black dark:bg-slate-800 dark:text-white hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                    type="button"
                                >
                                    <!----><span class="px-2"
                                        >View invoice</span
                                    ></button
                                ><button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-100 text-black dark:bg-slate-800 dark:text-white hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                    type="button"
                                >
                                    <!----><span class="px-2">PDF</span>
                                </button>
                            </div>
                            <div
                                class="mt-6 md:mt-0 flex justify-between md:justify-end items-center"
                            >
                                <!--Date and day-->
                                <p class="text-gray-500 mr-6">
                                    Sun, Sep 1, 2023
                                </p>
                                <div
                                    class="inline-flex items-center capitalize leading-none text-sm border rounded-full py-1.5 px-4 bg-blue-500 text-white mr-6"
                                >
                                    <!----><span>Paid</span>
                                </div>
                                <!--Amount-->
                                <h2 class="text-2xl font-semibold">P3000.00</h2>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 p-6">
                        <div class="md:flex md:justify-between md:items-center">
                            <div
                                class="flex items-center justify-center md:justify-start flex-wrap -mb-3"
                            >
                                <button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-100 text-black dark:bg-slate-800 dark:text-white hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                    type="button"
                                >
                                    <!----><span class="px-2"
                                        >View invoice</span
                                    ></button
                                ><button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-100 text-black dark:bg-slate-800 dark:text-white hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                    type="button"
                                >
                                    <!----><span class="px-2">PDF</span>
                                </button>
                            </div>
                            <div
                                class="mt-6 md:mt-0 flex justify-between md:justify-end items-center"
                            >
                                <p class="text-gray-500 mr-6">
                                    Sun, Aug 1, 2023
                                </p>
                                <div
                                    class="inline-flex items-center capitalize leading-none text-sm border rounded-full py-1.5 px-4 bg-blue-500 text-white mr-6"
                                >
                                    <!----><span>Paid</span>
                                </div>
                                <h2 class="text-2xl font-semibold">P3000.00</h2>
                            </div>
                        </div>
                    </div>

                    <!--Pagination sa Payment History-->
                    <div
                        class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800"
                    >
                        <div class="justify-between items-center block md:flex">
                            <div
                                class="flex items-center justify-center mb-6 md:mb-0"
                            >
                                <div
                                    class="flex items-center justify-start flex-wrap -mb-3"
                                >
                                    <button
                                        class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-200 dark:bg-slate-700 hover:bg-gray-200 hover:dark:bg-slate-700 text-sm p-1 mr-3 last:mr-0 mb-3"
                                        type="button"
                                    >
                                        <!----><span class="px-2"
                                            >1</span
                                        ></button
                                    ><button
                                        class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                        type="button"
                                    >
                                        <!----><span class="px-2"
                                            >2</span
                                        ></button
                                    ><button
                                        class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                        type="button"
                                    >
                                        <!----><span class="px-2"
                                            >3</span
                                        ></button
                                    ><button
                                        class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-white dark:border-slate-900 ring-gray-200 dark:ring-gray-500 bg-white text-black dark:bg-slate-900 dark:text-white hover:bg-gray-100 hover:dark:bg-slate-800 text-sm p-1 mr-3 last:mr-0 mb-3"
                                        type="button"
                                    >
                                        <!----><span class="px-2">4</span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-center">
                                <small>Page 1 of 4</small>
                            </div>
                        </div>
                    </div>
                    <!---->
                </div>
                <section class="pt-6 mb-6 flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <span
                            class="inline-flex justify-center items-center w-6 h-6 mr-2"
                            ><svg
                                viewBox="0 0 24 24"
                                width="20"
                                height="20"
                                class="inline-block"
                            >
                                <path
                                    fill="currentColor"
                                    d="M20 4H4A2 2 0 0 0 2 6V18A2 2 0 0 0 4 20H20A2 2 0 0 0 22 18V6A2 2 0 0 0 20 4M20 11H4V8H20Z"
                                ></path></svg></span>
                        <h1 class="text-2xl leading-tight">Payment methods</h1>
                    </div>
                    <button
                        class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-blue-600 dark:border-blue-500 ring-blue-300 dark:ring-blue-700 bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 hover:border-blue-700 hover:dark:bg-blue-600 hover:dark:border-blue-600 text-sm p-1"
                        type="button"
                    >
                        <span
                            class="inline-flex justify-center items-center w-6 h-6"
                            ><svg
                                viewBox="0 0 24 24"
                                width="16"
                                height="16"
                                class="inline-block"
                            >
                                <path
                                    fill="currentColor"
                                    d="M17,13H13V17H11V13H7V11H11V7H13V11H17M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"
                                ></path></svg></span
                        ><span class="px-1">Add card</span>
                    </button>
                </section>
                <div
                    class="rounded-2xl shadow-lg flex-col dark:bg-slate-900/70 bg-white flex mb-6"
                >
                    <div class="flex-1 p-6">
                        <div class="md:flex md:justify-between md:items-center">
                            <div
                                class="flex justify-start items-center mb-6 md:mb-0"
                            >
                                <div
                                    class="flex items-center justify-center h-12 w-20 bg-gray-100 dark:bg-gray-800 rounded-lg mr-6"
                                >
                                    <img
                                        src="/assets/mc-light-bg-25bebdb1.svg"
                                        class="dark:grayscale h-8 md:h-10"
                                    />
                                </div>
                                <div class="mr-6">
                                    <h2 class="text-xl font-semibold">
                                        MasterCard 4575
                                    </h2>
                                    <p class="text-gray-500 text-sm uppercase">
                                        04/24 john doe
                                    </p>
                                </div>
                                <div
                                    class="inline-flex items-center capitalize leading-none text-xs border rounded-full py-1 px-3 bg-blue-500 border-blue-500 text-white"
                                >
                                    <span
                                        class="inline-flex justify-center items-center w-4 h-4 mr-1"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="14"
                                            height="14"
                                            class="inline-block"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M20 4H4C2.9 4 2 4.89 2 6V18C2 19.11 2.9 20 4 20H13.09C13.03 19.67 13 19.34 13 19C13 15.69 15.69 13 19 13C20.06 13 21.09 13.28 22 13.81V6C22 4.89 21.11 4 20 4M20 11H4V8H20M17.75 22L15 19L16.16 17.84L17.75 19.43L21.34 15.84L22.5 17.25L17.75 22"
                                            ></path></svg></span
                                    ><span>Primary</span>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-end md:justify-start flex-wrap -mb-3"
                            >
                                <button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-100 text-black dark:bg-slate-800 dark:text-white hover:bg-gray-200 hover:dark:bg-slate-700 p-1 mr-3 last:mr-0 mb-3"
                                    type="button"
                                >
                                    <span
                                        class="inline-flex justify-center items-center w-6 h-6"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M20.6 11.3C20.7 11.2 20.8 11.1 21 11.1C21.1 11.1 21.3 11.2 21.4 11.3L22.6 12.5C22.8 12.7 22.8 13.1 22.6 13.3L21.6 14.3L19.6 12.3L20.6 11.3M13 18.9L19 12.8L21.1 14.9L15.1 21H13V18.9M11 19.1V18.1L11.6 17.5L18.1 11H4V8H20V9.1L22 7.1V6C22 4.9 21.1 4 20 4H4C3.5 4 3 4.2 2.6 4.6C2.2 5 2 5.5 2 6V18C2 18.5 2.2 19 2.6 19.4C3 19.8 3.5 20 4 20H11V19.1Z"
                                            ></path></svg></span
                                    ><!---->
                                </button>
                                <button
                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-gray-100 dark:border-slate-800 ring-gray-200 dark:ring-gray-500 bg-gray-100 text-black dark:bg-slate-800 dark:text-white hover:bg-gray-200 hover:dark:bg-slate-700 p-1 mr-3 last:mr-0 mb-3"
                                    type="button"
                                >
                                    <span
                                        class="inline-flex justify-center items-center w-6 h-6"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M9,8H11V17H9V8M13,8H15V17H13V8Z"
                                            ></path></svg></span
                                    ><!---->
                                </button>
                            </div>
                        </div>
                    </div>
                    <!---->
                </div>
            </div>
        </div>
        <!--Eto yung dati-->
        <!--
        <div class="main w-full">
            <div class="w-full md:flex md:justify-center md:items-center" v-if="payments.length > 0">
                <div v-if="payments.length > 0"
                    :style="{width: isMobileView ? '100vw' : '35vw'}"
                    class="px-5 py-5 md:px-0 md:py-0"
                >
                    <div class="w-full flex justify-center items-center rounded-sm mt-5"
                        v-for="payment in payments" :key="payment.id"
                        style="height: 350px; 	background-color: rgb(251 146 60)"

                    >
                        <div class="w-full flex flex-col bg-white rounded-lg"
                            style="width: 90%;"
                        >
                            <div class="w-full">
                                <p class="text-center font-bold mt-10"
                                    :style="{'font-size': isMobileView ? '15px': '20px'}"
                                >
                                    {{ owner.name }}
                                </p>

                                <p class="text-center font-bold"
                                    :style="{'font-size': isMobileView ? '12px': '15px'}"
                                >
                                    {{ owner.phone_number }}
                                </p>

                                <p class="text-center font-bold my-8"
                                    :style="{'font-size': isMobileView ? '15px': '22px'}"
                                >
                                    ₱{{
                                        payment.partial != null
                                        ? parseFloat(payment.partial).toFixed(2)
                                        : payment.pending_payment != null
                                        ? parseFloat(payment.pending_payment).toFixed(2)
                                        : payment.amount_paid != null
                                        ? parseFloat(payment.amount_to_pay - payment.amount_paid).toFixed(2)
                                        : payment.amount_paid == null
                                        ? parseFloat(payment.amount_to_pay).toFixed(2)
                                        : ''
                                    }}
                                </p>
                            </div>

                            <div class="w-full flex justify-center items-center mb-2"
                                v-if="payment.amount_to_pay != payment.amount && !payment.pending_payment"
                            >
                                <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs mr-1"
                                    @click="pay(payment.id, 'GCash Payment')"
                                    v-if="methods.includes('GCash')"
                                >
                                    Pay Via GCash
                                </button>

                                <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs mr-1"
                                    @click="openModal(payment.id)"
                                    v-if="methods.includes('Bank')"
                                >
                                    Pay Via Bank
                                </button>
                            </div>

                            <div class="w-full" v-if="payment.pending_payment">
                                <p class="text-center font-bold mt-10"
                                    :style="{'font-size': isMobileView ? '10px': '12px'}"
                                >
                                    Wait for dorm owner to verify your payment.
                                </p>
                            </div>

                            <div class="w-full mb-2">
                                <p class="text-center font-bold mt-3"
                                    :style="{'font-size': isMobileView ? '10px': '10px'}"
                                >
                                    {{ payment.display_date }}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="w-full flex justify-center items-center" v-if="payments.length == 0">
                <div class=""
                    :style="{width: isMobileView ? '100vw' : '50vw'}"
                >
                    <div class="w-full px-2 rounded-lg text-center flex justify-center items-center bg-gray-200"
                        :style="{height: isMobileView ? '200px': '500px'}"

                    >
                        <p class="font-bold" :style="{'font-size': isMobileView ? '16px': '25px'}">
                            There's no payment(s) in your account.
                        </p>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <div id="bankModal" class="bankModal mt-10 md:mt-0">
                    <div class="bank-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '30%'}">
                        <div class="w-full">
                            <span class="text-lg font-bold">
                                Upload Bank Transfer Receipt
                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5">
                            <input type="file" id="bank_receipt" style="display: none"
                                @change="imageChange($event)"
                            />

                            <img :src="receipt ?? '/images/upload_image.png'"
                                class="cursor-pointer"
                                @click="imageClick()"
                                style="border: 1px solid black; border-radius: 5px; width: 100%; height: 260px;"
                            >

                            <p v-if="imageError" class="text-red-500 text-xs mt-1">
                                {{ imageError }}
                            </p>
                        </div>

                        <div class="w-full mt-5">
                            <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs mr-1"
                                @click="pay(payment_id, 'Bank Payment')"
                            >
                                Upload
                            </button>
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
    background-color: #e5e8e8;
}

.bankModal {
    display: none;
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 20px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0, 0, 0); /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
}

/* Modal Content */
.bank-modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 100%;
}

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

::-webkit-scrollbar {
    width: 0px;
    background: transparent; /* make scrollbar transparent */
}
</style>
