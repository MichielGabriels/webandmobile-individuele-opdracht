import * as types from "../actions/actionTypes";
import initialState from "../store/initialState";

export default function userInfoReducer(state = initialState.userInfo, action) {
    switch (action.type) {
        case types.USERNAME_ENTERED: {
            return Object.assign({}, state, { name: action.body });
        }

        case types.LOGGED_IN_WITH_ROLE_ADMINISTRATOR: {
            return Object.assign({}, state, { role: action.body })
        }

        default:
            return state;
    }
}