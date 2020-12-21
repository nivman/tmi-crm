export const user = state => {
    return state.user;
};
export const settings = state => {
    return state.settings;
};
export const admin = state => {
    return state.user && state.user.roles.find(r => r == "admin");
};
export const superAdmin = state => {
    return state.user && state.user.roles.find(r => r == "super");
};
export const customer = state => {
    return state.user && state.user.roles.find(r => r == "customer");
};
export const vendor = state => {
    return state.user && state.user.roles.find(r => r == "vendor");
};
export const stock = state => {
    return state.settings.stock ? true : false;
};
export const notifications = state => {
    return state.notifications.payment_due > 0 || state.notifications.payment_review > 0;
};
export const note = state => {

    return state.note
};