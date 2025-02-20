import { createInput } from '@formkit/vue';
import OneTimePassword from '../../../../form/src/FormKit/Inputs/OneTimePassword.vue';
import Rating from './Rating.vue';

export default {
    otp: createInput(OneTimePassword, {
        props: ['digits'],
    }),
    rating: createInput(Rating, {
        props: [],
    }),
};