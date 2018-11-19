import React from 'react';
import PropTypes from "prop-types";
import { Textfield, Button } from 'react-mdl';

const AddReaction = (props) => {
    const {
        onReactionTextfieldChanged,
        reactionModelToAdd,
        reactToComment,
        formId
    } = props;

    return (
        <div>
            <form id={"form" + formId}>
                <Textfield
                    id={reactionModelToAdd.messageId}
                    label='Place a reaction...'
                    style={{ width: '500px' }}
                    defaultValue={reactionModelToAdd.content}
                    onChange={onReactionTextfieldChanged}
                    rows={5}
                    maxLength='254'
                />
                <div>
                    <Button onClick={(e) => {reactToComment(e, formId)}}>
                        Add reaction
                    </Button>
                </div>
            </form>
        </div>

    );
}

AddReaction.defaultProps = {
    reactionModelToAdd: { messageId: 0, content: '' }
}

AddReaction.propTypes = {
    onReactionTextfieldChanged: PropTypes.func.isRequired,
    reactionModelToAdd: PropTypes.object.isRequired,
    reactToComment: PropTypes.func.isRequired
}

export default AddReaction;