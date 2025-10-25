const CONFIG = {
    // Automatically detects the current domain
    BASE_URL: window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/'),
    API: {
        ADMIN: '../Admin/Php/',
        USER: '../User/Php/',
        ACCOUNTS: '../Accounts/',
        API: '../API/'
    }
};

window.APP_CONFIG = CONFIG;