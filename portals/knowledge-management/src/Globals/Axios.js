import axios from 'axios';

const axiosInstance = axios.create({
    withCredentials: true,
});

axiosInstance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axiosInstance.defaults.headers.common['Accept'] = 'application/json';
axiosInstance.defaults.headers.common['Access-Control-Allow-Origin'] = '*';

export default axiosInstance;
