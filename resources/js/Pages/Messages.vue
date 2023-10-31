<script>
import TenantLayout from "@/Layouts/AuthenticatedLayout.vue";
import OtherLayout from "@/Layouts/SidebarLayout.vue";
import { usePage, Head, Link, router, useForm } from "@inertiajs/vue3";
import Dorm from "@/Components/Dorm.vue";
import { ref, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
    components: {
        TenantLayout,
        OtherLayout,
    },
    setup() {
        const showSidebar = ref(false);

        const clickShowSideBar = () => {
            showSidebar.value = !showSidebar.value;
        };
        const page = usePage();
        const user = page.props.auth.user;
        const threads = ref([]);
        const selectedThread = ref(null);
        const isMobileView = ref(false);
        const messageToSend = ref(null);

        isMobileView.value = screen.width < 600;

        if (!isMobileView.value) {
            selectedThread.value = page.props.threads.length
                ? page.props.threads[0]
                : null;
        }

        threads.value = page.props.threads;

        const selectThread = (thread) => {
            selectedThread.value = thread;

            setTimeout(() => {
                const lastChildElement = messageDiv.value.lastElementChild;

                lastChildElement?.scrollIntoView({
                    behavior: "smooth",
                });
            }, 50);
        };

        const sendMessage = () => {
            const data = {
                thread_id: selectedThread.value.id,
                message: messageToSend.value,
            };

            axios.post(route("send.message", data)).then((response) => {
                selectedThread.value = response.data;
                messageToSend.value = null;

                setTimeout(() => {
                    const lastChildElement = messageDiv.value.lastElementChild;

                    lastChildElement?.scrollIntoView({
                        behavior: "smooth",
                    });
                }, 50);
            });
        };

        const timer = ref(null);

        const fetchMessages = () => {
            axios.get(route("fetch.messages")).then((response) => {
                threads.value = response.data;
                selectedThread.value = response.data.filter((x) => {
                    return x.id == selectedThread.value.id;
                })[0];

                setTimeout(() => {
                    const lastChildElement = messageDiv.value.lastElementChild;

                    lastChildElement?.scrollIntoView({
                        behavior: "smooth",
                    });
                }, 50);
            });
        };

        const messageDiv = ref();

        onMounted(() => {
            clearInterval(timer.value);

            timer.value = setInterval(() => {
                fetchMessages();
            }, 3000);

            setTimeout(() => {
                const lastChildElement = messageDiv.value.lastElementChild;

                lastChildElement?.scrollIntoView({
                    behavior: "smooth",
                });
            }, 50);
        });

        return {
            user,
            isMobileView,
            threads,
            selectedThread,
            messageToSend,
            messageDiv,
            selectThread,
            sendMessage,
            clickShowSideBar,
        };
    },
};
</script>

