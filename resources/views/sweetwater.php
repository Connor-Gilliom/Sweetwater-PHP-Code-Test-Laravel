<?php

    //ouput the comments set by the controller
    output_comments($candy_comments, $call_comments, $referred_comments, $signature_comments, $misc_comments);

    //takes all the comment sets and outputs them as html
    function output_comments($candy_comments, $call_comments, $referred_comments, $signature_comments, $misc_comments)
    {
        comment_formater($candy_comments, "Candy Comments");
        comment_formater($call_comments, "Call Comments");
        comment_formater($referred_comments, "Referred Comments");
        comment_formater($signature_comments, "Signature Comments");
        comment_formater($misc_comments, "Miscellaneous Comments");
        echo "<hr>";
    }

    //takes a set of comments and formats them into an html list using a provided name as the section title
    function comment_formater($comments, $readable_name){
        echo "<hr><h3>" . $readable_name . "</h3>";
        echo "<ul>";
            foreach($comments as $comment)
            {
                echo "<li>" . $comment . "</li>";
            }
        echo "</ul>";
    }

?>