import axiosInstance from "axios";

const instance = axiosInstance.create({
    //   baseURL: import.meta.env.VITE_BACKEND_API,
    baseURL: 'http://210.213.80.10:9500/api/',    
    timeout: 10000,
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: false,
});

instance.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});

export default instance;