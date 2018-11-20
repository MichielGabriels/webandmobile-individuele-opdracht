import React, { Component } from 'react';
import Users from './Users';
import axios from 'axios';
import Select from 'react-select';
import { Button } from 'react-mdl';
import { connect } from "react-redux";
import { Redirect } from 'react-router-dom';

class UserList extends Component {

    constructor(props) {
        super(props);

        this.state = {
            users: [],
            userIds: [],
            selectedRemoveUserOption: null,
            selectedEditUserRoleOption: null,
            selectedRoleOption: null,
            possibleUserRoles: [
                { value: 'Administrator', label: 'Administrator' },
                { value: 'Moderator', label: 'Moderator' },
                { value: 'User', label: 'User' }
            ]
        }
    }

    componentWillMount() {
        axios.get('http://localhost:8000/users')
            .then(response => {
                const users = response.data;
                this.setState({ users });
                this.setState({
                    userIds: []
                });
                users.forEach(user => {
                    this.setState({
                        userIds: this.state.userIds.concat({ value: user.id, label: user.id })
                    });
                });
            });
    }

    handleRemoveUserSelectChange = (selectedRemoveUserOption) => {
        this.setState({
            selectedRemoveUserOption: selectedRemoveUserOption
        });
    }

    handleRemoveUser = (e) => {
        e.preventDefault();
        if (this.state.selectedRemoveUserOption != null) {
            axios.get('http://localhost:8000/users/' + this.state.selectedRemoveUserOption.value)
                .then(response => {
                    this.componentWillMount();
                    this.setState({
                        selectedRemoveUserOption: null
                    });
                });
        }
    }

    handleEditUserRoleSelectChange = (selectedEditUserRoleOption) => {
        this.setState({
            selectedEditUserRoleOption: selectedEditUserRoleOption
        });
    }

    handleRoleSelectChange = (selectedRoleOption) => {
        this.setState({
            selectedRoleOption: selectedRoleOption
        });
    }

    handleEditUserRole = (e) => {
        e.preventDefault();
        if (this.state.selectedEditUserRoleOption != null && this.state.selectedRoleOption != null) {
            axios.post('http://localhost:8000/users/' + this.state.selectedEditUserRoleOption.value + '?role=' + this.state.selectedRoleOption.value)
                .then(response => {
                    this.componentWillMount();
                    this.setState({
                        selectedEditUserRoleOption: null,
                        selectedRoleOption: null
                    })
                });
        }
    }

    render() {
        return (
            this.props.role ?
                <div className="UserList" align="center" style={{ marginTop: '100px' }}>
                    <div style={{ marginTop: '10px', width: '10%' }}>
                        <form onSubmit={this.handleRemoveUser}>
                            <Select value={this.state.selectedRemoveUserOption} onChange={this.handleRemoveUserSelectChange} options={this.state.userIds} />
                            <Button raised ripple style={{ marginTop: '5px' }}>Remove User</Button>
                        </form>
                    </div>

                    <Users users={this.state.users} />

                    <div style={{ marginTop: '10px', width: '10%' }}>
                        <form onSubmit={this.handleEditUserRole}>
                            <Select value={this.state.selectedEditUserRoleOption} onChange={this.handleEditUserRoleSelectChange} options={this.state.userIds} />
                            <Select value={this.state.selectedRoleOption} onChange={this.handleRoleSelectChange} options={this.state.possibleUserRoles} />
                            <Button raised ripple style={{ marginTop: '5px' }}>Edit User</Button>
                        </form>
                    </div>
                </div>
            : <Redirect to="/" />
        );
    }
}

function mapStateToProps(state) {
    return {
        role: state.userInfo.role
    };
}

function mapDispatchToProps(dispatch, ownProps) {
    return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(UserList);