import {applyMiddleware, compose, createStore} from 'redux';
import thunkMiddleware from 'redux-thunk';
import initialState from './initialState';
import rootReducer from '../reducers/rootReducer';

export default function configureStore() {
    const middlewareEnhancer = applyMiddleware(thunkMiddleware);
    const composedEnhancers = compose(middlewareEnhancer);

    return createStore(rootReducer, initialState, composedEnhancers);
}