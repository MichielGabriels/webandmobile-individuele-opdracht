import React from 'react';
import { Card, CardText, CardTitle, CardActions, FABButton, Icon, List, ListItem, ListItemContent } from 'react-mdl';
import PropTypes from "prop-types";
import 'react-mdl/extra/material.js';
import 'react-mdl/extra/material.css';
import AddReaction from './AddReaction.js';
import ReactionText from './ReactionText';

const Message = (props) => {
    return (
        <div style={{ width: "50%", margin: "auto" }}>
            <Card shadow={0} style={{ background: "#FFFFFF", maxWidth: 'auto', width: 'auto', margin: 'auto', marginTop: '20px', marginBottom: '30px', boxShadow: '1px 1px 2.5px 2.5px lightgrey' }}>
                <CardTitle style={{ color: '#fff', minHeight: '50px', height: 'auto', background: '#58A618' }}>
                    Category: {props.messageModel.category}, Upvotes: {props.messageModel.upvotes}, Downvotes: {props.messageModel.downvotes}
                </CardTitle>
                <CardText style={{ textAlign: 'left', minHeight: '100px' }}>
                    {props.messageModel.content}
                </CardText>
                <CardActions border style={{ background: '#FFFFFF' }}>
                    <ReactionText>
                        <div>
                            {props.reactions.filter(r => parseInt(r.messageId) === parseInt(props.messageModel.id)).map(reactionModel =>
                                <List>
                                    <ListItem threeLine style={{ background: "#FFFFFF", boxShadow: '0.5px 0.5px 0.5px 0.5px lightgrey', padding: '5px' }}>
                                        <ListItemContent avatar="person"></ListItemContent>
                                        <CardText style={{ textAlign: 'left' }}>
                                            {reactionModel.content}
                                        </CardText>
                                    </ListItem>
                                </List>
                            )}
                        </div>
                        <AddReaction
                            onReactionTextfieldChanged={(e) => props.onReactionTextfieldChanged(e, props.messageModel.id)}
                            reactionModelToAdd={props.reactionModelToAdd}
                            reactToComment={props.reactToComment}>
                        </AddReaction>
                    </ReactionText>

                    <div style={{ width: '100%' }}>
                        <FABButton style={{ float: 'left' }} raised ripple onClick={() => props.onClickUpvote(props.messageModel.id)} >
                            <Icon name="thumb_up_alt" />
                        </FABButton>
                        <FABButton style={{ float: 'right' }} raised ripple onClick={() => props.onClickDownvote(props.messageModel.id)}>
                            <Icon name="thumb_down_alt" />
                        </FABButton>
                    </div>
                </CardActions>
            </Card>
        </div>
    );
}

Message.PropTypes = {
    id: PropTypes.number.isRequired,
    content: PropTypes.string.isRequired,
    category: PropTypes.string.isRequired,
    upvotes: PropTypes.number,
    downvotes: PropTypes.number,
    onClickDownvote: PropTypes.func.isRequired,
    onClickUpvote: PropTypes.func.isRequired,
    messageId: PropTypes.number.isRequired,
    reactionContent: PropTypes.string.isRequired,
    reactToComment: PropTypes.func.isRequired,
    onReactionTextfieldChanged: PropTypes.func.isRequired,
    reactionModelToAdd: PropTypes.array.isRequired
}

export default Message;