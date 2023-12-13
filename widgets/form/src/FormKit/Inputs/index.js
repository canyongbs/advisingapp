import { createInput } from '@formkit/vue';
import OneTimePassword from './OneTimePassword.vue';
import Signature from './Signature.vue';

export default {
    'otp': createInput(OneTimePassword, {
        props: ['digits'],
    }),
    'signature': createInput(Signature, {
        props: [],
    }),
}
