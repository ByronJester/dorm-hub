<script>
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { ref, onMounted } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
export default{
    props: ["user"],
    components:{
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        ApplicationLogo,
        Toast
    },
    setup(props){
        const userStatus = ref(props.user.status);
        const user = props.user;
        const toast = useToast();
        const getUserStatus = () => {
            if(userStatus.value == 'pending') {
                axios
                    .get(route("owner.status"))
                    .then((response) => {
                        userStatus.value = response.data
                    })
                    .catch((error) => {

                    });
            }

        }

        console.log(user.first_name);

        const recallTimer = ref(null);

        onMounted(() => {
            clearInterval(recallTimer.value);

            recallTimer.value = setInterval(() => {
                getUserStatus();
            }, 10000);
        });

        const statuss = ref('pending')
        const form = useForm({
            first_name: user.first_name,
            middle_name: user.middle_name,
            last_name: user.last_name,
            phone_number: user.phone_number,
            id_picture: '',
            selfie_id_picture: '',
            status: statuss.value
        });

        const idPictureChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                console.log(e);
                form.id_picture = e.target.result;
            };
        };

        //Dinagdag ko


        const SelfieidPictureChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                console.log(e);
                form.selfie_id_picture = e.target.result;
            };
        };

        const confirmSubmit = () => {
            axios
                .post(route("submit.id"), form)
                .then((response) => {
                    toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Submitted successfully', life: 3000 });
                    location.reload();
                })
                .catch((error) => {
                    toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Submitted unsuccessfuly', life: 3000 });
                });
        };


        return{
            userStatus,
            form,
            idPictureChange,
            SelfieidPictureChange,
            confirmSubmit
        }
    }
}
</script>
<template>
    <Toast />
     <nav
        class="fixed top-0 z-50 w-full bg-white shadow-md dark:bg-gray-800 dark:border-gray-700"
        v-if="userStatus == null || userStatus == 'decline' || userStatus =='pending'"
    >
        <div class="w-full flex items-center justify-center p-3 text-white text-sm font-semibold bg-red-500" v-if="userStatus == 'decline'">
            Your account has been declined. Reason: {{ user.reason }}
        </div>
        <div class="w-full flex items-center justify-center p-3 text-white text-sm font-semibold bg-orange-400" v-if="userStatus == 'pending'">
            You have pending status for user verication. Please wait for the system admin to approve you!
        </div>
        <div class="max-w-[2520px] xl:px-20 md:px-10 sm:px-2 px-4">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <a href="/">
                        <ApplicationLogo />
                    </a>

                    <div class="flex items-center">
                        <div class="flex items-center ml-3">
                            <AppDropdown>
                                <button
                                    type="button"
                                    class="flex text-sm text-black dark:text-white hover:text-gray-400"
                                    aria-expanded="false"
                                >
                                    {{ user.first_name }}
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        class="ml-1 h-5 w-5 fill-current"
                                    >
                                        <path
                                            d="M15.3 9.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"
                                        ></path>
                                    </svg>
                                </button>

                                <AppDropdownContent class="">
                                    <AppDropdownItem
                                        @click="logOut()"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                        role="menuitem"
                                    >
                                        Logout
                                    </AppDropdownItem>
                                </AppDropdownContent>
                            </AppDropdown>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-[2520px] mt-24 xl:px-20 md:px-10 sm:px-2 px-4" v-if="userStatus == null || userStatus == 'decline'">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="w-full items-center">
                <p class="font-bold text-2xl">Verification Details</p>
                <hr class="mt-5" />
                <form @submit.prevent="confirmSubmit">
                    <div
                        class="py-6 border-b border-gray-100 dark:border-gray-800"
                    >
                        <div class="w-full md:w-9/12">
                            <div class="flex flex-wrap -m-3">
                                <div class="w-full p-3 md:w-1/3">
                                    <p
                                        class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                    >
                                        Name:
                                    </p>
                                </div>
                                <div class="w-full p-3 md:w-1/3">
                                    <input
                                        class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                        type="text"
                                        v-model='form.first_name'
                                        placeholder="First Name"
                                    />
                                </div>
                                <div class="w-full p-3 md:w-1/3">
                                    <input
                                        class="w-full px-4 py-2.5 dark:bg-gray-800 placeholder-gray-400 dark:border-gray-800 dark:placeholder-gray-500 dark:text-gray-400 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                        type="text"
                                        v-model='form.last_name'
                                        placeholder="Last Name"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="py-6 border-b border-gray-100 dark:border-gray-800"
                    >
                        <div class="w-full md:w-9/12">
                            <div class="flex flex-wrap -m-3">
                                <div class="w-full p-3 md:w-1/3">
                                    <p
                                        class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                    >
                                        Address:
                                    </p>
                                </div>
                                <div class="w-full p-3 md:w-1/2">
                                    <input
                                        class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                        type="text"
                                        placeholder="Address"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="py-6 border-b border-gray-100 dark:border-gray-800"
                    >
                        <div class="w-full md:w-9/12">
                            <div class="flex flex-wrap -m-3">
                                <div class="w-full p-3 md:w-1/3">
                                    <p
                                        class="text-sm font-semibold text-gray-800 dark:text-gray-400"
                                    >
                                        Verificcation ID
                                    </p>
                                    <p
                                        class="text-xs font-semibold text-orange-400"
                                    >
                                        (ex. Goverments ID, and School ID)
                                    </p>
                                </div>
                                <div class="w-full p-3 md:flex-1">
                                    <div
                                        class="flex items-center justify-center w-full md:w-1/2"
                                    >
                                    <input
                                        type="file"
                                        id="idPicture"
                                        class="hidden"
                                        @change="idPictureChange($event)"
                                        accept="image/*"
                                        required
                                    />
                                        <label
                                            for="idPicture"
                                            class="flex flex-col items-center justify-center w-full h-64 bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
                                        >
                                            <img
                                                v-if="form.id_picture"
                                                :src="form.id_picture"
                                                alt="id picure"
                                                class="h-64 w-full object-cover bg-no-repeat bg-center rounded-lg"
                                            />
                                            <div
                                                v-else
                                                class="flex flex-col items-center justify-center px-4 pt-5 pb-6"
                                            >
                                                <span
                                                    class="text-blue-500 dark:text-gray-400"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        fill="currentColor"
                                                        class="w-8 h-8 bi bi-cloud-upload"
                                                        viewBox="0 0 16 16"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"
                                                        />
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"
                                                        /></svg></span>
                                                <p
                                                    class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    <span
                                                        class="font-semibold text-blue-500"
                                                        >Click to upload</span
                                                    >
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    SVG, PNG, JPG or GIF (upto
                                                    10MB)
                                                </p>
                                            </div>
                                            <input type="file" class="hidden" />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="py-6 border-b border-gray-100 dark:border-gray-800"
                    >
                        <div class="w-full md:w-9/12">
                            <div class="flex flex-wrap -m-3">
                                <div class="w-full p-3 md:w-1/3">
                                    <p
                                        class="text-sm font-semibold text-gray-800 dark:text-gray-400"
                                    >
                                        Selfie with Verificcation ID
                                    </p>
                                    <p
                                        class="text-xs font-semibold text-orange-400"
                                    >
                                        (Upload a selfie holding verification
                                        ID)
                                    </p>
                                </div>
                                <div class="w-full p-3 md:flex-1">
                                    <div
                                        class="flex items-center justify-center w-full md:w-1/2"
                                    >
                                    <input
                                        type="file"
                                        id="selfie_id_picture"
                                        class="hidden"
                                        @change="SelfieidPictureChange($event)"
                                        accept="image/*"
                                        required
                                    />

                                        <label
                                            for="selfie_id_picture"
                                            class="flex flex-col items-center justify-center w-full h-64 bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
                                        >
                                            <img
                                                v-if="form.selfie_id_picture"
                                                :src="form.selfie_id_picture"
                                                alt="business permit"
                                                class="h-64 w-full object-cover bg-no-repeat bg-center rounded-lg"
                                            />
                                            <div
                                                class="flex flex-col items-center justify-center px-4 pt-5 pb-6"
                                                v-else
                                            >
                                                <span
                                                    class="text-blue-500 dark:text-gray-400"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        fill="currentColor"
                                                        class="w-8 h-8 bi bi-cloud-upload"
                                                        viewBox="0 0 16 16"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"
                                                        />
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"
                                                        /></svg></span>
                                                <p
                                                    class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    <span
                                                        class="font-semibold text-blue-500"
                                                        >Click to upload</span
                                                    >
                                                    or drag and drop
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    SVG, PNG, JPG or GIF (upto
                                                    10MB)
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-lg bg-orange-400 text-white float-right mb-5">Submit</button>
                </form>
            </div>
        </div>
    </div>

        <div class="flex items-center justify-center w-full h-screen" v-if="userStatus == 'pending'">
            <div class="flex justify-center items-center space-x-1 text-sm text-gray-700">

                        <svg fill='none' class="w-6 h-6 animate-spin" viewBox="0 0 32 32" xmlns='http://www.w3.org/2000/svg'>
                            <path clip-rule='evenodd'
                                d='M15.165 8.53a.5.5 0 01-.404.58A7 7 0 1023 16a.5.5 0 011 0 8 8 0 11-9.416-7.874.5.5 0 01.58.404z'
                                fill='currentColor' fill-rule='evenodd' />
                        </svg>


                <div>Loading ...</div>
            </div>
        </div>
</template>