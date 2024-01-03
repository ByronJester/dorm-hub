<script>
import Rooms from "@/Components/RoomCard.vue";
import SidebarLayout from "@/Layouts/SidebarLayout.vue";
import { usePage, router } from "@inertiajs/vue3";
import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
import Card from "primevue/card";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
export default {
    components: {
        Rooms,
        SidebarLayout,
        Card,
        TabView,
        TabPanel
    },
    setup() {
        const page = usePage();
        const dorm = page.props.myDorm;
        const user = page.props.user;
        const myApplication = page.props.myApplication;
        const active = ref(0);

        const viewRooms = (arg) => {
            console.log(arg)
            router.get(route('tenant.mydorm', [arg.room_id, arg.profile_id]));
        }

        console.log(myApplication)

        return {
            active,
            dorm,
            user,
            viewRooms,
            myApplication,
        };
    },
};
</script>
<template>
    <SidebarLayout>
        <div class="p-4 lg:ml-64">
            <p class="text-4xl mb-5 mt-20  font-bold">Rooms and Application</p>
            <hr class="my-2"/>
            <div class="card">
            <TabView>
                <TabPanel>
                    <template #header >
                        <button @click="active = 0" class="p-2" :class="{
                            'border-b border-px border-orange-400 text-orange-400 font-bold' : active == 0
                        }"
                        >My Rooms</button>
                    </template>
                    <div v-if="dorm.length > 0">
                        <div
                            class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 mx-3 mb-10 gap-2"
                        >
                            <div
                                v-for="(myDorm, index) in dorm"
                                :key="index"
                            >
                                <Card style="width: 30em; cursor: pointer;" >
                                    <template #header>
                                        <img
                                            alt="user header"
                                            :src="myDorm.room.image"
                                            class="h-[300px] w-full rounded-t-lg"
                                            @click="viewRooms(myDorm)"
                                        />
                                        <div class="w-full text-center text-white bg-orange-400">
                                            Room for {{ myDorm.profile.first_name + ' ' + myDorm.profile.last_name}}
                                        </div>
                                    </template>
                                    <template #title>
                                        {{ myDorm.room.name }}
                                        <p class="text-lg">
                                            ₱ {{ parseFloat(myDorm.room.fee).toFixed(2) }}/ a
                                            month
                                        </p>
                                    </template>
                                    <template #content class="py-1">
                                        <div class="font-light">
                                            Dorm Name:
                                            <span>{{ myDorm.dorm.property_name }}</span>
                                        </div>
                                        <div class="font-semibold">
                                            Deposit fee:
                                            <span class="font-light">{{
                                                myDorm.room.deposit
                                            }}</span>
                                        </div>
                                        <div class="font-semibold">
                                            Advance fee:
                                            <span class="font-light">
                                                ₱
                                                {{
                                                    parseFloat(myDorm.room.advance).toFixed(2)
                                                }}
                                            </span>
                                        </div>
                                        <!--Capacity-->
                                        <div class="font-semibold">
                                            <div class="flex flex-row mt-1 gap-2">
                                                <i class="fa-solid fa-users"></i>
                                                <span class="font-light"
                                                    >{{
                                                        myDorm.room.type_of_room
                                                    }}
                                                    person(s)</span
                                                >
                                            </div>
                                        </div>

                                        <div class="font-semibold">
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
                                                <span class="font-light">{{
                                                    myDorm.room.furnished_type
                                                }}</span>
                                            </div>
                                        </div>

                                        <!--Furnished Description-->
                                        <div class="font-bold">
                                            Furnished Description:
                                            <span class="font-light">{{ myDorm.room.furnished_desc }}</span>
                                        </div>

                                        <!--Aircon-->
                                        <div class="font-semibold">
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

                                                <span class="font-light">{{
                                                    myDorm.room.is_aircon == "Yes"
                                                        ? "Airconditioned"
                                                        : "Not-Airconditioned"
                                                }}</span>
                                            </div>
                                        </div>


                                    </template>
                                </Card>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center min-h-screen">
                        <p class="text-xl"> You don't have leasing yet </p>
                    </div>
                </TabPanel>
                <TabPanel>
                    <template #header>
                        <button @click="active = 1" class="p-2 ml-5" :class="{
                            'border-b border-px border-orange-400 text-orange-400 font-bold' : active == 1
                        }"
                        >My Applications</button>
                    </template>
                    <div v-if="myApplication.length > 0">
                        <div
                            class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 mx-3 mb-10 gap-2"
                        >
                            <div
                                v-for="(myDorm, index) in myApplication"
                                :key="index"
                            >
                                <Card style="width: 30em; cursor: pointer;" >
                                    <template #header>
                                        <img
                                            alt="user header"
                                            :src="myDorm.room.image"
                                            class="h-[300px] w-full rounded-t-lg"
                                        />
                                        <div v-if="myDorm.status=='pending'" class="w-full text-center text-sm py-2 font-bold text-white bg-orange-400">
                                            PENDING
                                        </div>
                                        <div v-if="myDorm.status=='declined'" class="w-full text-center text-sm py-2 font-bold text-white bg-red-400">
                                            This application is decline. Reaason: {{ myDorm.reason }}
                                        </div>
                                    </template>
                                    <template #title>
                                        {{ myDorm.room.name }}
                                        <p class="text-lg">
                                            ₱ {{ parseFloat(myDorm.room.fee).toFixed(2) }}/ a
                                            month
                                        </p>
                                    </template>
                                    <template #content class="py-1">
                                        <div class="font-light">
                                            Dorm Name:
                                            <span>{{ myDorm.dorm.property_name }}</span>
                                        </div>
                                        <div class="font-semibold">
                                            Deposit fee:
                                            <span class="font-light">{{
                                                myDorm.room.deposit
                                            }}</span>
                                        </div>
                                        <div class="font-semibold">
                                            Advance fee:
                                            <span class="font-light">
                                                ₱
                                                {{
                                                    parseFloat(myDorm.room.advance).toFixed(2)
                                                }}
                                            </span>
                                        </div>
                                        <!--Capacity-->
                                        <div class="font-semibold">
                                            <div class="flex flex-row mt-1 gap-2">
                                                <i class="fa-solid fa-users"></i>
                                                <span class="font-light"
                                                    >{{
                                                        myDorm.room.type_of_room
                                                    }}
                                                    person(s)</span
                                                >
                                            </div>
                                        </div>

                                        <div class="font-semibold">
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
                                                <span class="font-light">{{
                                                    myDorm.room.furnished_type
                                                }}</span>
                                            </div>
                                        </div>

                                        <!--Aircon-->
                                        <div class="font-semibold">
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

                                                <span class="font-light">{{
                                                    myDorm.room.is_aircon == "Yes"
                                                        ? "Arconditioned"
                                                        : "Not-Arconditioned"
                                                }}</span>
                                            </div>
                                        </div>




                                    </template>
                                </Card>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center min-h-screen">
                            <p class="text-xl">You don't have application</p>
                    </div>

                </TabPanel>
            </TabView>
        </div>

        </div>
    </SidebarLayout>
</template>
