function getAppContext(accessUrl) {
    const host = window.location.hostname;
    const expectedHost = new URL(accessUrl).hostname;
    const isEmbeddedInAdvisingApp = host.replace(/\/$/, '') === expectedHost.replace(/\/$/, '');

    let baseUrl = '/';

    if (isEmbeddedInAdvisingApp) {
        baseUrl = '/portals/knowledge-management';
    }

    return { isEmbeddedInAdvisingApp, baseUrl };
}

export default getAppContext;
