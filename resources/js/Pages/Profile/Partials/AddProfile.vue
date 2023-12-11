<script>

import FileUpload from 'primevue/fileupload';
import { ref } from 'vue';
import axios from "axios";

export default{
    components:{
        FileUpload
    },
    setup(){
        const openComplainModal = () => {
            var modal = document.getElementById("complainModal");

            modal.style.display = "block";
        };

        const closeComplainModal = () => {
            var modal = document.getElementById("complainModal");

            modal.style.display = "none";
        };

        const form = ref({
            relationship : null,
            // image: null,
            first_name: null,
            last_name: null,
            contact: null
        })

        const onUploadImage = (arg) => {
            alert()
            console.log(arg)
        }


        const saveProfile = () => {
            console.log(form.value)
            axios.post(route('tenant.sub-profile'), form.value)
                    .then(response => {
                        location.reload()
                    })
                    .catch(error => {

                    })
        }

        return{
            openComplainModal,
            closeComplainModal,
            form,
            onUploadImage,
            saveProfile
        }
    }
}

</script>
<template>
    <div class="mt-5">
        <p class="mb-5 text-2xl">Add Profile</p>
        <button @click="openComplainModal()" class="bg-orange-400 text-white p-4 rounded-xl hover:opacity-75">
            <i class="fa-solid fa-user-plus" style="color: #FFF;"></i>
            Add
        </button>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mt-5">
            <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center justify-center">
                <img
                    src='https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'
                    alt="Profile picture"
                    class="rounded-full block md:h-40 w-40  bg-no-repeat bg-cover object-fit max-w-full bg-gray-100 dark:bg-slate-800"
                />
                <p class="mt-2 text-gray-700 text-lg font-bold">Jear De La Rea</p>
                <p class="mt-2 text-gray-400 text-lg font-bold">Father</p>
            </div>
        </div>

        <div
                    id="complainModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Add Profile
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeComplainModal()"
                                    >
                                        <svg
                                            class="w-3 h-3"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 14 14"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                                            />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-6 space-y-6">
                                    <form>
                                        <label for="subject" class="block mb-2 font-bold text-xl">Relationship</label>
                                            <select id="subject" v-model="form.relationship" class="block w-full px-4 py-3 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 ">
                                                <option selected>Choose</option>
                                                <option value="Parent">Parent</option>
                                                <option value="Sibling">Sibling</option>
                                                <option value="Friend">Friend</option>
                                                <option value="Classmate">Classmate</option>
                                                <option value="Others">Others</option>

                                            </select>
                                        <!-- <div class="flex items-center justify-between px-3 py-2 border-b dark:border-gray-600">
                                            <div class="flex flex-wrap items-center divide-gray-200 sm:divide-x dark:divide-gray-600">
                                                <div class="flex items-center space-x-1 sm:pr-4">
                                                    <button type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                                                <path d="M14.066 0H7v5a2 2 0 0 1-2 2H0v11a1.97 1.97 0 0 0 1.934 2h12.132A1.97 1.97 0 0 0 16 18V2a1.97 1.97 0 0 0-1.934-2ZM10.5 6a1.5 1.5 0 1 1 0 2.999A1.5 1.5 0 0 1 10.5 6Zm2.221 10.515a1 1 0 0 1-.858.485h-8a1 1 0 0 1-.9-1.43L5.6 10.039a.978.978 0 0 1 .936-.57 1 1 0 0 1 .9.632l1.181 2.981.541-1a.945.945 0 0 1 .883-.522 1 1 0 0 1 .879.529l1.832 3.438a1 1 0 0 1-.031.988Z"/>
                                                                <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z"/>
                                                            </svg>
                                                        <span class="sr-only">Upload image</span>
                                                    </button>

                                                </div>
                                            </div>
                                        </div> -->
                                        <p class="mt-5 font-bold text-xl">Profile Information</p>
                                        <!-- <div class="flex flex-col gap-2 w-full items-center justify-center">
                                            <div class="">
                                                <img
                                                    src='https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'
                                                    alt="Profile picture"
                                                    class="rounded-full block md:h-60 w-60 bg-no-repeat bg-cover object-fit max-w-full bg-gray-100 dark:bg-slate-800"
                                                />
                                            </div>
                                            <div>
                                                <p></p>
                                                <FileUpload mode="basic" name="demo[]" url="/api/upload" accept="image/*" class="bg-orange-400" :maxFileSize="1000000"
                                                    @upload="onUploadImage()"
                                                />
                                            </div>
                                        </div> -->
                                        <hr class="my-3" />
                                        <div class="grid grid-cols-1 md:grid-cols-2 my-2 gap-2">
                                            <div>
                                                <p>First Name</p>
                                                <input class="rounded-xl w-full border-gray-400" type="text" v-model="form.first_name"/>
                                            </div>
                                            <div>
                                                <p>Last Name</p>
                                                <input class="rounded-xl w-full border-gray-400" type="text" v-model="form.last_name"/>
                                            </div>
                                        </div>
                                        <p>Contact</p>
                                         <vue-tel-input
                                            autoFormat
                                            validCharactersOnly
                                            :maxlength = '16'
                                            v-model="form.contact"
                                        ></vue-tel-input>
                                    </form>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                >
                                    <button
                                        @click.prevent="saveProfile()"
                                        type="button"
                                        class="py-2 px-4 bg-orange-400 text-white rounded-full float-right hover:opacity-75"
                                    >
                                        Submit
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
</template>
