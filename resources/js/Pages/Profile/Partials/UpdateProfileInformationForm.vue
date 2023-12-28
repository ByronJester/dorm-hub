<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const toast = useToast();
const confirm = useConfirm();
const user = usePage().props.auth.user;

const form = useForm({
    first_name: user.first_name,
    middle_name: user.middle_name,
    last_name: user.last_name,
    phone_number: user.phone_number,
    username: user.username,
    image: user.image,
    bio: user.bio,
    pk: user.pk,
    sk: user.sk,
    bank_name: user.bank_name,
    account_name: user.account_name,
    account_number: user.account_number,
    payment_settings: false
});

const updateProfile = () => {
    confirm.require({
                message: 'Are you sure you want to update your profile?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    form.patch(route("profile.update"), {
                        preserveScroll: true,
                        onSuccess: () => {
                            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'You have successfully update your profile', life: 3000 });
                            window.location.reload();
                        },
                        onError: () => {
                            if (form.errors.password) {
                                form.reset("password", "password_confirmation");
                                passwordInput.value.focus();
                            }
                            if (form.errors.current_password) {
                                form.reset("current_password");
                                currentPasswordInput.value.focus();
                            }

                            //Add validation handler for errors in phone number
                            if(form.errors.phone_number){
                                toast.add({ severity: 'error', summary: 'Error', detail: form.errors.phone_number, life: 3000 });
                            }

                            //Add validation handler for errors in first name
                            if(form.errors.first_name){
                                toast.add({ severity: 'error', summary: 'Error', detail: form.errors.first_name, life: 3000 });
                            }

                            //Add validation handler for errors in last name
                            if(form.errors.last_name){
                                toast.add({ severity: 'error', summary: 'Error', detail: form.errors.last_name, life: 3000 });
                            }

                            //Add validation handler for errors in email
                            if(form.errors.email){
                                toast.add({ severity: 'error', summary: 'Error', detail: form.errors.email, life: 3000 });
                            }
                        },
                    });
                    
                },
                reject: () => {
                    
                }
            });
};

const imageClick = () => {
    document.getElementById("profile_image").click();
};

const imageChange = (e) => {
    const image = e.target.files[0];

    const reader = new FileReader();

    reader.readAsDataURL(image);

    reader.onload = (e) => {
        form.image = e.target.result;
    };
};
</script>

<template>
    <div class="flex-1 p-6">
        
        <div
            class="justify-around shadow-md p-5 lg:justify-center items-center block md:flex"
        >
            <div class="flex items-center justify-center mb-6 md:mb-0">
                <div class="lg:mx-12 md:mx-2">
                    <img
                        :src="form.image ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'"
                        alt="Profile picture"
                        class="rounded-full block md:h-60 w-60 bg-no-repeat bg-cover object-fit max-w-full bg-gray-100 dark:bg-slate-800"
                    />
                </div>
            </div>
            <div class="flex items-center justify-center">
                <div class="space-y-3 text-center md:text-left lg:mx-12">
                    <h1 class="text-2xl">Hello, <b>{{user.first_name}}</b>!</h1>
                    <h2 class="text-md text-gray-600">{{user.bio}}</h2>
                    <div class="flex justify-center md:block">
                        <div
                            class="inline-flex items-center capitalize leading-none text-sm border rounded-full py-1.5 px-4 bg-blue-500 border-blue-500 text-white"
                        >
                            <span
                                class="inline-flex justify-center items-center w-4 h-4 mr-2"
                                ><svg
                                    viewBox="0 0 24 24"
                                    width="16"
                                    height="16"
                                    class="inline-block"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"
                                    ></path></svg></span
                            ><span>Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h1 class="text-2xl leading-tight mb-3">Profile Information</h1>

    <form @submit.prevent="updateProfile" class="rounded-2xl flex-col shadow-md bg-white flex">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="flex-1 p-6">
                <div class="mb-6 last:mb-0">
                    <label class="block font-bold mb-2">Avatar</label>
                    <div class="">
                        <div class="flex justify-start items-stretch relative">
                            <div class="inline-flex">
                                <a

                                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-blue-600 ring-blue-300 py-2 px-3"
                                    disabled="false"
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
                                                d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"
                                            ></path>
                                        </svg> </span
                                    ><span class="px-2">Upload</span>

                                    <input
                                        @change="imageChange($event)"
                                        accept="image/png, image/gif, image/jpeg"
                                        id="profile_image"
                                        type="file"
                                        class="absolute top-0 left-0 h-full opacity-0 outline-none -z-1"
                                    />
                                </a>
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <div class="mb-6 last:mb-0">
                    <label class="block font-bold mb-2">Name</label>
                    <div class="grid grid-col-2 gap-2">
                        <!--First Name-->
                        <div class="relative">
                            <input
                                id="first_name"
                                autocomplete="first_name"
                                required=""
                                v-model="form.first_name"
                                type="text"
                                class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full 12 border bg-white pl-10"
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
                                        d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"
                                    ></path></svg></span>
                        </div>
                        <!--Middle Name-->
                        <div class="relative">
                            <input
                                id="middle_name"
                                autocomplete="middle_name"
                                v-model="form.middle_name"
                                type="text"
                                class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full 12 border bg-white pl-10"
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
                                        d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"
                                    ></path></svg></span>
                        </div>
                        <!--Last Name-->
                        <div class="relative">
                            <input
                                id="last_name"
                                autocomplete="last_name"
                                required=""
                                v-model="form.last_name"
                                type="text"
                                placeholder="Last Name"
                                class="px-3 py-2 max-w-full focus:ring focus:outline-none placeholder:text-gray-400 border-gray-700 rounded w-full 12 border bg-white pl-10"
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
                                        d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"
                                    ></path></svg></span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                        Required. Your name
                    </div>
                </div>
                <!--Email-->
                <div class="mb-6 last:mb-0">
                    <label class="block font-bold mb-2">Username</label>
                    <div class="">
                        <div class="relative">
                            <input
                                id="email"
                                autocomplete="email"
                                required=""
                                v-model="form.username"
                                type="text"
                                class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full h-12 border bg-white pl-10"
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
                                        d="M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4M17,17H7V15H17M17,13H7V11H17M20,9H17V6H20"
                                    ></path></svg></span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Required. Your username
                    </div>
                </div>
            </div>
            <div class="flex-1 pl-6 pr-6 lg:pt-6 mb-6">
                <!--Contact-->
                <div class="mb-6 last:mb-0">
                    <label class="block font-bold mb-2">Contact</label>
                    <div class="">
                        <div class="relative">
                            <input
                                id="phone_number"
                                autocomplete="phone_number"
                                v-model="form.phone_number"
                                required=""
                                type="text"
                                class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full 12 border bg-white pl-10"
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
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"
                                    /></svg></span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                        Required. Contact
                    </div>
                </div>
                <!--Bio-->
                <div class="mb-6 last:mb-0">
                    <label class="block font-bold mb-2">Bio</label>
                    <div class="">
                        <div class="relative">
                            <textarea
                                id="message"
                                v-model="form.bio"
                                rows="12"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-700 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Write your bio here..." />
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <hr />

        <footer class="p-6">
            <div>
                <Toast />
                <ConfirmDialog></ConfirmDialog>
                <button
                    :disabled="form.processing"
                    class="bg-orange-400 text-white hover:opacity-75 p-4 rounded-xl float-right"
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


    
        
</template>
