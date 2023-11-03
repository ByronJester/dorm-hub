<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';


const user = usePage().props.auth.user;

const form = useForm({
    first_name: user.first_name,
    middle_name: user.middle_name,
    last_name: user.last_name,
    phone_number: user.phone_number,
    email: user.email,
    image: user.image,
    bio: user.bio,
    pk: user.pk,
    sk: user.sk,
    bank_name: user.bank_name,
    account_name: user.account_name,
    account_number: user.account_number,
    payment_settings: true
});

const updateProfile = () => {
    swal(
        {
            title: "Are you sure to update your payment settings?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false,
        },
        function () {
            form.patch(route("profile.update"), {
                preserveScroll: true,
                onSuccess: () => {
                    swal(
                        "Success!",
                        "Your payment settings has been updated.",
                        "success"
                    );

                },
                onError: () => {
                },
            });
        }
    );
};
</script>

<template>
    <div class="w-full">
        <h1 class="text-2xl leading-tight m-6">Payment Information</h1>

        <form @submit.prevent="updateProfile" class="rounded-2xl flex-col shadow-md bg-white flex">
            <div class="w-full">
                <div class="flex-1 pl-6 pr-6 lg:pt-6" v-if="user.user_type == 'owner'">
                    <!--Paymongo-->
                    <div class="mb-6 last:mb-0">
                        <div>
                            <div>
                                <div class="flex gap-2 items-center">
                                    <label class="block font-bold mb-2">Paymongo</label>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        color="green"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5 cursor-pointer hover:bg-orange-400 animate-bounce"
                                        @click="openTutModal()"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                                        />
                                    </svg>
                                </div>
                                <span class="text-xs text-red-500"
                                    ></span
                                >
                            </div>
                        </div>

                        <div class="grid grid-rows-2 gap-2">
                            <!--PK-->
                            <div class="relative">
                                <input
                                    id="pk"
                                    v-model="form.pk"
                                    required=""
                                    placeholder="Paymongo Public Key"
                                    type="text"
                                    class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                /><span
                                    class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                    ><svg
                                        viewBox="0 0 24 24"
                                        width="16"
                                        height="16"
                                        class="inline-block"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"
                                        /></svg></span>
                            </div>
                            <!--SK-->
                            <div class="relative">
                                <input
                                    id="sk"
                                    v-model="form.sk"
                                    required=""
                                    placeholder="Paymongo Secret Key"
                                    type="text"
                                    class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                /><span
                                    class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                    ><svg
                                        viewBox="0 0 24 24"
                                        width="16"
                                        height="16"
                                        class="inline-block"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"
                                        /></svg></span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                            Required. Paymongo
                        </div>

                    </div>
                    <!--Bank-->
                    <div class="mb-6 last:mb-0" v-if="user.user_type == 'owner'">
                        <div>
                            <div>
                                <label class="block font-bold mb-2">Bank Details <span class="text-sm text-gray-400">(Optional)</span></label>
                            </div>
                        </div>

                        <div class="grid grid-rows-2 gap-2">
                            <!--Bank Name-->
                            <div class="relative">
                                <input
                                    id="bank_name"
                                    v-model="form.bank_name"
                                    placeholder="Bank Name"
                                    type="text"
                                    class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                /><span
                                    class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                    ><svg
                                        viewBox="0 0 24 24"
                                        width="16"
                                        height="16"
                                        class="inline-block"
                                    >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg></span>
                            </div>
                            <!--Account Name-->
                            <div class="relative">
                                <input
                                    id="acc_name"
                                    v-model="form.account_name"
                                    placeholder="Account Name"
                                    type="text"
                                    class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                /><span
                                    class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                    ><svg
                                        viewBox="0 0 24 24"
                                        width="16"
                                        height="16"
                                        class="inline-block"
                                    >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg></span>
                            </div>
                            <!--Account Number-->
                            <div class="relative">
                                <input
                                    id="acc_number"
                                    v-model="form.account_number"
                                    placeholder="Account Number"
                                    type="text"
                                    class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                /><span
                                    class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                    ><svg
                                        viewBox="0 0 24 24"
                                        width="16"
                                        height="16"
                                        class="inline-block"
                                    >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg></span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                            Optional. Bank Details
                        </div>

                    </div>
                </div>

            </div>

            <footer class="p-6">
                <div class="flex items-center justify-start flex-wrap -mb-3">
                    <button
                        :disabled="form.processing"
                        class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-blue-600 dark:border-blue-500 ring-blue-300 dark:ring-blue-700 bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 hover:border-blue-700 hover:dark:bg-blue-600 hover:dark:border-blue-600 py-2 px-3 mr-3 last:mr-0 mb-3"
                        type="submit"
                    >
                        <!----><span class="px-2">Update</span>
                    </button>
                    <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                    </Transition>
                </div>
            </footer>
        </form>
    </div>

</template>
