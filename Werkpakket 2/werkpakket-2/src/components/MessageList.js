import React, { Component } from 'react';
import './Message';
import Message from './Message';
import axios from 'axios';
import NavigationBar from './NavigationBar.js';
import { ProgressBar } from 'react-mdl';

class MessageList extends Component {
    constructor(props) {
        super(props);
        this.state = {
            messages: [],
            reactions: [],
            reactionModelToAdd: {
                messageId: 0,
                content: ''
            },
            searchContentString: '',
            searchCategoryString: '',
            messagesLoading: false
        }
    }

    componentDidMount() {
        this.setState({
            messagesLoading: true
        })
        axios.get('http://localhost:8000/messages')
            .then(response => {
                const messages = response.data;
                this.setState({ messages });
            });
        axios.get('http://localhost:8000/reactions')
            .then(response => {
                const reactions = response.data;
                this.setState({ reactions, messagesLoading: false });
            });
    }

    onClickUpvote = (id) => {
        axios.post('http://localhost:8000/message/upvote/' + id)
            .then(response => {
                const updatedMessages = Array.from(this.state.messages);
                updatedMessages[id - 1] = response.data;
                this.setState({ messages: updatedMessages });
            });
    }

    onClickDownvote = (id) => {
        axios.post('http://localhost:8000/message/downvote/' + id)
            .then(response => {
                const updatedMessages = Array.from(this.state.messages);
                updatedMessages[id - 1] = response.data;
                this.setState({ messages: updatedMessages });
            });
    }

    onReactionTextfieldChanged = (event, messageId) => {
        const newReaction = event.target.value;
        const modelToUpdate = this.state.reactionModelToAdd;

        if (newReaction) {
            modelToUpdate.content = newReaction;
            modelToUpdate.messageId = messageId;
        } else {
            modelToUpdate.content = '';
        }

        this.setState({ reactionModelToAdd: modelToUpdate });
    }

    reactToComment = () => {
        if (this.state.reactionModelToAdd.content.trim()) {
            axios.post('http://localhost:8000/reaction/' + this.state.reactionModelToAdd.messageId + '/' + this.state.reactionModelToAdd.content)
                .then(response => {
                    const updatedReactions = Array.from(response.data);
                    this.setState({ reactions: updatedReactions });
                });

        }
        this.setState({
            reactionModelToAdd: {
                messageId: 0,
                content: ''
            }
        });
    }

    onSearchSubmit = (event) => {
        if (event.key === 'Enter') {
            // Check if event triggered by content search
            if (event.target.id === "searchContent") {
                var content = event.target.value;
                this.setState({
                    searchContentString: content
                }, () => {
                    if (this.state.searchContentString === '' && this.state.searchCategoryString === '') {
                        this.componentDidMount();
                    }
                });
                // Check if necessary to search by both content and category --> category InputBase also has text?
                if (this.state.searchCategoryString !== '') {
                    axios.get('http://localhost:8000/message?content=' + content + '&category=' + this.state.searchCategoryString)
                        .then(
                            (response) => {
                                const filteredMessages = response.data;
                                this.setState({
                                    messages: filteredMessages
                                });
                            },
                            (error) => {
                                this.setState({
                                    messages: []
                                });
                            }
                        );
                    // category InputBase has no text
                } else {
                    axios.get('http://localhost:8000/message?content=' + content)
                        .then(
                            (response) => {
                                const filteredMessages = response.data;
                                this.setState({
                                    messages: filteredMessages
                                });
                            },
                            (error) => {
                                this.setState({
                                    messages: []
                                });
                            }
                        );
                }
                // Check if event triggered by category search
            } else if (event.target.id === "searchCategory") {
                var category = event.target.value;
                this.setState({
                    searchCategoryString: category
                }, () => {
                    if (this.state.searchContentString === '' && this.state.searchCategoryString === '') {
                        this.componentDidMount();
                    }
                });
                // Check if necessary to search by both content and category --> content InputBase also has text?
                if (this.state.searchContentString !== '') {
                    axios.get('http://localhost:8000/message?content=' + this.state.searchContentString + '&category=' + category)
                        .then(
                            (response) => {
                                const filteredMessages = response.data;
                                this.setState({
                                    messages: filteredMessages
                                });
                            },
                            (error) => {
                                this.setState({
                                    messages: []
                                });
                            }
                        );
                    // content InputBase has no text
                } else {
                    axios.get('http://localhost:8000/message?category=' + category)
                        .then(
                            (response) => {
                                const filteredMessages = response.data;
                                this.setState({
                                    messages: filteredMessages
                                });
                            },
                            (error) => {
                                this.setState({
                                    messages: []
                                });
                            }
                        );
                }
            }
        }
    }

    renderMessages() {
        return this.state.messages.map(message =>
            (
                <Message key={message.id} data-key={message.id} reactions={this.state.reactions} reactToComment={this.reactToComment} reactionModelToAdd={this.state.reactionModelToAdd} onReactionTextfieldChanged={this.onReactionTextfieldChanged} messageModel={message} onClickDownvote={this.onClickDownvote} onClickUpvote={this.onClickUpvote}></Message>
            ),
        )
    }

    render() {
        return (
            <div>
                <NavigationBar
                    onSearchSubmit={this.onSearchSubmit}
                />
                {this.state.messagesLoading ?
                    <div style={{
                        width: '100%'
                    }}>
                        <ProgressBar indeterminate style={{ width: '100%', height: '5px' }} />
                    </div>
                    : null
                }

                {this.renderMessages()}
            </div>
        );
    }
}

export default MessageList;