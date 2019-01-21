let user = window.App.user;

module.exports = {
    owns (module,prop = 'user_id') {
        return module[prop] == user.id;
    },

    isAdmin() {
        return ['meiyiming'].includes(user.name);
    }
}