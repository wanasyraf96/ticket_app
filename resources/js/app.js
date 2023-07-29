import './bootstrap';

import { createApp } from 'vue'
import app from '@/components/app.vue'
import router from './router/index';
import Pusher from 'pusher-js'

const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
});

createApp(app)
    .use(router)
    // .use(pusher)
    .mount('#root')
