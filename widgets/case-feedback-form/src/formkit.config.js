import { generateClasses } from '@formkit/themes';
import { genesisIcons } from '@formkit/icons';
import theme from './FormKit/theme.js';
import inputs from './FormKit/Inputs/index.js';

export default {
    icons: {
        ...genesisIcons,
    },
    inputs,
    config: {
        classes: generateClasses(theme),
    },
};