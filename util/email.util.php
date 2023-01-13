<?php

function note_to_maillist($personID, $shiftType, $shiftNum, $nweek) {
    $db  =& Db::Instance();     
    $sql = 'SELECT a.InstitutionName AS instname, b.FirstName, b.LastName FROM `starweb`.`institutions` a, `starweb`.`members` b WHERE a.Id = b.InstitutionId and b.Id ='.intval($personID);
    $r = $db->Query($sql);
    send_email_to_maillist($personID, $r[0]['FirstName'].' '.$r[0]['LastName'], $r[0]['instname'], $shiftType, $shiftNum, $nweek);
}

function send_email_to_maillist($personID,$personName,$pInst,$shiftType,$shiftNum,$nweek)
{
  
    $cfg =& Config::Instance();                                                                                                                      

    $suppress = $cfg->Get('generic', 'suppress_emails');
    if (!empty($suppress)) {
		return;
    }

    $db  =& Db::Instance(); 
    $sql = 'SELECT a.CouncilRepId, a.Id as inst_id, b.EmailAddress FROM `starweb`.`institutions` a, `starweb`.`members` b WHERE a.CouncilRepId = b.Id AND a.InstitutionName = "'.$pInst.'"';
        
    $r = $db->Query($sql);
   
    $emailAddress = $r[0]['EmailAddress'];
   
    $person = $personName;
    $shift  = get_shift_type($shiftType);
    //$pInst = $r[0]['inst_id'];
    $slot = get_shift_hours($shiftNum);
    
    $tm = strtotime($cfg->Get('run','run_start_date'));
    $tm_st = $tm + $nweek * 7 * 24 * 3600;                                                                                                               
    $tm_en = $tm + ($nweek+1) * 7 * 24 * 3600;
    $nweek = date('M j', $tm_st).date('S', $tm_st).' - '.date('M j', $tm_en).date('S',$tm_en);

    $to = $cfg->Get('generic','email_admin').', '.$emailAddress.', '.$cfg->Get('generic', 'email_maillist');
    $from = $cfg->Get('generic', 'email_maillist');

    $subject = "Shift Removed for institution - $pInst";
    $message = "For your reference we are letting you know "
	     .  "as the $pInst institutional representative that the following shift slot " 
	     .  "$shift, for the week of $nweek, at the time $slot "
	     .  "which is assigned to your institution has been vacated by "
	     .  "$person and "
             .  "un-assigned on the date and time of this email.\n\n "
	     .  "The allowed time to un-assign a shift is " 
             .  "below the than 3 weeks prior to the previously assigned shift.  "
             .  "\n\n Please, note that, as discussed and agreed with the Council, the coverage "
             .  "of slots which are unassigned is the responsibility of the institution to which "
             .  "they were originally assigned. \n\n Please insure that the person who "
             .  "replaces the person originally signed up in the slot has the "
	     .	"necessary training from STAR and C-AD to accept the level of responsibility "
	     .	"required for this shift assignment. ";

    if (isset($from) and strlen($from)) {
		$additional = "From: " . $from;
    }
    mail($to,$subject,$message,$additional);
    //mail('arkhipkin@bnl.gov',$subject,$message,$additional);
};
