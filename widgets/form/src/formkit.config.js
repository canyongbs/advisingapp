import { generateClasses } from '@formkit/themes';
import { genesisIcons } from '@formkit/icons';
import myTailwindTheme from './tailwind-theme.js';
import {createInput} from "@formkit/vue";
import Signature from "./FormKit/Signature.vue";

export default {
    icons: {
        ...genesisIcons,
    },
    inputs: {
        'signature': createInput(Signature, {
            props: [],
        }),
    },
    config: {
        classes: generateClasses(myTailwindTheme),
    },
};
