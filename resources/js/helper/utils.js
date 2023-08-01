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

export const isUserStaff = (user) => {
    if (!user) return false
    // get staff id

    if (user.roles.find((role) => role.name === 'staff'))
        return true
    return false
}

export const getAccessToken = () => {
    return sessionStorage.getItem('token')
}

export const storeAccessToken = (token) => {
    sessionStorage.setItem('token', token)
    return true
}

export const decodeHtml = ((text) => {
    const parser = new DOMParser();
    const decoded = parser.parseFromString(text, 'text/html').body.textContent;
    return decoded
})

export const jsonToQueryString = (json) => {
    return Object.keys(json)
        .map((key) => `${encodeURIComponent(key)}=${encodeURIComponent(json[ key ])}`)
        .join(',');
}
