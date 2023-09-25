<script>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

export default {
    components: {
        InputError,
        InputLabel,
        PrimaryButton,
        TextInput
    },
    setup() {
        const user = usePage().props.auth.user;

        const form = useForm({
            source_of_income: user.income_information ? user.income_information.source_of_income : '',
            proof: user.income_information ? user.income_information.proof : '',
            monthly_income: user.income_information ? user.income_information.monthly_income : '',
            monthly_expenses: user.income_information ? user.income_information.monthly_expenses : '',
        });

        const imageClick = () => {
            document.getElementById('proof').click()
        }

        const imageChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = e =>{
                form.proof = e.target.result
            }
        }

        const updateIncomeInformation = () => {
            swal({
                title: "Are you sure to update your income information?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function(){
                form.post(route('profile.income_information.update'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        swal("Success!", "Your income information has been updated.", "success");
                    },
                    onError: () => {
                    },
                });
            });
        }

        return {
            user,
            form,
            imageClick,
            imageChange,
            updateIncomeInformation
        }
    }
}

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Income Information</h2>

            <p class="mt-1 text-xs md:text-sm text-gray-600">
                Update the information below to speed up the application proccess.
            </p>

            <p class="mt-3 md:mt-1 text-xs md:text-sm text-gray-600">
                Note: Source of income proof can be payslips, business permit, (GCash, Bank, Pension) Receipt and other documents that can prove your source of income.
            </p>
        </header>

        <form @submit.prevent="updateIncomeInformation" class="mt-6 space-y-6">
            <div class="w-full flex flex-col md:flex-row mt-5 md:mt-12">
                <div class="w-full flex flex-col">
                    <div class="w-full">
                        <InputLabel for="source_of_income" value="SOURCE OF INCOME" />

                        <select v-model="form.source_of_income" class="w-full mt-1 rounded-md">
                            <option value="Salary">Salary</option>
                            <option value="Business">Business</option>
                            <option value="Pension">Pension</option>
                            <option value="Allowance">Allowance</option>
                        </select>

                        <InputError class="mt-2" :message="form.errors.source_of_income" />
                    </div>

                    <div class="w-full mt-5">
                        <InputLabel for="monthly_income" value="MONTHLY INCOME" />

                        <TextInput
                            id="monthly_income"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.monthly_income"
                            required
                        />

                        <InputError class="mt-2" :message="form.errors.monthly_income" />
                    </div>

                    <div class="w-full mt-5">
                        <InputLabel for="monthly_expenses" value="MONTHLY EXPENSES" />

                        <TextInput
                            id="monthly_expenses"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.monthly_expenses"
                            required
                        />

                        <InputError class="mt-2" :message="form.errors.monthly_expenses" />
                    </div>
                </div>

                <div class="w-full flex flex-col mt-5 md:mt-0">
                    <div class="w-full md:px-10">
                        <InputLabel for="proof" value="SOURCE OF INCOME PROOF" />

                        <input type="file" id="proof" style="display: none"
                                @change="imageChange($event)"
                        />

                        <img :src="form.proof == '' ? '/images/upload_image.png' : form.proof"
                            class="cursor-pointer"
                            @click="imageClick()"
                            style="border: 1px solid black; border-radius: 5px; width: 100%; height: 280px;"
                        >

                        <InputError class="mt-2" :message="form.errors.proof" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
