import * as types from './mutation-types'

export const updateSettings = ({ commit }, settings) => {
    commit(types.UPDATE_SETTINGS, settings);
}

export const updateUser = ({ commit }, user) => {
    commit(types.UPDATE_USER, user);
}
