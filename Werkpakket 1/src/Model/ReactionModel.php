<?php

namespace App\Model;

interface ReactionModel
{
    function getAllReactions();
    function postReactionByMessageId($id, $content);
}
