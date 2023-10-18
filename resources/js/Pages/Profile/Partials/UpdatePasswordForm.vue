<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    swal({
        title: "Are you sure to change password?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    },
    function(){
        form.put(route('password.update'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset()

                swal("Success!", "Your password has been changed.", "success");
            },
            onError: () => {
                if (form.errors.password) {
                    form.reset('password', 'password_confirmation');
                    passwordInput.value.focus();
                }
                if (form.errors.current_password) {
                    form.reset('current_password');
                    currentPasswordInput.value.focus();
                }
            },
        });
    });

};
</script>

<template>
    <section>
        <form
                    @submit.prevent="updatePassword"
                    class="rounded-2xl flex-col dark:bg-slate-900/70 bg-white flex"
                >
                    <div class="flex-1 p-6">
                        <!--Current Password-->
                        <div class="mb-6 last:mb-0">
                            <label class="block font-bold mb-2"
                                >Current password</label
                            >
                            <div class="">
                                <div class="relative">
                                    <input
                                        id="current_password"
                                        ref="currentPasswordInput"
                                        v-model="form.current_password"
                                        autocomplete="current-password"
                                        required
                                        type="password"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full dark:placeholder-gray-400 h-12 border bg-white dark:bg-slate-800 pl-10"
                                    /><span
                                        class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M21 13H14.4L19.1 17.7L17.7 19.1L13 14.4V21H11V14.3L6.3 19L4.9 17.6L9.4 13H3V11H9.6L4.9 6.3L6.3 4.9L11 9.6V3H13V9.4L17.6 4.8L19 6.3L14.3 11H21V13Z"
                                            ></path></svg></span>
                                </div>
                                <InputError :message="form.errors.current_password" class="mt-2" />
                            </div>
                            <div
                                class="text-xs text-gray-500 dark:text-slate-400 mt-1"
                            >
                                Required. Your current password
                            </div>
                        </div>
                        <hr
                            class="my-6 -mx-6 dark:border-slate-800 border-t border-gray-100"
                        />

                        <!--New Password-->
                        <div class="mb-6 last:mb-0">
                            <label class="block font-bold mb-2"
                                >New password</label
                            >
                            <div class="">
                                <div class="relative">
                                    <input
                                        id="password"
                                        ref="passwordInput"
                                        v-model="form.password"
                                        type="password"
                                        required=""
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full dark:placeholder-gray-400 h-12 border bg-white dark:bg-slate-800 pl-10"
                                    /><span
                                        class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M17,7H22V17H17V19A1,1 0 0,0 18,20H20V22H17.5C16.95,22 16,21.55 16,21C16,21.55 15.05,22 14.5,22H12V20H14A1,1 0 0,0 15,19V5A1,1 0 0,0 14,4H12V2H14.5C15.05,2 16,2.45 16,3C16,2.45 16.95,2 17.5,2H20V4H18A1,1 0 0,0 17,5V7M2,7H13V9H4V15H13V17H2V7M20,15V9H17V15H20M8.5,12A1.5,1.5 0 0,0 7,10.5A1.5,1.5 0 0,0 5.5,12A1.5,1.5 0 0,0 7,13.5A1.5,1.5 0 0,0 8.5,12M13,10.89C12.39,10.33 11.44,10.38 10.88,11C10.32,11.6 10.37,12.55 11,13.11C11.55,13.63 12.43,13.63 13,13.11V10.89Z"
                                            ></path></svg></span>
                                </div>
                            </div>
                            <div
                                class="text-xs text-gray-500 dark:text-slate-400 mt-1"
                            >
                                Required. New password
                            </div>
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <!--Confirm Password-->
                        <div class="mb-6 last:mb-0">
                            <label class="block font-bold mb-2"
                                >Confirm password</label
                            >
                            <div class="">
                                <div class="relative">
                                    <input
                                    id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        autocomplete="new-password"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full dark:placeholder-gray-400 h-12 border bg-white dark:bg-slate-800 pl-10"
                                    /><span
                                        class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="M17,7H22V17H17V19A1,1 0 0,0 18,20H20V22H17.5C16.95,22 16,21.55 16,21C16,21.55 15.05,22 14.5,22H12V20H14A1,1 0 0,0 15,19V5A1,1 0 0,0 14,4H12V2H14.5C15.05,2 16,2.45 16,3C16,2.45 16.95,2 17.5,2H20V4H18A1,1 0 0,0 17,5V7M2,7H13V9H4V15H13V17H2V7M20,15V9H17V15H20M8.5,12A1.5,1.5 0 0,0 7,10.5A1.5,1.5 0 0,0 5.5,12A1.5,1.5 0 0,0 7,13.5A1.5,1.5 0 0,0 8.5,12M13,10.89C12.39,10.33 11.44,10.38 10.88,11C10.32,11.6 10.37,12.55 11,13.11C11.55,13.63 12.43,13.63 13,13.11V10.89Z"
                                            ></path></svg></span>
                                </div>
                            </div>
                            <div
                                class="text-xs text-gray-500 dark:text-slate-400 mt-1"
                            >
                                Required. New password one more time
                            </div>
                        </div>
                        <InputError :message="form.errors.password_confirmation" class="mt-2" />
                    </div>
                    <footer class="p-6">
                        <div
                            class="flex items-center justify-start flex-wrap -mb-3"
                        >
                            <button
                                :disabled="form.processing"
                                class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-blue-600 dark:border-blue-500 ring-blue-300 dark:ring-blue-700 bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 hover:border-blue-700 hover:dark:bg-blue-600 hover:dark:border-blue-600 py-2 px-3 mr-3 last:mr-0 mb-3"
                                type="submit"
                            >
                                <!----><span class="px-2">Change Password</span>
                            </button>
                            <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                                <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                            </Transition>
                        </div>
                    </footer>
                </form>
    </section>
</template>
