<script>
import { ref } from "vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { endOfMonth, endOfYear, startOfMonth, startOfYear, subMonths } from 'date-fns';

export default {
    component: {
        VueDatePicker,
    },
    setup() {
        const date = ref();

        const presetDates = ref([
            { label: 'Today', value: [new Date(), new Date()] },
            {
                label: 'Today (Slot)',
                value: [new Date(), new Date()],
                slot: 'preset-date-range-button'
            },
            { label: 'This month', value: [startOfMonth(new Date()), endOfMonth(new Date())] },
            {
                label: 'Last month',
                value: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))],
            },
            { label: 'This year', value: [startOfYear(new Date()), endOfYear(new Date())] },
        ]);
            return {
            date,
            presetDates,
        };
    },
};
</script>
<template>
    <div>
        <p class="text-2xl font-semibold my-4">Income Report</p>
    </div>
    <div class="flex flex-row items-center justify-between gap-2 w-full">
        <div class="flex flex-row float-right gap-2">
            <div>
                <p class="text-sm">Dorm:</p>
                <select
                    id="subject"
                    class="block w-56 px-4 py-1.5 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                >
                    <option v-for="option in options" :key="option">
                        {{ option }}
                    </option>
                </select>
            </div>

            <div class="w-96">
                <p class="text-sm">Date Range:</p>
                <VueDatePicker v-model="date" range :preset-dates="presetDates" :enable-time-picker="false">
                    <template #preset-date-range-button="{ label, value, presetDate }">
                    <span 
                        class=""
                        role="button"
                        :tabindex="0"
                        @click="presetDate(value)"
                        @keyup.enter.prevent="presetDate(value)"
                        @keyup.space.prevent="presetDate(value)">
                    {{ label }}
                    </span>
                </template>
                </VueDatePicker>
            </div>
        </div>
    </div>
</template>
