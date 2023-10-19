<script>
    import TenantLayout from '@/Layouts/AuthenticatedLayout.vue';
    import OtherLayout from '@/Layouts/SidebarLayout.vue';
    import { usePage, Head, Link, router, useForm  } from '@inertiajs/vue3'
    import Dorm from '@/Components/Dorm.vue';
    import { ref } from 'vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import Checkbox from '@/Components/Checkbox.vue';

    export default {
        components: {
            Dorm,
            ApplicationLogo,
            Link,
            InputLabel,
            PrimaryButton,
            TextInput,
            InputError,
            Checkbox,
            TenantLayout,
            OtherLayout
        },
        setup() {
            const page = usePage()

            const showingNavigationDropdown = ref(false);
            const user = page.props.auth.user;
            const dorm = page.props.dorm;

            const back = () => {
                var url = null;

                if(user) {
                    url = user.user_type + '.dorms'
                } else {
                    url = 'landing.page'
                }

                router.get(route(url));
            }

            const openRegisterModal = () => {
                var modal = document.getElementById("registerModal");

                modal.style.display = "block";

                form.reset()
            }

            const closeRegisterModal = () => {
                var modal = document.getElementById("registerModal");

                modal.style.display = "none";
            }

            const openLoginModal = () => {
                var modal = document.getElementById("loginModal");

                modal.style.display = "block";

                loginForm.reset()
            }

            const closeLoginModal = () => {
                var modal = document.getElementById("loginModal");

                modal.style.display = "none";
            }

            const isMobileView = ref(false)

            isMobileView.value = screen.width < 600;

            const form = useForm({
                first_name: '',
                middle_name: '',
                last_name: '',
                phone_number: '',
                email: '',
                user_type: 'tenant',
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
                user,
                dorm,
                showingNavigationDropdown,
                isMobileView,
                form,
                loginForm,
                idPictureClick,
                idPictureChange,
                submit,
                back,
                openRegisterModal,
                closeRegisterModal,
                openLoginModal,
                closeLoginModal,
                login
            }
        }
    }
</script>


<template>
    <div>
        <div class="w-full" v-if="user">
            <TenantLayout v-if="user.user_type == 'tenant'">
                <div class="max-w-[2520px] mx-auto xl:px-20 md:px-10 sm:px-2 px-4">
                    <div 
                        className="
                        max-w-screen-lg 
                        mx-auto
                        "
                    >
                    <div class="w-full pt-24 pl-5">
                        <p class="cursor-pointer" @click="back()">
                            <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                        </p>
                    </div>

                    <div class="w-full mt-5">
                        <Dorm :dorm.sync="dorm" :user.sync="user" ></Dorm>
                    </div>
                    </div>
                </div>
            </TenantLayout>


            <OtherLayout v-else>
                <div class="p-4 lg:ml-64">
                    <div 
                        className="
                        max-w-screen-lg 
                        mx-auto
                        "
                    >
                    <div class="w-full pt-24 pl-5">
                        <p class="cursor-pointer" @click="back()">
                            <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                        </p>
                    </div>

                    <div class="w-full mt-5">
                        <Dorm :dorm.sync="dorm" :user.sync="user" ></Dorm>
                    </div>
                </div>
                </div>
            </OtherLayout>
        </div>


        <div class="bg-white w-full flex flex-col" style="min-height: 100vh;" v-else>
            <nav
                class="fixed top-0 z-50 w-full bg-white shadow-md border-gray-200 dark:bg-gray-800 dark:border-gray-700"
            >
                <div class="py-4 border-b-[1px]">
                    <div
                        class="max-w-[2520px] mx-auto xl:px-20 md:px-10 sm:px-2 px-4"
                    >
                        <div
                            class="flex flex-row items-center justify-between gap-3 md:gap-0"
                        >
                            <div class="flex items-center justify-start">
                                <a href="/">
                                    <ApplicationLogo />
                                </a>
                            </div>
                            <div class="flex items-center ">
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        @click.prevent="openLoginModal()"
                                        class="bg-transparent hover:bg-orange-400 text-orange-400 font-semibold hover:text-white py-2 px-4 border border-orange-400 hover:border-transparent rounded"
                                    >
                                        Signin
                                    </button>

  
                                    <button
                                        @click.prevent="openRegisterModal()"
                                        class="bg-orange-400 hover:bg-transparent text-white font-semibold hover:text-orange-400 py-2 px-4 border border-transparent hover:border-orange-400 rounded"
                                    >
                                        Signup
                                    </button>
                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!--New UI register modal-->
                <div
                    id="registerModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="registerModal fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <div
                                class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                            >
                                <button
                                    type="button"
                                    class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-modal-hide="authentication-modal"
                                    @click="closeRegisterModal()"
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
                                <div class="px-6 py-6 lg:px-8">
                                    <div class="mb-4">
                                        <h3
                                            class="text-xl font-medium text-gray-900 dark:text-white"
                                        >
                                            Welcome to Dormhub!
                                        </h3>
                                        <span
                                            class="mb-4 text-sm font-medium text-gray-900 dark:text-white"
                                            >Create an account</span
                                        >
                                    </div>

                                    <hr class="mb-4" />
                                    <form
                                        class="space-y-6"
                                        @submit.prevent="submit"
                                    >
                                        <div class="mb-4">
                                            <InputLabel
                                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                value="Register as ?"
                                            />
                                            <div class="grid grid-cols-2 gap-2">
                                                <button
                                                    @click="
                                                        selectUserType('tenant')
                                                    "
                                                    :class="{
                                                        'bg-orange-500':
                                                            form.user_type ===
                                                            'tenant',
                                                        'bg-transparent':
                                                            form.user_type !==
                                                            'tenant',
                                                    }"
                                                    class="px-4 py-2 rounded-lg border hover:bg-orange-400 text-black dark:text-white"
                                                >
                                                    Tenant
                                                </button>
                                                <button
                                                    @click="
                                                        selectUserType('owner')
                                                    "
                                                    :class="{
                                                        'bg-orange-500':
                                                            form.user_type ===
                                                            'owner',
                                                        'bg-transparent':
                                                            form.user_type !==
                                                            'owner',
                                                    }"
                                                    class="px-4 py-2 rounded-lg border hover:bg-orange-400 text-black dark:text-white"
                                                >
                                                    Dorm Owner
                                                </button>
                                            </div>
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.user_type"
                                            />
                                        </div>
                                        <hr class="mb-4" />
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <InputLabel
                                                    for="first_name"
                                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                    value="First Name"
                                                />

                                                <TextInput
                                                    id="first_name"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                    placeholder="ex: Juan"
                                                    v-model="form.first_name"
                                                    required
                                                    autofocus
                                                    autocomplete="first_name"
                                                />

                                                <InputError
                                                    class="mt-2"
                                                    :message="
                                                        form.errors.first_name
                                                    "
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="middle_name"
                                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                    value="Middle Name"
                                                />

                                                <TextInput
                                                    id="middle_name"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                    placeholder="ex: Casimero"
                                                    v-model="form.middle_name"
                                                    autofocus
                                                    autocomplete="middle_name"
                                                />

                                                <InputError
                                                    class="mt-2"
                                                    :message="
                                                        form.errors.middle_name
                                                    "
                                                />
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <InputLabel
                                                    for="last_name"
                                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                    value="Last Name"
                                                />

                                                <TextInput
                                                    id="last_name"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                    placeholder="ex: De La Cruz"
                                                    v-model="form.last_name"
                                                    required
                                                    autofocus
                                                    autocomplete="last_name"
                                                />

                                                <InputError
                                                    class="mt-2"
                                                    :message="
                                                        form.errors.last_name
                                                    "
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="phone_number"
                                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                    value="Contact"
                                                />

                                                <TextInput
                                                    id="phone_number"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                    placeholder="ex: 09112233445"
                                                    v-model="form.phone_number"
                                                    required
                                                    autocomplete="phone_number"
                                                />

                                                <InputError
                                                    class="mt-2"
                                                    :message="
                                                        form.errors.phone_number
                                                    "
                                                />
                                            </div>
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="email-login"
                                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                value="Your Email"
                                            />

                                            <TextInput
                                                id="email-login"
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                placeholder="name@company.com"
                                                v-model="form.email"
                                                required
                                                autofocus
                                                autocomplete="email"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.email"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="password-login"
                                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                value="Your Password"
                                            />

                                            <TextInput
                                                id="password-login"
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                placeholder="*********"
                                                v-model="form.password"
                                                required
                                                autofocus
                                                autocomplete="current-password"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.password"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                for="password_confirmation"
                                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                value="Confirm Password"
                                            />

                                            <TextInput
                                                id="password_confirmation"
                                                type="password"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                placeholder="*********"
                                                v-model="
                                                    form.password_confirmation
                                                "
                                                required
                                                autocomplete="new-password"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors
                                                        .password_confirmation
                                                "
                                            />
                                        </div>

                                        <div class="mb-4">
                                            <div class="mb-4">
                                                <div>
                                                    <div
                                                        class="flex gap-2 items-center"
                                                    >
                                                        <InputLabel
                                                            for="id_picture"
                                                            class="block text-sm font-medium text-gray-900 dark:text-white"
                                                            value="Valid ID"
                                                        />
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            color="orange"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="1.5"
                                                            stroke="currentColor"
                                                            class="w-5 h-5 cursor-pointer hover:bg-orange-400 animate-bounce"
                                                            @click="
                                                                openTutModal()
                                                            "
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                                                            />
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs text-red-500"
                                                        >(ex: Passport, National
                                                        Id, etc For Student:
                                                        School ID)</span
                                                    >
                                                </div>
                                            </div>

                                            <input
                                                type="file"
                                                id="id_picture"
                                                class="hidden"
                                                @change="
                                                    idPictureChange($event)
                                                "
                                                accept="image/*"
                                            />

                                            <label
                                                for="id_picture"
                                                class="relative cursor-pointer"
                                            >
                                                <div
                                                    class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                >
                                                    <img
                                                        v-if="form.id_picture"
                                                        :src="form.id_picture"
                                                        alt="Valid ID"
                                                        class="h-48 w-auto rounded-lg"
                                                    />
                                                    <span v-else
                                                        >Select or Drag & Drop a
                                                        file</span
                                                    >
                                                </div>
                                            </label>

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.id_picture
                                                "
                                            />
                                        </div>
                                        <!--Selfie-->
                                        <div class="mb-4">
                                            <div class="mb-4">
                                                <InputLabel
                                                    for="selfie_id_picture"
                                                    class="block text-sm font-medium text-gray-900 dark:text-white"
                                                    value="Selfie Verificaion"
                                                />
                                                <span
                                                    class="text-xs text-red-500"
                                                    >Confirm your identity with
                                                    a photo of yourself holding
                                                    your ID</span
                                                >
                                            </div>

                                            <input
                                                type="file"
                                                id="selfie_id_picture"
                                                class="hidden"
                                                @change="
                                                    SelfieidPictureChange(
                                                        $event
                                                    )
                                                "
                                                accept="image/*"
                                            />

                                            <label
                                                for="selfie_id_picture"
                                                class="relative cursor-pointer"
                                            >
                                                <div
                                                    class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                >
                                                    <img
                                                        v-if="
                                                            form.selfie_id_picture
                                                        "
                                                        :src="
                                                            form.selfie_id_picture
                                                        "
                                                        alt="Selfie with Valid ID"
                                                        class="h-48 w-auto rounded-lg"
                                                    />
                                                    <span v-else
                                                        >Select or Drag & Drop a
                                                        file</span
                                                    >
                                                </div>
                                            </label>

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors
                                                        .selfie_id_picture
                                                "
                                            />
                                        </div>
                                        <hr class="mb-4" />
                                        <div class="flex justify-between">
                                            <div class="flex items-start">
                                                <Checkbox
                                                    name="terms"
                                                    v-model:checked="
                                                        form.accept
                                                    "
                                                />
                                                <div
                                                    class="text-sm font-medium text-gray-500 dark:text-gray-300"
                                                >
                                                    I agreed to the
                                                    <a
                                                        @click="
                                                            openTermsModal()
                                                        "
                                                        class="hover:underline text-orange-500"
                                                        >Terms and Condition</a
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            :class="{
                                                'opacity-25': form.processing,
                                            }"
                                            :disabled="form.processing"
                                            class="w-full text-white bg-orange-500 hover:bg-orange-400 focus:ring-4 focus:outline-none focus:ring-orange-600 font-medium rounded-lg text-sm px-5 py-2.5 text-center0"
                                        >
                                            Sign up
                                        </button>
                                        <div
                                            class="text-sm font-medium text-gray-500 dark:text-gray-300"
                                        >
                                            Already registered?
                                            <a
                                                @click="
                                                    openLoginModal(),
                                                        closeRegisterModal()
                                                "
                                                class="hover:underline text-orange-500"
                                                >Signin Now</a
                                            >
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms modal -->
                <div
                    id="defaultModal"
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
                                        Terms and Condition
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeTermsModal()"
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
                                    <Terms />
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                >
                                    <button
                                        @click="closeTermsModal()"
                                        type="button"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        I accept
                                    </button>
                                    <button
                                        @click="closeTermsModal()"
                                        type="button"
                                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                                    >
                                        Decline
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tutorial modal-->
                <div
                    id="tutModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div
                                class="relative bg-gray-700 rounded-lg shadow dark:bg-white"
                            >
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-white dark:text-black"
                                    >
                                        Secure User Verification: A Step-by-Step
                                        Guide to Uploading Valid ID and Selfie
                                        with Valid ID
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeTutModal()"
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
                                <div class="p-6 space-y-6"></div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                >
                                    <button
                                        @click="closeTutModal()"
                                        type="button"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        I understood
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--New UI login-->
                <div
                    id="loginModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="loginmodal fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-md max-h-full">
                            <div
                                class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                            >
                                <button
                                    type="button"
                                    class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-modal-hide="authentication-modal"
                                    @click="closeLoginModal()"
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
                                <div class="px-6 py-6 lg:px-8">
                                    <div class="mb-4">
                                        <h3
                                            class="text-xl font-medium text-gray-900 dark:text-white"
                                        >
                                            Welcome to Dormhub!
                                        </h3>
                                        <span
                                            class="mb-4 text-sm font-medium text-gray-900 dark:text-white"
                                            >Signin to your account</span
                                        >
                                    </div>
                                    <form
                                        class="space-y-6"
                                        @submit.prevent="login"
                                    >
                                        <div>
                                            <InputLabel
                                                for="email-login"
                                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                value="Your Email"
                                            />

                                            <TextInput
                                                id="email-login"
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                placeholder="name@company.com"
                                                v-model="loginForm.email"
                                                required
                                                autofocus
                                                autocomplete="email"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.email"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel
                                                for="password-login"
                                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                value="Your Password"
                                            />

                                            <TextInput
                                                id="password-login"
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                                placeholder="*********"
                                                v-model="loginForm.password"
                                                required
                                                autofocus
                                                autocomplete="current-password"
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.password"
                                            />
                                        </div>
                                        <div class="flex justify-between">
                                            <div class="flex items-start">
                                                <Checkbox
                                                    name="remember"
                                                    v-model:checked="
                                                        loginForm.remember
                                                    "
                                                />
                                                <span
                                                    class="ml-2 text-sm text-gray-600 dark:text-gray-300"
                                                    >Remember me</span
                                                >
                                            </div>
                                            <a
                                                href="#"
                                                class="text-xs hover:underline text-red-500"
                                                >Lost Password?</a
                                            >
                                        </div>
                                        <button
                                            :class="{
                                                'opacity-25': form.processing,
                                            }"
                                            :disabled="form.processing"
                                            class="w-full text-white bg-orange-500 hover:bg-orange-400 focus:ring-4 focus:outline-none focus:ring-orange-600 font-medium rounded-lg text-sm px-5 py-2.5 text-center0"
                                        >
                                            Signin
                                        </button>
                                        <div
                                            class="text-sm font-medium text-gray-500 dark:text-gray-300"
                                        >
                                            Not registered?
                                            <a
                                                @click="
                                                    openRegisterModal(),
                                                        closeLoginModal()
                                                "
                                                class="hover:underline text-orange-500"
                                                >Create an account</a
                                            >
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="max-w-[2520px] xl:px-20 md:px-10 sm:px-2 px-4">
                <div class="w-full pt-24 pl-5">
                    <p class="cursor-pointer" @click="back()">
                        <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                    </p>
                </div>
                <Dorm :dorm.sync="dorm" :user.sync="user" ></Dorm>
            </div>
        </div>
    </div>
</template>

<style>
    .main {
        height: 100%;
        background-color: #E5E8E8;
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

    .registerModal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 20px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .register-modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 100%;
    }

    .loginModal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 20px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .login-modal-content {
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
</style>
