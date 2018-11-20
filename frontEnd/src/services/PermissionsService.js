const PermissionsService = {
    isGranted: (user, role) => {
        return user.permissions[role] ? true : false;
    },

    isGrantedArr: (user, roles, type) => {
        let permissionsCount = Object.keys(roles).length;
        if (!permissionsCount) {
            return false;
        }
        if (!type) {
            type = 1;
        }
        let checkedPermission = 0;
        for(let i in roles) {
            if (user.permissions[roles[i]]) {
                if (type === 1) {
                    return true;
                }
            } else {
                if (type === 2) {
                    return false;
                }
            }
            checkedPermission++;
        }
        if (type === 2 && checkedPermission === permissionsCount) {
            return true;
        }
        return false;
    }
};

export default PermissionsService;