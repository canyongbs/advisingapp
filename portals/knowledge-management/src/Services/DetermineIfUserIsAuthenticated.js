import axios from '@/Globals/Axios.js';
import { useTokenStore } from '@/Stores/token.js';

async function determineIfUserIsAuthenticated(endpoint) {
    const { getToken } = useTokenStore();
    let token = await getToken();

    // TODO Remove hardcoded endpoint
    return await axios
        .get(endpoint, {
            headers: { Authorization: `Bearer ${token}` },
        })
        .then((response) => {
            const isAuthenticated = response.status === 200;

            return isAuthenticated;
        })
        .catch((error) => {
            return false;
        });
}

export default determineIfUserIsAuthenticated;
