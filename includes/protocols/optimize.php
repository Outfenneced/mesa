<?php
if(isset($scrubbed["optimize"])) {
    $warnings[] = "This button has not been fully implemented yet.";
    $pkEventid = $scrubbed["pkEventid"];
    
    $q1 = "SELECT blCalendar FROM tblcalendars cals JOIN tblusers users ON users.pkUserid = cals.fkUserid WHERE cals.fkEventid = ?";
    
    $calendars = [];
    if($stmt = $dbc->prepare($q1)){
        $stmt->bind_param("i", $pkEventid);
        $stmt->execute();
        $stmt->bind_result($blCalendar);
        while($stmt->fetch()) {
            $calendars[] = $blCalendar;
        }
        $stmt->free_result();
        $stmt->close();
    }
    if(count($calendars) != 0) {
        
        $q2 = "SELECT dtStart, dtEnd, txLocation, blSettings, txRRule FROM tblevents WHERE pkEventid = ?";
        
        if($stmt = $dbc->prepare($q2)){
            $stmt->bind_param("i", $pkEventid);
            $stmt->execute();
            $stmt->bind_result($dtStart, $dtEnd, $txLocation, $blSettings, $txRRule);
            $stmt->free_result();
            $stmt->close();
        }
        
        $calendars = json_encode($calendars);
        exec("python \"".$homedir."python/OptCode.py\" \"$dtStart\" \"$dtEnd\" \"$txLocation\" $blSettings $txRRule $calendars", $output);
        $blOptiSuggestion = implode("\n",$output);
        
        var_dump($output);
//        $q3 = "UPDATE tblevents SET blOptiSuggestion = ? WHERE pkEventid = ?";
//        $q4 = "SELECT nmTitle FROM tblevents WHERE pkEventid = ?";
//        
//        if($stmt = $dbc->prepare($q3)){
//            $stmt->bind_param("si", $blOptiSuggestion,$pkEventid);
//            $stmt->execute();
//            $stmt->free_result();
//            $stmt->close();
//        }
//        if($stmt = $dbc->prepare($q4)){
//            $stmt->bind_param("i", $pkEventid);
//            $stmt->execute();
//            $stmt->bind_result($nmTitle);
//            $stmt->fetch();
//            $stmt->free_result();
//            $stmt->close();
//        }
//        $notifications[] = "Mesa has finished calculating optimal meeting times, return to the event page for <b><i>$nmTitle</i></b> to view suggestions.";
    } else {
        $warnings[] = "None of your attendees have responded to your calendar access request email yet. Please have at least one response before attempting to optimize event settings";
    }
}
?>