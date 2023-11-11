<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

export default{
    components:{
        AuthenticatedLayout,
    },
    setup(){

    }
}

</script>
<template>
    <AuthenticatedLayout>
        <div class="max-w-[2520px] mt-20 mx-auto xl:px-20 md:px-10 sm:px-2 px-4">
            <div
                className="
                        max-w-screen-lg
                        mx-auto
                        "
            ><p class="font-bold text-gray-900 text-2xl">My Reservation</p>
            <div class="mt-10">
                    <!--Dorm name-->
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold">Dorm 1</h3>
                    </div>
                    <p>Caloocan balayan</p>
                    <div
                        className="col-span-2 flex mt-5 flex-col md:flex-row gap-8"
                    >
                        <div>
                            <div class="md:col-span-3">
                                <img
                                    alt="dormimage"
                                    style="
                                        display: block;
                                        box-sizing: border-box;
                                        height: 404px;
                                        width: 600px;
                                    "
                                    class="rounded-md shadow-md"
                                    width="882"
                                    height="404"
                                />
                            </div>
                        </div>
                        <div class="flex-grow">
                            <div
                                className="bg-white rounded-xl border-[1px] w-full h-full shadow-lg p-4 border-neutral-200 overflow-hidden"
                            >
                                <h5 class="text-2xl font-bold">Room 1</h5>
                                <hr class="w-full my-2" />
                                <!--Room Image-->
                                <img
                                    alt="room1"
                                    style="
                                        display: block;
                                        box-sizing: border-box;
                                    "
                                    class="rounded-md shadow-md w-[350px] h-[200px]"
                                    width="882"
                                    height="404"
                                />
                                
                                <!--Features ng room-->
                                <div class="flex flex-row mt-1 gap-2">
                                    <svg
                                        width="20"
                                        height="20"
                                        stroke-width="1.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M22 3.6V11H2V3.6C2 3.26863 2.26863 3 2.6 3H21.4C21.7314 3 22 3.26863 22 3.6Z"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M18 7H19"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M2 11L2.78969 13.5844C3.04668 14.4255 3.82294 15 4.70239 15H6"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M22 11L21.2103 13.5844C20.9533 14.4255 20.1771 15 19.2976 15H18"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M9.5 14.5C9.5 14.5 9.5 21.5 6 21.5"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M14.5 14.5C14.5 14.5 14.5 21.5 18 21.5"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M12 14.5V21.5"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                    <p>Aircon</p>
                                </div>
                                <div class="flex flex-row mt-1 gap-2">
                                    <i class="fa-solid fa-users"></i>
                                    <p>Room for 1</p>
                                </div>
                                <div class="flex flex-row mt-1 gap-2">
                                    <svg
                                        height="20"
                                        width="20"
                                        version="1.1"
                                        id="Layer_1"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        viewBox="0 0 512 512"
                                        xml:space="preserve"
                                        fill="#000000"
                                    >
                                        <g
                                            id="SVGRepo_bgCarrier"
                                            stroke-width="0"
                                        ></g>
                                        <g
                                            id="SVGRepo_tracerCarrier"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        ></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g>
                                                <g>
                                                    <g>
                                                        <path
                                                            style="
                                                                fill: #010101;
                                                            "
                                                            d="M490.305,265.22H21.695C9.763,265.22,0,274.983,0,286.915v167.051 c0,11.932,9.763,21.695,21.695,21.695h33.627v14.102c0,11.932,9.763,21.695,21.695,21.695s21.695-8.678,21.695-21.695v-14.102 h314.576v14.102c0,11.932,9.763,21.695,21.695,21.695c10.847,0,20.61-8.678,21.695-21.695v-14.102h33.627 c11.932,0,20.61-9.763,21.695-21.695V286.915C512,274.983,502.237,265.22,490.305,265.22z M43.39,307.525h190.915v124.746H83.759 c-2.125-0.7-4.39-1.085-6.742-1.085s-4.617,0.384-6.742,1.085H43.39V307.525z M468.61,432.271h-26.885 c-2.125-0.7-4.39-1.085-6.742-1.085s-4.617,0.384-6.742,1.085H277.695V307.525H468.61V432.271z"
                                                        ></path>
                                                        <path
                                                            style="
                                                                fill: #010101;
                                                            "
                                                            d="M373.153,368.271h1.085c11.932,0,21.695-9.763,21.695-21.695s-9.763-21.695-21.695-21.695h-1.085 c-11.932,0-21.695,9.763-21.695,21.695S361.22,368.271,373.153,368.271z"
                                                        ></path>
                                                        <path
                                                            style="
                                                                fill: #010101;
                                                            "
                                                            d="M136.678,368.271h2.169c11.932,0,20.61-9.763,20.61-21.695s-9.763-21.695-21.695-21.695h-1.085 c-11.932,0-21.695,9.763-21.695,21.695S124.746,368.271,136.678,368.271z"
                                                        ></path>
                                                        <path
                                                            style="
                                                                fill: #010101;
                                                            "
                                                            d="M120.407,230.508h271.186c11.932,0,21.695-9.763,21.695-21.695V22.237 c0-11.932-9.763-21.695-21.695-21.695H120.407c-11.932,0-21.695,9.763-21.695,21.695v186.576 C98.712,220.746,108.475,230.508,120.407,230.508z M142.102,43.932h228.881v143.186H142.102V43.932z"
                                                        ></path>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <p>Bare</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full grid grid-cols-1 md:grid-cols-2 mt-5 gap-5 ">
                    <div class="rounded-lg  border-[1px] bg-white shadow-lg p-5">
                        <p class="text-xl font-bold text-gray-900 mb-3">Reservation Details</p>
                        <div>
                            <p>Reservation fee:</p>
                            <p>Visit Date:</p>
                            <p>Visit Time:</p>
                            <p>Reservation ends:</p>
                            <p>Days Remaining:</p>
                        </div>
                    </div>
                    <div class="rounded-lg  border-[1px] bg-white shadow-lg p-5">
                        <div class="flex flex-row items-center gap-3 mb-3">
                            <p class="text-xl font-bold text-gray-900 ">Cancellation Policy</p>
                            <p class="px-5 py-2 rounded-full text-xs text-gray-600 bg-gray-100">Non-refundable</p>
                        </div>
                        
                        <div>
                            <p>If you cancel your reservation you will not get a refund</p>
                        </div>
                        <button class="py-2 px-5 rounded-full bg-red-500 text-white font-semibold mt-5">
                            Cancel Reservation
                        </button>
                    </div>
                    
                </div>
            </div>

            <div class="mt-10">
                No reservation
            </div>
            
        </div>
    </AuthenticatedLayout>
</template>