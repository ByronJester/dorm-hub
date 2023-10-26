<script>
import TenantLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage, router } from '@inertiajs/vue3'
import { ref, reactive, watch, onMounted, computed } from "vue";

export default {
    components: {
        TenantLayout,
    },
    setup() {
        const page = usePage()
        const user = page.props.auth.user;

        const room = page.props.room
        const dorm = page.props.dorm
        const action = page.props.action
        const now = page.props.now
        const expiredDate = page.props.expiredDate
        const min = page.props.min
        const max = page.props.max

        const back = () => {
            var url = null;

            if(user) {
                router.get(route('view.dorm', room.dorm_id));
            } else {
                router.get(route('landing.page'));
            }
        }

        const visitDate = ref(new Date().toISOString().split('T')[0])

        const onVisitDateChange = (e) => {
            const minDate = new Date(min);
            const maxDate = new Date(max);
            const selectedDate = new Date(e.target.value)

            if(selectedDate < minDate || selectedDate > maxDate) {
                visitDate.value = new Date().toISOString().split('T')[0]
            }  else {
                visitDate.value = e.target.value
            }
        }

        const moveInDate = ref(new Date().toISOString().split('T')[0])

        const onMoveInDateChange = (e) => {
            const minDate = new Date(min);
            const maxDate = new Date(max);
            const selectedDate = new Date(e.target.value)

            if(selectedDate < minDate) {
                moveInDate.value = new Date().toISOString().split('T')[0]
            }  else {
                moveInDate.value = e.target.value
            }
        }

        const amount_to_paid = ref(0)

        if(action == 'rent') {
            amount_to_paid.value = parseInt(room.advance) + parseInt(room.deposit)
        } else {
            amount_to_paid.value = 300
        }


        return{
            back,
            room,
            dorm,
            action,
            now,
            expiredDate,
            min,
            max,
            visitDate,
            moveInDate,
            amount_to_paid,
            onVisitDateChange,
            onMoveInDateChange
        }
},
};
</script>

