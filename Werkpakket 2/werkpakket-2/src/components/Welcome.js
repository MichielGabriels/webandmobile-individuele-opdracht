import React, { Component, Fragment } from 'react';
import { Redirect } from 'react-router-dom';
import Typography from '@material-ui/core/Typography';
import TextField from '@material-ui/core/TextField';
import Button from '@material-ui/core/Button';
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import { storeUserName } from "../actions/userInfoActions";
import NavigationBar from './NavigationBar.js';

class Welcome extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: '',
            navigate: false
        };
    }

    onClickLogin = () => {
        this.props.storeUserName(this.state.name);
        this.setState({ navigate: true });
    }

    onClickAnonymous = () => {
        this.props.storeUserName('Anonymous');
        this.setState({ navigate: true });
    }

    render() {
        return (
            this.state.navigate ? <Redirect to="/messageList" /> :
                <div>
                    <NavigationBar displaySearchFields={'none'} />
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
                            <Button
                                variant="contained"
                                size="small"
                                color="primary"
                                id="confirmName"
                                onClick={this.onClickLogin}
                                disabled={!this.state.name}>
                                Enter
                        </Button>
                        </div>
                        <Button
                            variant="contained"
                            size="small"
                            color="primary"
                            id="denyLogin"
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
        storeUserName: bindActionCreators(storeUserName, dispatch)
    };
}

export default connect(mapStateToProps, mapDispatchToProps)(Welcome);