import { useRouter } from "vue-router"
const router = new useRouter()
export const validateUser = () => {
    const token = getAccessToken()
    return fetch('/api/user', {
        method: 'get',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        }
    })
        .then(res => {
            if (!res.ok)
                return false
            return true
        })
        .catch(error => {
            console.error('Unauthenticated User')
            // console.error('Redirect to Login Page')
            // router.push('/')
            return false
        })
}

export const getUser = async () => {
    try {
        const token = getAccessToken()
        const res = await axios({
            url: '/api/user',
            method: 'get',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
            }
        })

        return res.data
    } catch (err) {
        // console.error(err)
        console.log('redirect to homepage')
        // router.push('/')
    }
}

export const getAccessToken = () => {
    return sessionStorage.getItem('token')
}

export const storeAccessToken = (token) => {
    sessionStorage.setItem('token', token)
    return true
}
