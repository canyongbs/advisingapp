(function() {
    // Get the portal embed element
    const portalEmbedElement = document.querySelector("qna-advisor-embed");
    if (!portalEmbedElement) return;

    // Get the resources URL from the element
    const resourcesUrl = portalEmbedElement.getAttribute("url");
    if (!resourcesUrl) return;

    // Fetch the latest resource URLs
    fetch(resourcesUrl)
        .then((response) => response.json())
        .then((resources) => {
            if (!resources || !resources.entry || !resources.js || !resources.css) {
                throw Error("Resources are missing or incomplete.");
            }

            portalEmbedElement.setAttribute("entry-url", resources.entry);
            portalEmbedElement.setAttribute("css-url", resources.css);

            const scriptElement = document.createElement("script");
            scriptElement.src = resources.js;
            scriptElement.type = "module";
            document.body.appendChild(scriptElement);
        })
        .catch((error) => {
            console.error("Failed to load portal resources:", error);
        });
})();
