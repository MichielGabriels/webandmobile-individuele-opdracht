import React, { Component } from 'react';
import Users from './Users';
import axios from 'axios';

class UserList extends Component {

    state = {
        users : []
    }

    componentWillMount() {
        axios.get('http://localhost:8000/users')
            .then(response => {
                const users = response.data;
                this.setState({ users });
            }
        );
    }

    handleRemoveUser(e) {
        console.log(e);
    }

    render() {
        return (
        <div className="UserList">
            <Users users={this.state.users} />
        </div>
        );
    }
}

export default UserList;