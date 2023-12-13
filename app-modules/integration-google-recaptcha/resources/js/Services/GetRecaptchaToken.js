async function getRecaptchaToken(siteKey) {
    return new Promise((resolve, reject) => {
        grecaptcha.ready(async function () {
            try {
                let token = await grecaptcha.execute(siteKey, { action: 'formSubmission' });

                resolve(token);
            } catch (error) {
                reject(error);
            }
        });
    });
}

export default getRecaptchaToken;
