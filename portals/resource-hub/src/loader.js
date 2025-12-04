(function () {
    // Get the portal embed element
    const portalEmbedElement = document.querySelector('resource-hub-portal-embed');
    if (!portalEmbedElement) throw new Error('Embed not found');

    // Get the resources URL from the element
    const resourcesUrl = portalEmbedElement.getAttribute('url');
    if (!resourcesUrl) throw new Error('Assets URL not found');

    // Fetch the latest resource URLs
    fetch(resourcesUrl)
        .then((response) => response.json())
        .then((assets) => {
            if (!assets || !assets.asset_url || !assets.entry || !assets.js) {
                throw Error('Assets are missing or incomplete.');
            }
            // Apply the CSS URL as an attribute to the portal embed
            // if (resources.css) {
            //     portalEmbedElement.setAttribute('css-url', resources.css);
            // }

            portalEmbedElement.setAttribute('entry-url', assets.entry);

            // Set up the global variable for Vite's dynamic imports using the asset endpoint
            window.__VITE_RESOURCE_HUB_PORTAL_ASSET_URL__ = assets.asset_url;

            const scriptElement = document.createElement('script');
                scriptElement.src = assets.js;
                scriptElement.type = 'module';
                document.body.appendChild(scriptElement);
        })
        .catch((error) => {
            console.error('Failed to load portal resources:', error);
        });
})();
