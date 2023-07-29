import { createRouter, createWebHistory } from "vue-router";
import TicketIndex from '@/components/tickets/index.vue'
import NotFound from '@/components/NotFound.vue'

const routes = [
    {
        path: '/',
        component: TicketIndex
    },
    {
        path: '/:pathMatch(.*)*',
        component: NotFound
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
