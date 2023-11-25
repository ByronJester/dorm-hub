<script>
import { ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import {
    endOfMonth,
    endOfYear,
    startOfMonth,
    startOfYear,
    subMonths,
} from "date-fns";

export default {
    component: {
        VueDatePicker,
    },
    setup() {
        const page = usePage();
        const user = page.props.auth.user;
        const date = ref();
        const options = ["Jear dorm", "Dorm 2"];
        const numoptions = ["5", "10", "15", "20"];
        const header=["Name", "Username", "User Type", "Contact No.","Date Registered"]

        const presetDates = ref([
            { label: "Today", value: [new Date(), new Date()] },
            {
                label: "Today (Slot)",
                value: [new Date(), new Date()],
                slot: "preset-date-range-button",
            },
            {
                label: "This month",
                value: [startOfMonth(new Date()), endOfMonth(new Date())],
            },
            {
                label: "Last month",
                value: [
                    startOfMonth(subMonths(new Date(), 1)),
                    endOfMonth(subMonths(new Date(), 1)),
                ],
            },
            {
                label: "This year",
                value: [startOfYear(new Date()), endOfYear(new Date())],
            },
        ]);
        const back = () => {
            var url = null;

            if (user) {
                router.get(route("admin.reports"));
            } else {
                router.get(route("landing.page"));
            }
        };

        const data = page.props.userArr
        return {
            date,
            presetDates,
            options,
            numoptions,
            header,
            data,
            back
        };
    },
};
</script>
<template>
    <div class="flex flex-row gap-2 items-center">
        
        <p class="text-2xl font-semibold my-4">User(s) Report</p>

    </div>

    <p class="text-lg  ">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quibusdam consequatur dolorem tempora alias quisquam quasi esse, maiores minus impedit voluptas.</p>

    <div class="w-[278px] mt-5">
        <p class="text-lg">Date Range:</p>
        <VueDatePicker
            v-model="date"
            range
            :preset-dates="presetDates"
            :enable-time-picker="false"
        >
            <template #preset-date-range-button="{ label, value, presetDate }">
                <span
                    class=""
                    role="button"
                    :tabindex="0"
                    @click="presetDate(value)"
                    @keyup.enter.prevent="presetDate(value)"
                    @keyup.space.prevent="presetDate(value)"
                >
                    {{ label }}
                </span>
            </template>
        </VueDatePicker>
    </div>

    <div class="mt-5">
        <div class="w-[278px] mt-5">
        <p class="text-lg">Position:</p>
        </div>
        <select class="px-3 py-2  focus:ring focus:outline-none rounded w-full h-12 border bg-white ">
            <option value="[object Object]">Owner</option>
            <option value="[object Object]">User</option>
            <option value="[object Object]">Admin</option></select>
        </div>    

        <div class="mt-5">
        <p class="text-lg">Department:</p>
        <input placeholder="Enter the department" type="tel" class="px-3 py-2 max-w-full focus:ring focus:outline-none rounded w-full dark:placeholder-gray-400 h-12 border bg-white ">
        </div>


    <!--Button-->
    <div class="mt-5">
        <button class="flex items-center px-3 py-2 bg-orange-400 rounded-md text-white shadow-lg font-semibold hover:bg-opacity-80">
            Generate Users Report
         </button>
    </div>


</template>
