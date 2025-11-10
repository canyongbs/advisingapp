(function () {
    // Get the embed element
    const embedElement = document.querySelector('application-embed');
    if (!embedElement) throw new Error('Embed not found');

    // Get the assets URL from the element
    const assetsUrl = embedElement.getAttribute('url');
    if (!assetsUrl) throw new Error('Assets URL not found');

    // Fetch the latest assets URLs
    fetch(assetsUrl)
        .then((response) => response.json())
        .then((assets) => {
            if (!assets || !assets.asset_url || !assets.entry || !assets.js) {
                throw Error('Assets are missing or incomplete.');
            }

            embedElement.setAttribute('entry-url', assets.entry);

            // Set up the global variable for Vite's dynamic imports using the asset endpoint
            window.__VITE_APPLICATIONS_ASSET_URL__ = assets.asset_url;

            const scriptElement = document.createElement('script');
            scriptElement.src = assets.js;
            scriptElement.type = 'module';
            document.body.appendChild(scriptElement);
        })
        .catch((error) => {
            console.error('Failed to load widget assets:', error);
        });
})();
