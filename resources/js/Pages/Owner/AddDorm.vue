<script>
import { ref } from "vue";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

export default {
    components: {
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        ApplicationLogo,
    },
    setup() {
        const active = ref(0);
        const loading = ref(false);
        const steps = ["Aray", "Naku"]
        const termsAndCondition = ref([]);
        const setCurrentStep = (index) => {
            // This method allows you to set the active step
            this.active = index;
        }

        return {
            steps,
            active,
            loading,
            termsAndCondition,
            setCurrentStep
        };
    },
};
</script>

<template>
    <nav
        class="fixed top-0 z-50 w-full bg-white shadow-md dark:bg-gray-800 dark:border-gray-700"
    >
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center px-10 justify-between">
                <ApplicationLogo />
                <div class="flex items-center">
                    <div class="flex items-center ml-3">
                        <AppDropdown>
                            <button
                                type="button"
                                class="flex text-sm text-black dark:text-white hover:text-gray-400"
                                aria-expanded="false"
                            >
                                {{ $page.props.auth.user.first_name }}
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
    </nav>
    <div class="max-w-[2520px] mt-16 xl:px-20 md:px-10 sm:px-2 px-4">
        <div class="max-w-screen-lg mx-auto">
            <div class="w-full overflow-y-scroll">
                <div class="mt-5">
                    <ol
                        class="flex items-center w-full text-sm font-medium text-center text-gray-500 dark:text-gray-400 sm:text-base"
                    >
                        <li
                            v-for="(step, index) in steps"
                            :key="index"
                            :class="[
                                'flex md:w-full items-center',
                                { 'text-blue-600': active === index },
                                'dark:text-blue-500',
                                'sm:after:content-[\'\'] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700',
                            ]"
                            @click="setCurrentStep(index)"
                        >
                            <span
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500"
                            >
                                <svg
                                    class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-2.5"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"
                                    />
                                </svg>
                                {{ step }}
                            </span>
                        </li>
                    </ol>
                </div>
            </div>
            <div
                class="w-full flex justify-center items-center"
                v-if="active == 0"
            >
                <div class="" style="height: 300px; width: 300px">
                    <img
                        src="/images/logo.png"
                        alt="logo"
                        style="width: 100%"
                        class="mt-10"
                    />
                    <p class="text-2xl text-center mt-5 font-fold">
                        Welcome to Dormhub!
                    </p>

                    <p class="text-md text-center mt-5 font-fold">
                        Register your dorm free by answering the following
                        questions.
                    </p>
                </div>
            </div>
            <div v-if="active == 0" class="absolute bottom-20 w-full mb-10">
                <div class="w-full mt-2">
                    <input
                        type="checkbox"
                        value="I guarantee that all details provided are accurate and true."
                        v-model="termsAndCondition"
                    />
                    <label class="ml-2 text-sn"
                        >* I guarantee that all details provided are accurate
                        and true.</label
                    >
                </div>

                <div class="w-full mt-2">
                    <input
                        type="checkbox"
                        value="I agree with the terms and condition."
                        v-model="termsAndCondition"
                    />
                    <label class="ml-2 text-sm"
                        >* I agree with the
                        <span
                            class="text-orange-400 cursor-pointer hover:underline"
                            >terms and condition</span
                        ></label
                    >
                </div>
            </div>
            <!--Button-->
            <div
                class="absolute bottom-10 items-center mt-10 space-x-2 border-gray-200 rounded-b dark:border-gray-600"
            >
                <div v-if="active == 0">
                    <button
                        data-modal-hide="defaultModal"
                        type="button"
                        :disabled="termsAndCondition.length < 2"
                        :class="{
                            'cursor-not-allowed': termsAndCondition.length < 2,
                            'bg-cyan-500 text-white':
                                termsAndCondition.length == 2,
                        }"
                        @click="active = 1"
                        class="border text-black focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    >
                        Start
                    </button>
                </div>

                <div class="w-full" v-else>
                    <button
                        data-modal-hide="defaultModal"
                        v-if="active > 1"
                        :disabled="active == 1"
                        :class="{
                            'cursor-not-allowed': active == 1,
                        }"
                        @click="active > 1 ? active-- : ''"
                        type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        Back
                    </button>
                    <button
                        data-modal-hide="defaultModal"
                        v-if="active < 6"
                        @click="active++"
                        type="button"
                        class="text-gray-500 float-right bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        Next
                    </button>
                    <button
                        data-modal-hide="defaultModal"
                        v-if="active == 6"
                        @click=""
                        type="button"
                        :disabled="loading"
                        :class="{ 'cursor-not-allowed': loading }"
                        class="text-gray-500 float-right bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        {{ !!loading ? "Saving..." : "Submit" }}
                        <pulse-loader :loading="loading"></pulse-loader>
                    </button>
                </div>
            </div>
        </div>
        <div class="flex flex-row items-center justify-center"></div>
    </div>
</template>
