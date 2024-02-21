import axios from '@/Globals/Axios.js';

async function determineIfUserIsAuthenticated() {
    console.log('determineIfUserIsAuthenticated()');

    // TODO Retrieve the token from the local storage...
    let token = null;

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
