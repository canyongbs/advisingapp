import axios from '@/Globals/Axios.js';
import { useTokenStore } from '@/Stores/token.js';

async function determineIfUserIsAuthenticated() {
    console.log('determineIfUserIsAuthenticated()');

    const { getToken } = useTokenStore();
    let token = await getToken();
    console.log('token', token);

    // TODO Remove hardcoded endpoint
    return await axios
        .get('http://test.advisingapp.local/api/user', {
            headers: { Authorization: `Bearer ${token}` },
        })
        .then((response) => {
            console.log('response', response);

            const isAuthenticated = response.status === 200;

            return isAuthenticated;
        })
        .catch((error) => {
            console.log('error', error);

            return false;
        });
}

export default determineIfUserIsAuthenticated;