<template>
    <TenantLayout>
        <div
            class="lg:pl-22 pt-14 min-h-screen w-screen transition-position lg:w-auto bg-gray-50"
        >
            <!--Di pa nagana back-->
            <section class="p-6 bg-gray-50 xl:max-w-7xl xl:mx-auto">
                <div class="w-full mb-3">
                        <p class="cursor-pointer hover:text-orange-400" @click="back()">
                            <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                        </p>
                    </div>
                <div class="flex items-center mb-6 justify-start">
                    <span
                        class="inline-flex justify-center items-center w-6 h-6 mr-2"
                        ><svg
                            viewBox="0 0 24 24"
                            width="20"
                            height="20"
                            class="inline-block"
                        >
                            <path
                                fill="currentColor"
                                d="M11,2V22C5.9,21.5 2,17.2 2,12C2,6.8 5.9,2.5 11,2M13,2V11H22C21.5,6.2 17.8,2.5 13,2M13,13V22C17.7,21.5 21.5,17.8 22,13H13Z"
                            ></path></svg></span>
                    <h1 class="text-4xl leading-tight">Billing</h1>
                </div>
                <div
                    class="rounded-2xl flex-col shadow-lg bg-white flex mb-6"
                >
                    <div class="flex-1 p-6">
                        <div class="grid grid-cols-1 items-center justify-center gap-2 md:grid-cols-4">
                            <!--Image ng Room-->
                            <div class="md:col-span-3">
                                <img
                                    :src="room.image"
                                    style="
                                        display: block;
                                        box-sizing: border-box;
                                        height: 404px;
                                        width: 882px;
                                    "
                                    width="882"
                                    height="404"
                                />
                            </div>
                            <!--Room Details -->
                            <div clas="flex p-10 flex-cols-1 items-center justify-ceneter md:flex-cols-4">
                                <div class="text-3xl mb-5">Room Details</div>
                                <div class="font-semibold">Room Name: <span class="font-light">{{ room.name }}</span></div>
                                <div class="font-semibold">Deposit fee: <span class="font-light">{{ room.deposit }}</span></div>
                                <div class="font-semibold">Advance fee: <span class="font-light">{{ room.advance }}</span></div>
                                <div class="font-semibold">Monthy fee: <span class="font-light">{{ room.fee }}</span></div>
                                <div class="font-semibold">Capacity: <span class="font-light">{{ room.type_of_room }}</span></div>
                                <div class="font-semibold">Furnished Type: <span class="font-light">{{ room.furnished_type }}</span></div>
                                <div class="font-semibold">Air Condition: <span class="font-light">{{ room.is_aircon == 'Yes' ? 'Arconditioned' : 'Not-Arconditioned'}}</span></div>
                            </div>

                        </div>
                    </div>
                    <hr class="h-px my-5 mx-5 bg-gray-200 border-0" />
                <div class="flex-1 p-6">
                    <div class="grid grid-cols-1  gap-2 md:grid-cols-2">
                        <div class="rounded-lg border border-gray-300 shadow-sm">
                            <!--Header-->
                            <div class="text-2xl p-5 border-b  border-gray-300   bg-gray-100">
                                Billing Information
                            </div>
                            <!--Body-->
                            <form class="m-8">
                                <div class="grid gap-6 mb-6 ">
                                    <div>
                                        <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900">First name</label>
                                        <input type="text" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="John" required>
                                    </div>
                                    <div>
                                        <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900">Last name</label>
                                        <input type="text" id="last_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Doe" required>
                                    </div>
                                    <div>
                                        <label for="phone_number" class="block mb-2 text-sm font-medium text-gray-900">Phone number</label>
                                        <input type="tel" id="phone_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " placeholder="123-45-678" pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}" required>
                                    </div>
                                    <!--Matic malalagyan ng data-->
                                    <div>
                                        <label for="dorm_name" class="block mb-2 text-sm font-medium text-gray-900">Dorm Name</label>
                                        <input v-model="dorm.property_name" type="text" disabled id="dorm_name" class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                                    </div>
                                    <!--Matic malalagyan ng date kung kailan nag reserve ang format Jan 12, 1999-->

                                    <div v-if="action == 'reserve'">
                                        <div>
                                            <label for="reserve_date_from" class="block mb-2 text-sm font-medium text-gray-900 ">Reservation Date From</label>
                                            <input v-model="now" type="text" disabled id="reserve_date_from" class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <!--7days dapat yung date simula from Jan 19, 1999-->
                                        <div>
                                            <label for="reserve_date_to" class="block mb-2 text-sm font-medium text-gray-900 ">Reservation Date To</label>
                                            <input v-model="expiredDate" type="text" disabled id="reserve_date_to" class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " required>
                                        </div>
                                    </div>

                                    <div v-if="action == 'rent'">
                                        <div>
                                            <label for="rent_date" class="block mb-2 text-sm font-medium text-gray-900 ">Rent Date</label>
                                            <input v-model="now" type="text" disabled id="rent_date" class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                    </div>


                                </div>
                            </form>

                        </div>
                        <div class="rounded-lg border border-gray-300 shadow-sm">
                            <div class="text-2xl p-5 border-b  border-gray-300   bg-gray-100">
                                Payment Information
                            </div>
                            <form class="m-8">
                                <div class="grid gap-6 mb-6 ">
                                    <div>
                                        <!--Papalitan pre ng date time picker ang selectable lang dapat e yung 7days na reservation from hanggan to-->
                                        <label for="date" class="block mb-2 text-sm font-medium text-gray-900 ">When will you check the {{action == 'reserve' ? 'reserved room' : 'move in'}}? </label>
                                        <input type="date" id="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                            v-model="visitDate" v-if="action == 'reserve'" @change="(e) => onVisitDateChange(e)"
                                        >

                                        <input type="date" id="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                            v-model="moveInDate" v-if="action == 'rent'" @change="(e) => onMoveInDateChange(e)"
                                        >
                                    </div>
                                    <div>
                                        <!--Pa palitan ako pre ng select-->
                                        <label for="pm" class="block mb-2 text-sm font-medium text-gray-900">Payment Method</label>
                                        <input type="text" id="pm" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Pa palitan ako pre ng select" required>
                                    </div>
                                    <div>
                                        <!--300 sa reservation sa rent e advance + deposit-->
                                        <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">Amount to be paid:</label>
                                        <input v-model="amount_to_paid" disabled type="text" id="amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " placeholder="300 sa reservation sa rent e advance + deposit"  required>
                                    </div>


                                </div>

                                <button class="text-white float-right bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-3 mb-5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Proceed to Payment</button>
                            </form>
                        </div>
                    </div>
                </div>

                </div>
                <!--Billing Information-->


            </section>
        </div>
    </TenantLayout>
</template>
