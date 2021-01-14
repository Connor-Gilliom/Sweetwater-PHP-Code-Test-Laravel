<?php

    //get all the order comments
    $results = DB::select('SELECT * FROM sweetwater_test');

    //arrays to store the different types of comments
    $candy_comments = array();
    $call_comments = array();
    $referred_comments = array();
    $signature_comments = array();
    $misc_comments = array();

    $num_results = count($results);

    //make sure data was recieved
    if($num_results > 0)
    {

        for($i = 0; $i < $num_results; $i++)
        {
            
            //convert the comment to lower case so caps don't mess up the search
            $search_comment = strtolower($results[$i]->comments);
            
            //check if the search comment contains one of our search strings if it does
            //push it to the corresponding array, if none of the searchs find a match 
            //put the comment in the miscellaneous array
            if(candy_filters($search_comment))
            {
                array_push($candy_comments, $results[$i]->comments);
            }
            else if(call_filters($search_comment))
            {
                array_push($call_comments, $results[$i]->comments);
            }
            else if(referred_filters($search_comment))
            {
                array_push($referred_comments, $results[$i]->comments);
            }
            else if(signature_filters($search_comment))
            {
                array_push($signature_comments, $results[$i]->comments);
            }
            else
            {
                array_push($misc_comments, $results[$i]->comments);
            }

            $date_search_flag = "expected ship date: ";
        
            //add the date to the db if a comment contains one
            if(str_contains($search_comment, $date_search_flag))
            {

                //get the index of the date by finding the position of the search flag and then adding the length of the flag
                $date_index = strpos($search_comment, $date_search_flag) + strlen($date_search_flag);

                //get the 8 chars of the date
                $date = substr($search_comment, $date_index, 8);
                
                //create a date time from the string so we can convert to the format we want
                $date_time = new DateTime($date);
                
                //get the date format Y-m-d as a string
                $date = $date_time->format("Y-m-d");
                
                //update the order with the date we found
                DB::update("UPDATE sweetwater_test SET shipdate_expected=? WHERE orderid=?", array($date, $results[$i]->orderid));
            }
        }

        //send all the comment data to be outputed
        output_comments($candy_comments, $call_comments, $referred_comments, $signature_comments, $misc_comments);

    }

    //applys the filters to find a call comment and returns true/false based on if this is a call comment
    function call_filters($comment)
    {
        //first level check
        if(str_contains($comment, "call") || str_contains($comment, "calls"))
        {
            
            if(
                //look for ways the word call is used with words around it
                str_contains($comment, "call me") ||
                str_contains($comment, "please call") ||
                str_contains($comment, "plz call") ||

                str_contains($comment, "phone call") ||
                str_contains($comment, "phone calls") ||

                str_contains($comment, "do not call") ||
                str_contains($comment, "don't call") ||
                str_contains($comment, "no calls") ||
                
                str_contains($comment, "call if") ||
                str_contains($comment, "try to call") ||

                //words that are used together but not next together
                (str_contains($comment, "answer") && str_contains($comment, "calls")) ||
                (str_contains($comment, "answer") && str_contains($comment, "call")) ||
                (str_contains($comment, "question") && str_contains($comment, "call")) ||
                (str_contains($comment, "question") && str_contains($comment, "calls"))
            ){
                return true;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }

    }

    function candy_filters($comment)
    {
        //check for the base word of candy as well as candy types
        if(str_contains($comment, "candy"))
        {
            return true;
        }
        else if(str_contains($comment, "smarties"))
        {
            return true;
        }
        else if(str_contains($comment, "bit o honey"))
        {
            return true;
        }
        else if(str_contains($comment, "mints"))
        {
            return true;
        }
        else if(str_contains($comment, "mint"))
        {
            return true;
        }
        else if(str_contains($comment, "cinnamon"))
        {
            return true;
        }
        else if(str_contains($comment, "tootsie rolls"))
        {
            return true;
        }
        else if(str_contains($comment, "taffy"))
        {
            return true;
        }
 
        return false;
        
    }

    function signature_filters($comment)
    {
        //check if the comment contains words denoting signing or signatures
        if(str_contains($comment, "signature") || str_contains($comment, "sign"))
        {
            return true;
        }

        return false;
    }

    function referred_filters($comment)
    {
        //check for different wordings of referrals
        if(str_contains($comment, "referred"))
        {
            return true;
        }
        else if(str_contains($comment, "referral"))
        {
            return true;
        }
        else if(str_contains($comment, "told me about"))
        {
            return true;
        }
        else if(str_contains($comment, "heard"))
        {
            return true;
        }
        
        return false;

    }

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