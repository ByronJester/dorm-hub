<!-- <script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);

const isMobileView = ref(false)

isMobileView.value = screen.width < 600;

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    dorms: Array
});

</script> -->

<script>
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import DormList from '@/Components/DormList.vue';
    import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
    import { ref, onMounted } from 'vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import Checkbox from '@/Components/Checkbox.vue';
    import Terms from '@/Components/Terms.vue' //dinagdag ko pati nasa component
    
    

    export default {
        components: {
            ApplicationLogo,
            Link,
            DormList,
            InputLabel,
            TextInput,
            PrimaryButton,
            InputError,
            Terms,
            Checkbox
        },
        setup(){
            const openRegisterModal = () => {
                var modal = document.getElementById("registerModal");

                modal.style.display = "block";

                form.reset()
            }

            const closeRegisterModal = () => {
                var modal = document.getElementById("registerModal");

                modal.style.display = "none";
            }

            //Dinagdag ko
            const openTermsModal = () => {
                var modal = document.getElementById("defaultModal");

                modal.style.display = "block";

            }

            const closeTermsModal = () => {
                var modal = document.getElementById("defaultModal");

                modal.style.display = "none";

            }

            const openTutModal = () => {
                var modal = document.getElementById("tutModal");

                modal.style.display = "block";

            }

            const closeTutModal = () => {
                var modal = document.getElementById("tutModal");

                modal.style.display = "none";

            }

            const selectUserType =(userType)=> {
               form.user_type = userType;
            }
            //Hanggang dito
            
            const openLoginModal = () => {
                var modal = document.getElementById("loginModal");

                modal.style.display = "block";

                loginForm.reset()
            }

            const closeLoginModal = () => {
                var modal = document.getElementById("loginModal");

                modal.style.display = "none";
            }

            const showingNavigationDropdown = ref(false);

            const isMobileView = ref(false)

            isMobileView.value = screen.width < 600;

            const page = usePage()

            const dorms = ref([])

            onMounted(() => {
                dorms.value = page.props.dorms
            });

            const form = useForm({
                first_name: '',
                middle_name: '',
                last_name: '',
                phone_number: '',
                email: '',
                user_type: '',
                password: '',
                password_confirmation: '',
                id_picture: '',
                terms: false,
            });

            const idPictureClick = () => {
                document.getElementById('id_picture').click()
            }

            const idPictureChange = (e) => {
                const image = e.target.files[0];

                const reader = new FileReader();

                reader.readAsDataURL(image);

                reader.onload = e =>{
                    console.log(e)
                    form.id_picture = e.target.result
                }
            }

            //Dinagdag ko
            const SelfieidPictureClick = () => {
                document.getElementById('selfie_id_picture').click()
            }

            const SelfieidPictureChange = (e) => {
                const image = e.target.files[0];

                const reader = new FileReader();

                reader.readAsDataURL(image);

                reader.onload = e =>{
                    console.log(e)
                    form.id_picture = e.target.result
                }
            }
            //hanggagn dito

            const submit = () => {
                form.post(route('user.register'), {
                    // onFinish: () => form.reset('password', 'password_confirmation'),
                });
            };

            const loginForm = useForm({
                email: '',
                password: '',
                remember: false,
            });

            const login = () => {
                loginForm.post(route('login'), {});
            }

            return {
                selectUserType,
                dorms,
                showingNavigationDropdown,
                isMobileView,
                form,
                loginForm,
                openRegisterModal,
                closeRegisterModal,
                openLoginModal,
                closeLoginModal,
                openTermsModal,
                closeTermsModal,
                openTutModal,
                closeTutModal,
                idPictureClick,
                idPictureChange,
                SelfieidPictureClick,
                SelfieidPictureChange,
                submit,
                login
            }
        }
    }
</script>

