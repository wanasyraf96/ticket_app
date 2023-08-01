<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { storeAccessToken } from '../../helper/utils';
const email = ref('');
const password = ref('');
const error = ref('')
const users = ref([]);

const router = new useRouter()
const login = async () => {
    fetch('/api/signin', {
        method: 'post',
        body: JSON.stringify({ email: email.value, password: password.value }),
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then((res) => {
        console.log('response', res)
        if (!res.ok) {
            if (res.status === 422) {
                throw new Error('Invalid Credentials')
            }
            else if (res.status === 500) {
                throw new Error('Server error')
            }
            else {
                throw new Error('Unknown error occurred')
            }
        }
        return res.json()
    }).then((json) => {
        const { access_token } = json;
        storeAccessToken(access_token)
        router.push('/tickets')

    }).catch((err) => {
        error.value = err.message
    })
};

onMounted(async () => {
    getUser()
})

const getUser = async () => {
    const res = await axios('/api/get-user')
    users.value = res.data
};

</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white p-8 rounded shadow">
            <!-- <div class="" v-if="users.length > 0">
                <ul>
                    <li v-for="user in users" :key="user.email">
                        {{ user.email }}
                    </li>
                </ul>
            </div> -->
            <div class="w-full">
                <ul>
                    <li>staff@example.com</li>
                    <li>password</li>
                </ul>
            </div>

            <h2 class="text-2xl font-semibold mb-6">Login</h2>
            <form @submit.prevent="login">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email Address:</label>
                    <input type="email" id="email" v-model="email" required
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500"
                        autocomplete="email" />
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700">Password:</label>
                    <input type="password" id="password" v-model="password" required
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500"
                        autocomplete="current-password" />
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Login
                </button>
            </form>
        </div>
    </div>
</template>

