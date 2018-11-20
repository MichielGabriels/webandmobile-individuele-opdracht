import React, { Component, Fragment } from 'react';
import { Redirect } from 'react-router-dom';
import Typography from '@material-ui/core/Typography';
import TextField from '@material-ui/core/TextField';
import Button from '@material-ui/core/Button';
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import { storeUserName, storeUserRole } from "../actions/userInfoActions";
import NavigationBar from './NavigationBar.js';
import axios from 'axios';

class Welcome extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: '',
            password: '',
            navigate: false,
            navigateUsers: false
        };
    }

    onClickLogin = () => {
        if (this.state.name !== '' && this.state.password !== '') {
            axios.get('http://localhost:8000/user/login?username=' + this.state.name + '&password=' + this.state.password)
                .then(response => {
                    this.props.storeUserName(this.state.name);
                    this.setState({ navigate: true });
                })
                .catch(error => {
                    alert("Invalid credentials");
                });
        }
    }

    onClickAnonymous = () => {
        this.props.storeUserName('Anonymous');
        this.setState({ navigate: true });
    }

    onClickUsers = () => {
        //this.setState({ navigateUsers: true })
        if (this.state.name !== '' && this.state.password !== '') {
            axios.get('http://localhost:8000/user/login?username=' + this.state.name + '&password=' + this.state.password)
                .then(response => {
                    const role = response.data.role;
                    if (role === 'Administrator') {
                        this.props.storeUserRole(role);
                        this.setState({ navigateUsers: true });
                    } else {
                        alert('You are not allowed to view this page.');
                    }
                })
                .catch(error => {
                    alert('Invalid credentials.');
                });
        }
    }

    render() {
        return (
            this.state.navigateUsers ? <Redirect to="/users" /> :
                this.state.navigate ? <Redirect to="/messageList" /> :
                <div>
                    <NavigationBar displaySearchFields={'none'}/>
                    <Fragment>
                        <Typography variant="title">
                            Welcome!
                    </Typography>
                        <div className="col s3">
                            <TextField
                                id="userName"
                                name="name"
                                placeholder="Enter your name"
                                value={this.state.name}
                                onChange={(e) => this.setState({ name: e.target.value })}
                            />
                        </div>
                        <div className="col s3">
                            <TextField
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Enter your password"
                                value={this.state.password}
                                onChange={(e) => this.setState({ password: e.target.value })}
                            />
                        </div>
                        <div className="col s3">
                            <Button
                                variant="contained"
                                size="small"
                                color="primary"
                                id="confirmName"
                                onClick={this.onClickLogin}
                                disabled={!(this.state.name.length > 0 && this.state.password.length > 0)}>
                                Enter
                            </Button>
                        </div>
                        <div className="col s3">
                            <Button 
                                variant="contained"
                                size="small"
                                color="primary"
                                onClick={this.onClickUsers}
                                disabled={!(this.state.name.length > 0 && this.state.password.length > 0)}>
                                Registered Users
                            </Button>
                        </div>
                        <Button 
                            variant="contained"
                            size="small"
                            color="primary"
                            id="denyLogin"
                            disabled={this.state.name.length > 0 && this.state.password.length > 0}
                            onClick={this.onClickAnonymous}>
                            Proceed anonymously
                        </Button>
                    </Fragment>
                </div>
        );
    }
}

function mapStateToProps(state) {
    return {};
}

function mapDispatchToProps(dispatch, ownProps) {
    return {
        storeUserName: bindActionCreators(storeUserName, dispatch),
        storeUserRole: bindActionCreators(storeUserRole, dispatch)
    };
}

export default connect(mapStateToProps, mapDispatchToProps)(Welcome);