<template>  
      <div>
        <TenantLayout v-if="user.user_type == 'tenant'">
            <div class="px-4 pt-14 lg:ml-64">
            <div class="w-full flex flex-col mt-16 overflow-y-hidden main">
                <div class="flex flex-row" v-if="!isMobileView">
                    <div class="w-3/12 border-r flex flex-col main">
                        <div class="w-full border-b py-5" v-for="thread in threads"
                            :class="{
                                'bg-gradient-to-r from-cyan-500 to-gray-400': thread.id == selectedThread.id,
                            }"
                        >
                            <p class="cursor-pointer text-center" @click="selectThread(thread)">
                                {{
                                    user.user_type  == 'owner' ? thread.tenant.name : thread.owner.name
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="w-9/12 main">
                        <div class="w-full border-b" style="height: 7.2vh;" v-if="selectedThread">
                            <p class="p-4 font-bold">
                                {{
                                    user.user_type  == 'owner' ? selectedThread.tenant.name : selectedThread.owner.name
                                }}
                            </p>
                        </div>

                        <div style="height: 77vh;" class="overflow-y-scroll flex flex-col" ref="messageDiv" v-if="selectedThread">
                            <div class="w-full px-2" v-for="m in selectedThread.messages">
                                <p class="break-words my-2 bg-gray-300 p-3 text-sm"
                                    style="width: 400px; border-radius: 10px;"
                                    :class="{
                                        'float-right': user.id == m.user_id,
                                    }"
                                >
                                    {{ m.message }}
                                </p>
                            </div>
                        </div>

                        <div class="w-full px-2" style="height: 10%;" v-if="selectedThread">
                            <input type="text" placeholder="Type your message hawdere..."
                                class="message-input py-2"
                                style="width: 90%;"
                                v-model="messageToSend"
                                @keyup.enter="sendMessage()"
                            />

                            <button class="send-btn bg-cyan-500 py-2" style="width: 10%;"
                                @click="sendMessage()"
                            >
                                Send
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else>
                    <div class="w-full flex flex-col" v-if="!selectedThread">
                        <div class="w-full border-b py-5" v-for="thread in threads"
                        >
                            <p class="cursor-pointer text-center" @click="selectThread(thread)">
                                {{
                                    user.user_type  == 'owner' ? thread.tenant.name : thread.owner.name
                                }}
                            </p>
                        </div>
                    </div>

                    <div v-else>
                        <div class="w-full border-b" style="height: 7.2vh;">
                            <p class="p-4 font-bold text-center">
                                {{
                                    user.user_type  == 'owner' ? selectedThread.tenant.name : selectedThread.owner.name
                                }}
                            </p>
                        </div>

                        <div style="height: 77vh;" class="overflow-y-scroll flex flex-col" ref="messageDiv">
                            <div class="w-full px-2" v-for="m in selectedThread.messages">
                                <p class="break-words my-2 bg-gray-300 p-3 text-sm"
                                    style="width: 100px; border-radius: 10px;"
                                    :class="{
                                        'float-right': user.id == m.user_id,
                                    }"
                                >
                                    {{ m.message }}
                                </p>
                            </div>
                        </div>

                        <div class="w-full px-2" style="height: 10%;">
                            <input type="text" placeholder="Type your message here..."
                                class="message-input py-2"
                                style="width: 70%;"
                                v-model="messageToSend"
                                @keyup.enter="sendMessage()"
                            />

                            <button class="send-btn bg-cyan-500 py-1" style="width: 30%;"
                                @click="sendMessage()"
                            >
                                Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </TenantLayout>

        <OtherLayout v-else>
            <div class="px-4 pt-14 lg:ml-64">
            <div class="w-full flex flex-col overflow-y-hidden main">
                <div class="flex flex-row" v-if="!isMobileView">
                    <div class="w-3/12 border-r flex flex-col main">
                        <div class="w-full border-b py-5" v-for="thread in threads"
                            :class="{
                                'bg-gradient-to-r from-cyan-500 to-gray-400': thread.id == selectedThread.id,
                            }"
                        >
                            <p class="cursor-pointer text-center" @click="selectThread(thread)">
                                {{
                                    user.user_type  == 'owner' ? thread.tenant.name : thread.owner.name
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="w-9/12 main">
                        <div class="w-full border-b" style="height: 7.2vh;" v-if="selectedThread">
                            <p class="p-4 font-bold ">
                                {{
                                    user.user_type  == 'owner' ? selectedThread.tenant.name : selectedThread.owner.name
                                }}
                            </p>
                        </div>

                        <div style="height: 77vh;" class="overflow-y-scroll flex flex-col" ref="messageDiv" v-if="selectedThread">
                            <div class="w-full px-2" v-for="m in selectedThread.messages">
                                <p class="break-words my-2 bg-gray-300 p-3 text-sm"
                                    style="width: 400px; border-radius: 10px;"
                                    :class="{
                                        'float-right': user.id == m.user_id,
                                    }"
                                >
                                    {{ m.message }}
                                </p>
                            </div>
                        </div>

                        <div class="w-full px-2" style="height: 10%;" v-if="selectedThread">
                            <input type="text" placeholder="Type your message here..."
                                class="message-input py-2"
                                style="width: 90%;"
                                v-model="messageToSend"
                                @keyup.enter="sendMessage()"
                            />

                            <button class="send-btn bg-cyan-500 py-2" style="width: 10%;"
                                @click="sendMessage()"
                            >
                                Send
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else>
                    <div class="w-full flex flex-col" v-if="!selectedThread">
                        <div class="w-full border-b py-5" v-for="thread in threads"
                        >
                            <p class="cursor-pointer text-center" @click="selectThread(thread)">
                                {{
                                    user.user_type  == 'owner' ? thread.tenant.name : thread.owner.name
                                }}
                            </p>
                        </div>
                    </div>

                    <div v-else>
                        <div class="w-full border-b" style="height: 7.2vh;">
                            <p class="p-4 font-bold text-center">
                                {{
                                    user.user_type  == 'owner' ? selectedThread.tenant.name : selectedThread.owner.name
                                }}
                            </p>
                        </div>

                        <div style="height: 77vh;" class="overflow-y-scroll flex flex-col" ref="messageDiv">
                            <div class="w-full px-2" v-for="m in selectedThread.messages">
                                <p class="break-words my-2 bg-gray-300 p-3 text-sm"
                                    style="width: 100px; border-radius: 10px;"
                                    :class="{
                                        'float-right': user.id == m.user_id,
                                    }"
                                >
                                    {{ m.message }}
                                </p>
                            </div>
                        </div>

                        <div class="w-full px-2" style="height: 10%;">
                            <input type="text" placeholder="Type your message here..."
                                class="message-input py-2"
                                style="width: 70%;"
                                v-model="messageToSend"
                                @keyup.enter="sendMessage()"
                            />

                            <button class="send-btn bg-cyan-500 py-1" style="width: 30%;"
                                @click="sendMessage()"
                            >
                                Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </OtherLayout>
      </div>
</template>

<style>
.main {
    height: 100%;
    min-height: 92vh;
    background-color: #e5e8e8;
}

.border-r {
    border-right: 1px solid gray;
}

.border-b {
    border-bottom: 1px solid gray;
}

.message-input {
    height: 80%;
    border: none;
    border-radius: 5px;
}

.send-btn {
    border: 1px solid gray;
    height: 80%;
}

.message {
    min-width: none;
    width: 400px;
    border-radius: 10px;
}
</style>
