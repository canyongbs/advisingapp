/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
import { createMessage, getNode } from '@formkit/core';
import { computed, reactive, ref, toRef, watch } from 'vue';

export default function wizard() {
    const activeStep = ref('');
    const steps = reactive({});
    const visitedSteps = ref([]);
    const stepNames = computed(() => Object.keys(steps));
    const currentStep = computed(() => stepNames.value.indexOf(activeStep.value) + 1);
    const totalSteps = computed(() => stepNames.value.length);

    const markStepSubmitted = (stepName) => {
        const node = getNode(stepName);

        if (!node) {
            return;
        }

        node.walk((n) => {
            n.store.set(
                createMessage({
                    key: 'submitted',
                    value: true,
                    visible: false,
                }),
            );
        });
    };

    watch(activeStep, (newStep, oldStep) => {
        if (oldStep && !visitedSteps.value.includes(oldStep)) {
            visitedSteps.value.push(oldStep);
        }

        visitedSteps.value.forEach(markStepSubmitted);
    });

    const isStepValid = (stepName) => {
        const step = steps[stepName];

        return Boolean(step) && step.valid === true;
    };

    const setStep = (delta) => {
        if (delta > 0 && !isStepValid(activeStep.value)) {
            if (!visitedSteps.value.includes(activeStep.value)) {
                visitedSteps.value.push(activeStep.value);
            }

            markStepSubmitted(activeStep.value);

            return;
        }

        const currentIndex = stepNames.value.indexOf(activeStep.value);
        const targetStep = stepNames.value[currentIndex + delta];

        if (targetStep) {
            activeStep.value = targetStep;
        }
    };

    const stepPlugin = (node) => {
        if (node.props.type === 'group') {
            steps[node.name] = steps[node.name] || {};

            node.on('created', () => {
                steps[node.name].valid = toRef(node.context.state, 'valid');
            });

            if (activeStep.value === '') {
                activeStep.value = node.name;
            }

            // Stop plugin inheritance to descendant nodes
            return false;
        }
    };

    return { activeStep, currentStep, totalSteps, visitedSteps, wizardPlugin: stepPlugin, setStep };
}
