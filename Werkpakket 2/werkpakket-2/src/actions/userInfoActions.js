import * as types from "./actionTypes";

function nameEntered(name) {
    return {
        type: types.USERNAME_ENTERED,
        body: name
    };
}

function administratorLoggedIn(role) {
    return {
        type: types.LOGGED_IN_WITH_ROLE_ADMINISTRATOR,
        body: role
    };
}

export function storeUserName(name) {
    return function (dispatch) {
        return dispatch(nameEntered(name));
    };
}

export function storeUserRole(role) {
    return function (dispatch) {
        return dispatch(administratorLoggedIn(role));
    };
}