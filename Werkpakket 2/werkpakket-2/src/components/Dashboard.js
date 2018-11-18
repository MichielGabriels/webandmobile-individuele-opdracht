import React, { Component } from 'react';
import MessageList from './MessageList.js';
import Welcome from './Welcome.js';
import { Route } from 'react-router-dom';

class Dashboard extends Component {
    state = {
    }

    render() {
        return (
            <div className="App">
                <Route exact path='/' component={Welcome} />
                <Route path='/messageList' component={MessageList} />
            </div>
        );
    }
}

export default Dashboard;