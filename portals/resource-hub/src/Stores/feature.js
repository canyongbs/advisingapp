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
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useFeatureStore = defineStore('feature', () => {
    const hasServiceManagement = ref(false);
    const hasAssets = ref(false);
    const hasLicense = ref(false);
    const hasTasks = ref(false);
    const isStatusEnabled = ref(false);
    const isAdvisoryEnabled = ref(false);
    const isAssetEnabled = ref(false);
    const isLicenseEnabled = ref(false);

    async function setHasServiceManagement(value) {
        hasServiceManagement.value = value;
    }

    async function getHasServiceManagement() {
        return hasServiceManagement.value;
    }

    async function setHasAssets(value) {
        hasAssets.value = value;
    }

    async function getHasAssets() {
        return hasAssets.value;
    }

    async function setHasLicense(value) {
        hasLicense.value = value;
    }

    async function getHasLicense() {
        return hasLicense.value;
    }

    async function setHasTasks(value) {
        hasTasks.value = value;
    }

    async function getHasTasks() {
        return hasTasks.value;
    }

    async function setIsStatusEnabled(value) {
        isStatusEnabled.value = value;
    }

    async function getIsStatusEnabled() {
        return isStatusEnabled.value;
    }

    async function setIsAdvisoryEnabled(value) {
        isAdvisoryEnabled.value = value;
    }

    async function getIsAdvisoryEnabled() {
        return isAdvisoryEnabled.value;
    }

    async function setIsAssetEnabled(value) {
        isAssetEnabled.value = value;
    }

    async function getIsAssetEnabled() {
        return isAssetEnabled.value;
    }

    async function setIsLicenseEnabled(value) {
        isLicenseEnabled.value = value;
    }

    async function getIsLicenseEnabled() {
        return isLicenseEnabled.value;
    }

    return {
        hasServiceManagement,
        getHasServiceManagement,
        setHasServiceManagement,
        hasAssets,
        getHasAssets,
        setHasAssets,
        hasLicense,
        getHasLicense,
        setHasLicense,
        hasTasks,
        getHasTasks,
        setHasTasks,
        isStatusEnabled,
        getIsStatusEnabled,
        setIsStatusEnabled,
        isAdvisoryEnabled,
        getIsAdvisoryEnabled,
        setIsAdvisoryEnabled,
        isAssetEnabled,
        getIsAssetEnabled,
        setIsAssetEnabled,
        isLicenseEnabled,
        getIsLicenseEnabled,
        setIsLicenseEnabled,
    };
});
