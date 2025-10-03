(function () {
    // Get the portal embed element
    const portalEmbedElement = document.querySelector('qna-advisor-embed');
    if (!portalEmbedElement) return;

    // Get the resources URL from the element
    const resourcesUrl = portalEmbedElement.getAttribute('resources-url');
    if (!resourcesUrl) return;

    // Fetch the latest resource URLs
    fetch(resourcesUrl)
        .then((response) => response.json())
        .then((resources) => {
            // Apply the CSS URL as an attribute to the portal embed
            if (resources.css) {
                portalEmbedElement.setAttribute('css-url', resources.css);
            }

            // Load the JS
            if (resources.js) {
                const scriptElement = document.createElement('script');
                scriptElement.src = resources.js;
                scriptElement.type = 'module';
                document.body.appendChild(scriptElement);
            }
        })
        .catch((error) => {
            console.error('Failed to load portal resources:', error);
        });
})();
