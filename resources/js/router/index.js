import { createRouter, createWebHistory } from "vue-router";
import LoginIndex from '@/components/login/container.vue'
import TicketsContainer from '@/components/tickets/container.vue'
import TicketContainer from '@/components/ticket/container.vue'
import NotFound from '@/components/NotFound.vue'

const routes = [
    {
        path: '/',
        component: LoginIndex
    },
    {
        path: '/tickets',
        component: TicketsContainer
    },
    {
        path: '/ticket',
        component: TicketContainer
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
