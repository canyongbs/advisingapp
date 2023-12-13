function attachRecaptchaScript(siteKey) {
    let recaptchaScript = document.createElement('script');
    recaptchaScript.setAttribute('src', 'https://www.google.com/recaptcha/api.js?render=' + siteKey);
    document.head.appendChild(recaptchaScript);
}

export default attachRecaptchaScript;
