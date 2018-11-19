import React, { Component } from 'react';
import MessageList from './MessageList.js';
import Welcome from './Welcome.js';
import { Route } from 'react-router-dom';
import UserList from './UserList.js';

class Dashboard extends Component {
    state = {
    }

    render() {
        return (
            <div className="App">
                <Route exact path='/' component={Welcome} />
                <Route path='/messageList' component={MessageList} />
                <Route path='/users' component={UserList} />
            </div>
        );
    }
}

export default Dashboard;