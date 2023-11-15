<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import { usePage, router } from "@inertiajs/vue3";
import EditAboutUs from "@/Pages/Admin/EditPage/EditAboutUs.vue";
import EditContactUs from "@/Pages/Admin/EditPage/EditContactUs.vue";
import EditHeroSection from "@/Pages/Admin/EditPage/EditHeroSection.vue";
import EditPrivacyPolicy from "@/Pages/Admin/EditPage/EditPrivacyPolicy.vue";
import EditTermsUsers from "@/Pages/Admin/EditPage/EditTermsUsers.vue";
import { ref } from "vue";

export default {
    components: {
        AuthenticatedLayout,
    },
    setup() {
      

            // You can add more conditions for other reports here
        }

    }
</script>
<template>
    <AuthenticatedLayout>
        <div class="p-4 mt-16 lg:ml-64">
            <div class="">
                <div class="flex items-center gap-2 justify-start">
                    <svg
                        class="w-10 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        viewBox="0 0 384 512"
                        fill="currentColor"
                    >
                        <path
                            d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-112-112c-24.2-24.2-60.6-29-89.6-14.3l-109-109V104c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L233.7 374.3c-7.8-20.9-9-43.6-3.6-65.1l-61.7-61.7L19.9 396.1zM512 144c0-10.5-1.1-20.7-3.2-30.5c-2.4-11.2-16.1-14.1-24.2-6l-63.9 63.9c-3 3-7.1 4.7-11.3 4.7H352c-8.8 0-16-7.2-16-16V102.6c0-4.2 1.7-8.3 4.7-11.3l63.9-63.9c8.1-8.1 5.2-21.8-6-24.2C388.7 1.1 378.5 0 368 0C288.5 0 224 64.5 224 144l0 .8 85.3 85.3c36-9.1 75.8 .5 104 28.7L429 274.5c49-23 83-72.8 83-130.5zM56 432a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z"
                        />
                    </svg>
                    <h3 class="text-3xl font-bold">Site Maintenance</h3>
                </div>
                <hr class="my-5" />
                <main>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 3xl:grid-cols-5 items-center w-full gap-8 mb-40 mt-5"
                    >
                        <div
                            class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        >
                            <div
                                class="w-full flex flex-row items-center gap-5"
                            >
                                <span>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        fill="currentColor"
                                        class="bi bi-bookmark-star-fill"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                        />
                                    </svg>
                                </span>
                                <p class="text-xl">About Us</p>
                            </div>
                            <hr class="my-3" />
                            <div class="flex flex-col gap-1 text-gray-400 ml-3">
                                <p>
                                    Contains a description of your website. It
                                    serves to provide visitors with information
                                    about who you are, what you do, your
                                    mission, and any other relevant details that
                                    help them get to know you better.
                                </p>
                            </div>
                            <div class="w-full mt-3">
                                <a
                                    class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right"
                                    :href="route('aboutUs.edit')"
                                >
                                    Edit
                                </a>
                            </div>
                        </div>
                        <div
                            class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        >
                            <div
                                class="w-full flex flex-row items-center gap-5"
                            >
                                <span>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        fill="currentColor"
                                        class="bi bi-bookmark-star-fill"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                        />
                                    </svg>
                                </span>
                                <p class="text-xl">Contact Us</p>
                            </div>
                            <hr class="my-3" />
                            <div class="flex flex-col gap-1 text-gray-400 ml-3">
                                <p>
                                    Provides users with a way to get in touch
                                    with your organization or business. It's an
                                    essential part of a website as it offers a
                                    means for visitors to ask questions, seek
                                    assistance, provide feedback, or inquire
                                    about your products or services.
                                </p>
                            </div>
                            <div class="w-full mt-3">
                                <a
                                    class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right"
                                    :href="route('contactUs.edit')"
                                >
                                    Edit
                                </a>
                            </div>
                        </div>
                        <div
                            class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        >
                            <div
                                class="w-full flex flex-row items-center gap-5"
                            >
                                <span>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        fill="currentColor"
                                        class="bi bi-bookmark-star-fill"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                        />
                                    </svg>
                                </span>
                                <p class="text-xl">Help Page</p>
                            </div>
                            <hr class="my-3" />
                            <div class="flex flex-col gap-1 text-gray-400 ml-3">
                                <p>
                                    Help Page serves as your go-to resource for understanding and addressing any disruptions in our services. Explore this page to stay informed about scheduled maintenance, system updates, and troubleshooting tips, ensuring a seamless and reliable user experience on our platform.
                                </p>
                            </div>
                            <div class="w-full mt-3">
                                <a
                                    class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right"
                                    
                                >
                                    Edit
                                </a>
                            </div>
                        </div>
                        <div
                            class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        >
                            <div
                                class="w-full flex flex-row items-center gap-5"
                            >
                                <span>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        fill="currentColor"
                                        class="bi bi-bookmark-star-fill"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                        />
                                    </svg>
                                </span>
                                <p class="text-xl">Privacy Policy</p>
                            </div>
                            <hr class="my-3" />
                            <div class="flex flex-col gap-1 text-gray-400 ml-3">
                                <p>
                                    It informs users about how their personal
                                    data is collected, used, and protected. It's
                                    a legal requirement in many jurisdictions,
                                    and it helps establish trust with your
                                    audience.
                                </p>
                            </div>
                            <div class="w-full mt-3">
                                <a
                                    class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right"
                                    :href="route('policy.edit')"
                                >
                                    Edit
                            </a>
                            </div>
                        </div>
                
                        <div
                            class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        >
                            <div
                                class="w-full flex flex-row items-center gap-5"
                            >
                                <span>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        fill="currentColor"
                                        class="bi bi-bookmark-star-fill"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                        />
                                    </svg>
                                </span>
                                <p class="text-xl">
                                    Terms & Condition for Users
                                </p>
                            </div>
                            <hr class="my-3" />
                            <div class="flex flex-col gap-1 text-gray-400 ml-3">
                                <p>
                                    Terms and Conditions for users who register
                                    in your system is important to set clear
                                    expectations, define user rights and
                                    responsibilities, and protect your platform.
                                </p>
                            </div>
                            <div class="w-full mt-3">
                                <a
                                    class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right"
                                    :href="route('terms.edit')"
                                >
                                    Edit
                                </a>
                            </div>
                        </div>
                        <div
                            class="relative flex flex-col p-5 rounded-xl card-style-boxshadows group overflow-hidden shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        >
                            <div
                                class="w-full flex flex-row items-center gap-5"
                            >
                                <span>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        fill="currentColor"
                                        class="bi bi-bookmark-star-fill"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zM8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.178.178 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.178.178 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.178.178 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.178.178 0 0 1-.134-.098L8.16 4.1z"
                                        />
                                    </svg>
                                </span>
                                <p class="text-xl">Hero Header</p>
                            </div>
                            <hr class="my-3" />
                            <div class="flex flex-col gap-1 text-gray-400 ml-3">
                                <p>
                                    Hero Header is a prominent and visually
                                    appealing part of a landing page that is
                                    usually placed at the top. It's the first
                                    thing visitors see and is designed to
                                    capture their attention and communicate key
                                    messages or call-to-actions.
                                </p>
                            </div>
                            <div class="w-full mt-3">
                                <a
                                    class="border-[2px] border-orange-400 text-orange-400 px-5 py-1 rounded-md float-right"
                                    :href="route('hero.edit')"
                                >
                                    Edit
                            </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
