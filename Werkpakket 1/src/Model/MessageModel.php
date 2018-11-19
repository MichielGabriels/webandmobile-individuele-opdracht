<?php
namespace App\Model;

interface MessageModel
{
    function getAllMessages();
    function findMessage($content, $category);
    function getMessage($id);
    function addUpvote($id);
    function addDownvote($id);
}
