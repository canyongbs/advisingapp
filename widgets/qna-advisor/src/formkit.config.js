import { genesisIcons } from '@formkit/icons';
import { generateClasses } from '@formkit/themes';
import inputs from '../../form/src/FormKit/Inputs/index';
import theme from '../../form/src/FormKit/theme';

export default {
    icons: {
        ...genesisIcons,
    },
    inputs,
    config: {
        classes: generateClasses(theme),
    },
};