<template>
    <div>
        <div class="h-full">
            <nav class="bg-white py-4 drop-shadow-lg">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between nav-bar-h relative">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link href="/">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>
                            
                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex absolute right-0">
                                <button @click.prevent="openLoginModal()" class="bg-transparent hover:bg-orange-400 text-orange-400 font-semibold hover:text-white py-2 px-4 border border-orange-400 hover:border-transparent rounded">
                                     Signin
                                </button>
                                <button @click.prevent="openRegisterModal()" class="bg-orange-400 hover:bg-transparent text-white font-semibold hover:text-orange-400 py-2 px-4 border border-transparent hover:border-orange-400 rounded">
                                     Signup
                                </button>
                              
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <div class="flex flex-col">
                            <div class="w-full mt-2">
                                <span
                                    @click.prevent="openLoginModal()"
                                    class="ml-4 font-semibold text-black mt-2 cursor-pointer"
                                    >SIGNIN
                                </span>
                            </div>

                            <div class="w-full mt-2">
                                <span
                                    @click="openRegisterModal()"
                                    class="ml-4 font-semibold text-black mt-3"
                                    >SIGN UP</span
                                >
                            </div>
                        </div>
                    </div>

                </div>

            </nav>

            <div class="w-full main flex flex-col">

                <div class="w-full">
                    <DormList :dorms.sync="dorms" :user.sync="null"/>
                </div>
                    <!--New UI register modal-->
                    <div id="registerModal" tabindex="-1" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.7);"
                        class="registerModal fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="h-screen flex justify-center items-center">
                            <div class="relative w-full max-w-2xl max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="authentication-modal"  @click="closeRegisterModal()">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                    <div class="px-6 py-6 lg:px-8">
                                        <div class="mb-4">
                                            <h3 class="text-xl font-medium text-gray-900 dark:text-white">Welcome to Dormhub!</h3>
                                            <span class="mb-4 text-sm font-medium text-gray-900 dark:text-white">Create an account</span>
                                        </div>
                                        
                                        <hr class="mb-4">
                                        <form class="space-y-6" @submit.prevent="submit">
                                            <div class="mb-4">
                                            <InputLabel class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Register as ?" />
                                            <div class="grid grid-cols-2 gap-2">
                                                <button @click="selectUserType('tenant')" :class="{ 'bg-orange-500': form.user_type === 'tenant', 'bg-transparent': form.user_type !== 'tenant' }" class="px-4 py-2 rounded-lg border hover:bg-orange-400 text-black dark:text-white">Tenant</button>
                                                <button @click="selectUserType('owner')" :class="{ 'bg-orange-500': form.user_type === 'owner', 'bg-transparent': form.user_type !== 'owner' }" class="px-4 py-2 rounded-lg border hover:bg-orange-400 text-black dark:text-white">Dorm Owner</button>
                                            </div>
                                            <InputError class="mt-2" :message="form.errors.user_type" />
                                            </div>
                                            <hr class="mb-4">
                                            <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <InputLabel for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="First Name" />

                                                    <TextInput
                                                        id="first_name"
                                                        type="text"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="ex: Juan"
                                                        v-model="form.first_name"
                                                        required
                                                        autofocus
                                                        autocomplete="first_name"
                                                    />

                                                <InputError class="mt-2" :message="form.errors.first_name" />
                                            </div>

                                             <div>
                                                <InputLabel for="middle_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Middle Name" />

                                                    <TextInput
                                                        id="middle_name"
                                                        type="text"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="ex: Casimero"
                                                        v-model="form.middle_name"
                                                        autofocus
                                                        autocomplete="middle_name"
                                                    />

                                                <InputError class="mt-2" :message="form.errors.middle_name" />
                                            </div>
                                         </div>

                                         <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <InputLabel for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Last Name" />

                                                    <TextInput
                                                        id="last_name"
                                                        type="text"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="ex: De La Cruz"
                                                        v-model="form.last_name"
                                                        required
                                                        autofocus
                                                        autocomplete="last_name"
                                                    />

                                                <InputError class="mt-2" :message="form.errors.last_name" />
                                            </div>
                                    
                                            <div>
                                                <InputLabel for="phone_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Contact" />

                                                    <TextInput
                                                        id="phone_number"
                                                        type="text"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="ex: 09112233445"
                                                        v-model="form.phone_number"
                                                        required
                                                        autocomplete="phone_number"
                                                    />

                                                <InputError class="mt-2" :message="form.errors.phone_number" />
                                             </div>
                                        </div>

        

                                            <div>
                                                <InputLabel for="email-login" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Your Email" />

                                                <TextInput
                                                    id="email-login"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="name@company.com"
                                                    v-model="form.email"
                                                    required
                                                    autofocus
                                                    autocomplete="email"
                                                />

                                                <InputError class="mt-2" :message="form.errors.email" />
                                            </div>

                                            <div>
                                                <InputLabel for="password-login" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Your Password" />

                                                <TextInput
                                                    id="password-login"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="*********"
                                                    v-model="form.password"
                                                    required
                                                    autofocus
                                                    autocomplete="current-password"
                                                />

                                                <InputError class="mt-2" :message="form.errors.password" />                                           
                                             </div>

                                             <div>
                                                <InputLabel for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Confirm Password" />

                                                    <TextInput
                                                        id="password_confirmation"
                                                        type="password"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="*********"
                                                        v-model="form.password_confirmation"
                                                        required
                                                        autocomplete="new-password"
                                                    />

                                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                            </div>

                                            <div class="mb-4">
                                                <div class="mb-4">
                                                    <div>
                                                        <div class="flex gap-2 items-center">
                                                            <InputLabel for="id_picture" class="block text-sm font-medium text-gray-900 dark:text-white" value="Valid ID" />
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" color="orange" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer hover:bg-orange-400 animate-bounce" @click="openTutModal()" >
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                                                        </svg>
                                                        </div>
                                                        <span class="text-xs  text-red-500">(ex: Passport, National Id, etc For Student: School ID)</span>
                                                    </div>
                                                </div>
                                                
                                                <input type="file" id="id_picture" class="hidden" @change="idPictureChange($event)" accept="image/*">

                                                <label for="id_picture" class="relative cursor-pointer">
                                                    <div class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg">
                                                        <img v-if="form.id_picture" :src="form.id_picture" alt="Valid ID" class="h-48 w-auto rounded-lg">
                                                        <span v-else>Select or Drag & Drop a file</span>
                                                    </div>
                                                </label>

                                                <InputError class="mt-2" :message="form.errors.id_picture" />
                                            </div>
                                            <!--Selfie-->
                                            <div class="mb-4">
                                                <div class="mb-4">
                                                    <InputLabel for="selfie_id_picture" class="block text-sm font-medium text-gray-900 dark:text-white" value="Selfie Verificaion" />
                                                    <span class="text-xs  text-red-500">Confirm your identity with a photo of yourself holding your ID</span>
                                                </div>
                                                
                                                <input type="file" id="selfie_id_picture" class="hidden" @change="SelfieidPictureChange($event)" accept="image/*">

                                                <label for="selfie_id_picture" class="relative cursor-pointer">
                                                    <div class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg">
                                                        <img v-if="form.selfie_id_picture" :src="form.selfie_id_picture" alt="Selfie with Valid ID" class="h-48 w-auto rounded-lg">
                                                        <span v-else>Select or Drag & Drop a file</span>
                                                    </div>
                                                </label>

                                                <InputError class="mt-2" :message="form.errors.selfie_id_picture" />
                                            </div>
                                            <hr class="mb-4">
                                            <div class="flex justify-between">
                                                <div class="flex items-start">
                                                    <Checkbox name="terms" v-model:checked="form.accept" />
                                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-300">I agreed to the <a @click="openTermsModal()" class="hover:underline text-orange-500">Terms and Condition</a></div>
                                                </div>
                                            </div>
                                            <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-full text-white bg-orange-500 hover:bg-orange-400 focus:ring-4 focus:outline-none focus:ring-orange-600 font-medium rounded-lg text-sm px-5 py-2.5 text-center0">Sign up</button>
                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
                                                Already registered? <a @click="openLoginModal(), closeRegisterModal()" class="hover:underline text-orange-500">Signin Now</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>

                   
                    <!-- Terms modal -->
                    <div id="defaultModal" tabindex="-1" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.7);"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="h-screen flex justify-center items-center">
                            <div class="relative w-full max-w-2xl max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow">
                                    <!-- Modal header -->
                                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-black">
                                            Terms and Condition
                                        </h3>
                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeTermsModal()">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-6 space-y-6">
                                        <Terms />
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button @click="closeTermsModal()" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
                                        <button @click="closeTermsModal()" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Decline</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                       
                    <!--Tutorial modal-->
                    <div id="tutModal" tabindex="-1" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.7);"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="h-screen flex justify-center items-center">
                            <div class="relative w-full max-w-2xl max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-gray-700 rounded-lg shadow dark:bg-white">
                                    <!-- Modal header -->
                                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-white dark:text-black">
                                            Secure User Verification: A Step-by-Step Guide to Uploading Valid ID and Selfie with Valid ID
                                        </h3>
                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeTutModal()">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-6 space-y-6">
                                        
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button @click="closeTutModal()" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I understood</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--New UI login-->
                    <div id="loginModal" tabindex="-1" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.7);"
                        class="loginmodal fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="h-screen flex justify-center items-center">
                            <div class="relative w-full max-w-md max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="authentication-modal"  @click="closeLoginModal()">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                    <div class="px-6 py-6 lg:px-8">
                                        <div class="mb-4">
                                            <h3 class="text-xl font-medium text-gray-900 dark:text-white">Welcome to Dormhub!</h3>
                                            <span class="mb-4 text-sm font-medium text-gray-900 dark:text-white">Signin to your account</span>
                                        </div>
                                        <form class="space-y-6" @submit.prevent="login">
                                            <div>
                                                <InputLabel for="email-login" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Your Email" />

                                                <TextInput
                                                    id="email-login"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="name@company.com"
                                                    v-model="loginForm.email"
                                                    required
                                                    autofocus
                                                    autocomplete="email"
                                                />

                                                <InputError class="mt-2" :message="form.errors.email" />
                                            </div>
                                            <div>
                                                <InputLabel for="password-login" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" value="Your Password" />

                                                <TextInput
                                                    id="password-login"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="*********"
                                                    v-model="loginForm.password"
                                                    required
                                                    autofocus
                                                    autocomplete="current-password"
                                                />

                                                <InputError class="mt-2" :message="form.errors.password" />                                           
                                             </div>
                                            <div class="flex justify-between">
                                                <div class="flex items-start">
                                                    <Checkbox name="remember" v-model:checked="loginForm.remember" />
                                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Remember me</span>
                                                </div>
                                                <a href="#" class="text-xs hover:underline text-red-500">Lost Password?</a>
                                            </div>
                                            <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-full text-white bg-orange-500 hover:bg-orange-400 focus:ring-4 focus:outline-none focus:ring-orange-600 font-medium rounded-lg text-sm px-5 py-2.5 text-center0">Signin</button>
                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
                                                Not registered? <a @click="openRegisterModal(), closeLoginModal()" class="hover:underline text-orange-500">Create an account</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                            
               
                
            </div>
        </div>
    </div>

</template>

<style>

.main {
    height: 100%;
    min-height: 92vh;
    background-color: #ffffff;
}

.bg-landing-page {
    background-color: #EB984E;
}

.register-btn {
    background-color: #5499C7;
    width: 150px;
}

.center {
    display: flex;
    justify-content: center;
    align-items: center;
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
</style>
